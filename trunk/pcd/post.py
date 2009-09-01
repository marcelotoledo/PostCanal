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

def processPost(url, token, requestQueue, name, module):
    mon = Monitor()
    
    monName = 'thread-' + name    
    name = "-" + name
    
    try:
        mon.setStatus(monName, 'Opening connection with interface %s' % url)        
        client = openConnection(url)
    except:
        logmsg = "Error opening connection with interface - %s" % (sys.exc_info()[1])
        mon.setStatus(monName, 'Opening connection with interface %s' % url)
        l.log(logmsg, funcName() + name)
        return None

    while 1:
        mon.setStatus(monName, 'Waiting for next in the queue to arrive')
        post = requestQueue.get()

        if post == 'kill':
            logmsg = "I am done, ending thread"
            mon.setStatus(monName, logmsg)
            l.log(logmsg, funcName() + name)
            mon.delKey(monName)
            return None        
    
        if type(post) != type(dict()):
            logmsg = "Feed type is wrong, expected <dict>"
            mon.setStatus(monName, logmsg)
            l.log(logmsg, funcName() + name)            
            return None

        try:
            id            = post['id']
            blog_type     = post['blog_type']
            blog_version  = post['blog_version']
            manager_url   = post['blog_manager_url']
            blog_username = post['blog_username']
            blog_password = post['blog_password']
            entry_title   = post['entry_title']
            entry_content = post['entry_content']
        except:
            logmsg = "Invalid post dictionary (%s)" % (sys.exc_info()[1])
            mon.setStatus(monName, logmsg)
            l.log(logmsg, funcName() + name)

        logmsg = "We'll publish using: %s (%s) - %s:%s" % (blog_type, manager_url, blog_username, blog_password)
        mon.setStatus(monName, logmsg)
        l.log(logmsg,  funcName() + name)

        dynClass = module.myClassByName(blog_type, manager_url, blog_username, blog_password)
        if dynClass == None:
            logmsg = "Blog not supported"
            mon.setStatus(monName, logmsg)
            l.log(logmsg, funcName() + name)
            return None

        logmsg = "Blog is supported! My name from the dynamic class is %s" % dynClass.modName
        mon.setStatus(monName, logmsg)
        l.log(logmsg, funcName() + name)

        if dynClass.authenticate() == False:
            logmsg = "Unable to authenticate"
            mon.setStatus(monName, logmsg)
            l.log(logmsg, funcName() + name)
            return None

        logmsg = "Authenticated!"
        mon.setStatus(monName, logmsg)
        l.log(logmsg, funcName() + name)

        logmsg = "Preparing to publish %s" % (id)
        mon.setStatus(monName, logmsg)
        l.log(logmsg, funcName() + name)

        published = False
        message = ""
        
        try:
            dynClass.setTitle(entry_title)
            dynClass.setContent(entry_content)
            if dynClass.postEntry() == False:
                logmsg = "Error postEntry returned false for %s" % (id)
                mon.setStatus(monName, logmsg)
                l.log(logmsg, funcName() + name)
                return None

            logmsg = "Entry %s '%s' published as %s" % (id, entry_title, str(post_id))
            mon.setStatus(monName, logmsg)
            l.log(logmsg, funcName() + name)
            published = True
        except xmlrpclib.Fault, message:
            logmsg = "(2) Failed to publish (%s) - (%s)" % (id, sys.exc_info()[1])
            mon.setStatus(monName, logmsg)            
            l.log(logmsg, funcName() + name)
        except:
            logmsg = "(3) Failed to publish (%s) - (%s)" % (id, sys.exc_info()[1])
            mon.setStatus(monName, logmsg)            
            l.log(logmsg, funcName() + name)

        try:
            client.blog_publish_set({ 'token'     : token,
                                      'id'        : id,
                                      'published' : published,
                                      'message'   : message })
        except:
            logmsg = "Failed to set published for %s - %s" % (id, sys.exc_info()[1])
            mon.setStatus(monName, logmsg)
            l.log(logmsg, funcName() + name)
            return None

class PostThread(threading.Thread):
    def __init__(self, url, token, requestQueue, id, module):
        threading.Thread.__init__(self, name="post%02d" % (id,))
        self.requestQueue = requestQueue
        self.url          = url
        self.token        = token
        self.id           = id
        self.module       = module
      
    def run(self):
        processPost(self.url, self.token, self.requestQueue, self.name, self.module)
