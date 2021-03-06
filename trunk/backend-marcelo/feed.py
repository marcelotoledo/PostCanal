# feed.py --- Module for updating feeds

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
from iface      import openConnection

import log
import sys
import threading
import time

l = log.Log()

def getNextFeed(client, token, total=1):
    try:
        feedList = client.feed_update_get({ 'token' : token,
                                            'total' : total })
    except:
        l.log("webservice call failed; (%s)" % (sys.exc_info()[0].__name__), funcName())
        return None
    
    if type(feedList) != type(list()):
        l.log("wrong type, expected <list>", funcName())
        return None
    
    if len(feedList) == 0:
        l.log("No feeds to update", funcName())
        return None
    
    return feedList

def processFeed(url, token, requestQueue, name):
    name = "-" + name
    
    try:
        client = openConnection(url)
    except:
        l.log("Error opening connection with interface - %s" % (sys.exc_info()[0].__name__), funcName() + name)
        return None
    
    while 1:
        feed = requestQueue.get()

        if feed == 'kill':
            l.log("I am done, ending thread", funcName() + name)
            return None
        
        if type(feed) != type(dict()):
            l.log("Feed type is wrong, expected <dict>", funcName() + name)
            continue

        try:
            id  = int(feed['id'])
            url = str(feed['feed_url'])
        except:
            l.log("Invalid feed dictionary", funcName() + name)
            continue
        
        l.log("Updating %s" % (url), funcName() + name)
    
        dump = feed_dump(get_feed(url))
    
        if type(dump) != type(dict()):
            l.log("Wrong type for feed dump", funcName() + name)
            continue
    
        status         = ""
        totalArticles  = 0
        saved          = 0
        
        try:
            status        = dump['feed_status']
            totalArticles = len(dump['articles'])
        except:
            l.log("invalid feed dump dictionary, probably not parsed", funcName() + name)
            continue
            
        l.log("%s has %d entries" %
              (url, totalArticles), funcName() + name)
            
        try:
            saved = client.feed_update_post({ 'token' : token, 
                                              'id'    : id, 
                                              'data'  : dump })
        except:
            l.log("Webservice call failed; (%s)" %
                  (sys.exc_info()[0].__name__), funcName() + name)
            continue
        
        if type(saved) != type(int()): saved = 0
        
        l.log("Feed %d saved %d articles" % (id, saved), funcName() + name)
        
        time.sleep(1)

def pendingFeeds(client, token):
    try:
        feedCount = client.feed_update_total({ 'token': token })
    except:
        l.log("webservice call failed; (%s)" % (sys.exc_info()[0].__name__), funcName())
        return None

    return feedCount

def feedScheduleAll(client, token):
    try:
        client.feed_update_reset({ 'token': token })
    except:
        l.log("webservice call failed; (%s)" % (sys.exc_info()[0].__name__), funcName())
        return None
    
class FeedThread(threading.Thread):
    def __init__(self, url, token, requestQueue, id):
        threading.Thread.__init__(self, name="feed%02d" % (id,))
        self.requestQueue = requestQueue
        self.url          = url
        self.token        = token
        self.id           = id
        
    def run(self):
        processFeed(self.url, self.token, self.requestQueue, self.name)
