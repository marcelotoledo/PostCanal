# post.py --- Module for processing posts

# Copyright  (C)  2009  Postcanal Inc. <http://www.postcanal.com>

# Version: 1.0
# Keywords: 
# Author: Marcelo Toledo <marcelo.toledo@postcanal.com>
#         Rafael Castilho <rafael.castilho@postcanal.com>
# Maintainer: Marcelo Toledo <marcelo.toledo@postcanal.com>
# URL: http://

# Commentary: 



# Code:

from utils   import funcName
from iface   import openConnection
from monitor import Monitor

import log
import sys
import threading
import xmlrpclib
import time

l = log.Log()

def getNextPost(client, token, total=1):
    try:
        publishList = client.blog_publish_get({ 'token' : token,
                                                'total' : total })
    except:
        l.log("Webservice call failed; (%s)" %
              (sys.exc_info()[0].__name__), funcName())
        return None

    if len(publishList) == 0:
        l.log("No items to publish", funcName())
        return None
    
    return publishList

def pendingPosts(client, token):
    try:
        postCount = client.blog_publish_total({ 'token': token })
    except:
        l.log("webservice call failed; (%s)" % (sys.exc_info()[0].__name__), funcName())
        postCount = 0

    return postCount

def postScheduleAll(client, token):
    try:
        client.blog_publish_reset({ 'token': token })
    except:
        l.log("webservice call failed; (%s)" % (sys.exc_info()[0].__name__), funcName())
        return None

def processPost(url, token, requestQueue, name, module, isMonitor=False):
    mon = None
    if isMonitor: mon = Monitor()
    
    monName = 'thread-' + name
    
    try:
        l.log('Opening connection with interface %s' % url, name, monName, 'copy-string', mon)
        client = openConnection(url)
    except:
        l.log("Error opening connection with interface - %s" % (sys.exc_info()[1]), name, monName, 'copy-string', mon)
        if isMonitor: mon.delKey(monName)
        return None

    while 1:
        l.log('Waiting for next in the queue to arrive', name, monName, 'copy-string', mon)

        try:
            post = requestQueue.get(timeout=60)
        except:
            l.log('Queue timeout, ending thread', name, monName, 'copy-string', mon)
            return None

        if post == 'kill':
            l.log("I am done, ending thread", name, monName, 'copy-string', mon)
            if isMonitor: mon.delKey(monName)
            return None        
    
        if type(post) != type(dict()):
            l.log("Feed type is wrong, expected <dict>", name, monName, 'copy-string', mon)            
            continue

        try:
            id            = post['id']
            blog_type     = post['blog_type']
            blog_version  = post['blog_version']
            manager_url   = post['blog_manager_url']
            blog_username = post['blog_username']
            blog_password = post['blog_password']
            entry_title   = post['entry_title']
            entry_content = post['entry_content']
            article_link  = post['article_link']
        except:
            l.log("Invalid post dictionary", name, monName, 'copy-string', mon)
            continue

        l.log("Well publish using: %s (%s) - %s:%s" % (blog_type, manager_url, blog_username, "*"*len(blog_password)), name, monName, 'copy-string', mon)

        dynClass = module.myClassByName(blog_type, manager_url, blog_username, blog_password)
        dynClass.setLogBanner(name)
        if dynClass == None:
            l.log("Blog not supported", name, monName, 'copy-string', mon)
            continue

        l.log("Blog is supported! My name from the dynamic class is %s" % dynClass.modName, name, monName, 'copy-string', mon)

        if dynClass.authenticate() == False:
            l.log("Unable to authenticate", name, monName, 'copy-string', mon)
            continue

        l.log("Authenticated!", name, monName, 'copy-string', mon)

        l.log("Preparing to publish %s" % (id), name, monName, 'copy-string', mon)

        message = ""
        
        try:
            dynClass.setArticleLink(article_link)
            dynClass.setTitle(entry_title)
            dynClass.setContent(entry_content)
            if dynClass.postEntry() == False:
                l.log("Error postEntry returned false for %s" % (id), name, monName, 'copy-string', mon)
                continue
        except xmlrpclib.Fault, message:
            l.log("(2) Failed to publish (%s) - (%s)" % (id, sys.exc_info()[1]), name, monName, 'copy-string', mon)
        except:
            l.log("(3) Failed to publish (%s) - (%s)" % (id, sys.exc_info()[1]), name, monName, 'copy-string', mon)

        l.log("Entry %s published" % (id), name, monName, 'copy-string', mon)

        try:
            client.blog_publish_set({ 'token'     : token,
                                      'id'        : id,
                                      'published' : True,
                                      'message'   : message })
        except:
            l.log("Failed to set published for %s - %s" % (id, sys.exc_info()[1]), name, monName, 'copy-string', mon)
            continue

        l.log("Entry %s marked as published" % (id), name, monName, 'copy-string', mon)

        time.sleep(1)

class PostThread(threading.Thread):
    def __init__(self, url, token, requestQueue, id, module, isMonitor):
        threading.Thread.__init__(self, name="post%02d" % (id,))
        self.requestQueue = requestQueue
        self.url          = url
        self.token        = token
        self.id           = id
        self.module       = module
        self.isMonitor    = isMonitor
      
    def run(self):
        processPost(self.url, self.token, self.requestQueue, self.name, self.module, self.isMonitor)
