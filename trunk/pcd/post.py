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

from utils import funcName
from iface import openConnection

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
    name = "-" + name
    
    try:
        client = openConnection(url)
    except:
        l.log("Error opening connection with interface - %s" % (sys.exc_info()[0].__name__), funcName() + name)
        return None

    while 1:
        post = requestQueue.get()

        if post == 'kill':
            l.log("I am done, ending thread", funcName() + name)
            return None        
    
        if type(post) != type(dict()):
            l.log("Wrong type, expected <dict>", funcName() + name)
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
            l.log("Invalid post dictionary (%s)" %
                  (sys.exc_info()[0].__name__), funcName() + name)

        l.log("We'll publish using: %s (%s) - %s:%s" % (blog_type, manager_url, blog_username, blog_password))

        dynClass = module.myClassByName(blog_type, manager_url, blog_username, blog_password)
        if dynClass == None:
            l.log("Blog not supported", funcName() + name)
            return None

        l.log("Blog is supported! My name from the dynamic class is %s" % dynClass.modName)

        if dynClass.authenticate() == False:
            l.log("Unable to authenticate", funcName() + name)
            return None

        l.log("Authenticated!")
            
        #t = init_type(blog_type, blog_version)

        #if t == None:
        #    l.log("Unknown blog type", funcName() + name)
        #    return None

        #t.set_manager_url(manager_url)
        #t.username = blog_username
        #t.password = blog_password

        l.log("Preparing to publish %s" % (id), funcName() + name)

        published = False
        message = ""
        
        try:
            dynClass.setTitle(entry_title)
            dynClass.setContent(entry_content)
            if dynClass.postEntry() == False:
                l.log("1Failed to publish (%s) - (%s)" % (id, message), funcName() + name)
                return None
            
            #l.log("Entry %s '%s' published as %s" % (id, entry_title, str(post_id)), funcName() + name)
            
            #post_id = t.publish({ 'title'  : entry_title,
            #                      'content': entry_content })
            #l.log("Entry %s '%s' published as %s" % (id, entry_title, str(post_id)), funcName() + name)
            published = True
        except xmlrpclib.Fault, message:
            l.log("2Failed to publish (%s) - (%s)" % (id, sys.exc_info()[1]), funcName() + name)
        except:
            l.log("3Failed to publish (%s) - (%s)" % (id, sys.exc_info()[1]), funcName() + name)

        try:
            client.blog_publish_set({ 'token'     : token,
                                      'id'        : id,
                                      'published' : published,
                                      'message'   : message })
        except:
            l.log("Failed to set published for %s - %s" % (id, sys.exc_info()[1]), funcName() + name)
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
