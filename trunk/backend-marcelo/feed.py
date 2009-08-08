# feed.py --- short description

# Copyright  (C)  2009  Postcanal Inc. <http://www.postcanal.com>

# Version: 1.0
# Keywords: 
# Author: Rafael Castilho <rafael.castilho@postcanal.com>
#         Marcelo Toledo <marcelo.toledo@postcanal.com>
# Maintainer: Marcelo Toledo <marcelo@marcelotoledo.org>
# URL: http://

# Commentary: 



# Code:

from aggregator import get_feed, feed_dump
from utils      import funcName

import log
import sys
import multitask

l = log.Log()

# class Feed():
#     def __init__(self, client, token):
#         self.client        = client
#         self.token         = token
#         self.feedList      = None
#         self.feed          = None
#         self.dump          = None
#         self.url           = None
#         self.id            = None
#         self.status        = None
#         self.totalArticles = None
#         self.saved         = None

#     def getNextFeed(self):
#         try:
#             self.feedList = self.client.feed_update_get({ 'token': self.token })
#         except:
#             l.log("webservice call failed; (%s)" % (sys.exc_info()[0].__name__), funcName())
#             return False

#         if type(self.feedList) != type(list()):
#             l.log("wrong type, expected <list>", funcName())
#             return False
    
#         if len(self.feedList) == 0:
#             l.log("No feeds to update", funcName())
#             return False

#         try:
#             self.feed = self.feedList.pop()
#         except:
#             return False

#         return True

#     def processFeed(self):
#         if type(self.feed) != type(dict()):
#             l.log("Feed type is wrong, expected <dict>", funcName())
#             return None
        
#         try:
#             self.id  = int(self.feed['id'])
#             self.url = str(self.feed['feed_url'])
#         except:
#             l.log("Invalid feed dictionary", funcName())
#             return None
        
#         l.log("Updating %s" % (self.url), funcName())
        
#         self.dump = feed_dump(get_feed(self.url))
        
#         if type(self.dump) != type(dict()):
#             l.log("Wrong type for feed dump", funcName())
#             return None
        
#         self.status         = ""
#         self.total_articles = 0
#         self.saved          = 0
        
#         try:
#             self.status        = self.dump['feed_status']
#             self.totalArticles = len(self.dump['articles'])
#         except:
#             l.log("invalid feed dump dictionary, probably not parsed", funcName())
            
#         l.log("%s has %d entries" %
#               (self.url, self.totalArticles), funcName())
            
#         try:
#             self.saved = self.client.feed_update_post({ 'token' : self.token, 
#                                                         'id'    : self.id, 
#                                                         'data'  : self.dump })
#         except:
#             l.log("Webservice call failed; (%s)" %
#                   (sys.exc_info()[0].__name__), funcName())
            
#         if type(self.saved) != type(int()): self.saved = 0
            
#         l.log("Feed %d saved %d articles" % (self.id, self.saved), funcName())

# def feedUpdate(client, token):
#     f = Feed(client, token)
    
#     res = f.getNextFeed()
#     if res == True:
        
#         f.processFeed()

def getNextFeed(client, token, lock):
    try:
        lock.acquire()
        feedList = client.feed_update_get({ 'token': token })
        lock.release()
    except:
        l.log("webservice call failed; (%s)" % (sys.exc_info()[0].__name__), funcName())
        return None
    
    if type(feedList) != type(list()):
        l.log("wrong type, expected <list>", funcName())
        return None
    
    if len(feedList) == 0:
        l.log("No feeds to update", funcName())
        return None
    
    try:
        feed = feedList.pop()
    except:
        return None

    return feed

def processFeed(client, token, feed, lock):
    if type(feed) != type(dict()):
        l.log("Feed type is wrong, expected <dict>", funcName())
        return None
        
    try:
        id  = int(feed['id'])
        url = str(feed['feed_url'])
    except:
        l.log("Invalid feed dictionary", funcName())
        return None
        
    l.log("Updating %s" % (url), funcName())
    
    dump = feed_dump(get_feed(url))
    
    if type(dump) != type(dict()):
        l.log("Wrong type for feed dump", funcName())
        return None
    
    status         = ""
    totalArticles  = 0
    saved          = 0
        
    try:
        status        = dump['feed_status']
        totalArticles = len(dump['articles'])
    except:
        l.log("invalid feed dump dictionary, probably not parsed", funcName())
            
    l.log("%s has %d entries" %
          (url, totalArticles), funcName())
            
    try:
        lock.acquire()
        saved = client.feed_update_post({ 'token' : token, 
                                                    'id'    : id, 
                                                    'data'  : dump })
        lock.release()
    except:
        l.log("Webservice call failed; (%s)" %
              (sys.exc_info()[0].__name__), funcName())
        
    if type(saved) != type(int()): saved = 0
            
    l.log("Feed %d saved %d articles" % (id, saved), funcName())
    
def feedUpdate(client, token, lock):
    feed = getNextFeed(client, token, lock)
    if feed != None:
        processFeed(client, token, feed, lock)
