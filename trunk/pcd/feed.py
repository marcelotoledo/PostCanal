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
from monitor    import Monitor

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
        l.log("webservice call failed; (%s)" % (sys.exc_info()[1]), funcName())
        return None
    
    if type(feedList) != type(list()):
        l.log("wrong type, expected <list>", funcName())
        return None
    
    if len(feedList) == 0:
        l.log("No feeds to update", funcName())
        return None
    
    return feedList

def processFeed(url, token, requestQueue, name, isMonitor=False):
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
            feed = requestQueue.get(timeout=60)
        except:
            l.log('Queue timeout, ending thread', name, monName, 'copy-string', mon)
            return None

        if feed == 'kill':
            l.log("I am done, ending thread", name, monName, 'copy-string', mon)
            if isMonitor: mon.delKey(monName)
            return None
        
        if type(feed) != type(dict()):
            l.log("Feed type is wrong, expected <dict>", name, monName, 'copy-string', mon)
            continue

        try:
            id  = int(feed['id'])
            url = str(feed['feed_url'])
        except:
            l.log("Invalid feed dictionary", name, monName, 'copy-string', mon)
            continue

        l.log("Updating %s" % (url), name, monName, 'copy-string', mon)
        
        dump = feed_dump(get_feed(url))    
        if type(dump) != type(dict()):
            l.log("Wrong type for feed dump", name, monName, 'copy-string', mon)
            continue
    
        status         = ""
        totalArticles  = 0
        saved          = 0
        
        try:
            status        = dump['feed_status']
            totalArticles = len(dump['articles'])
        except:
            l.log("invalid feed dump dictionary, probably not parsed", name, monName, 'copy-string', mon)
            continue

        l.log("%s has %d entries" % (url, totalArticles), name, monName, 'copy-string', mon)
            
        try:
            saved = client.feed_update_post({ 'token' : token, 
                                              'id'    : id, 
                                              'data'  : dump })
        except:
            l.log("Webservice call failed; (%s)" % (sys.exc_info()[1]), name, monName, 'copy-string', mon)
            continue
        
        if type(saved) != type(int()):
            saved = 0

        l.log("Feed %d saved %d articles" % (id, saved), name, monName, 'copy-string', mon)
        
        time.sleep(1)

def pendingFeeds(client, token):
    try:
        feedCount = client.feed_update_total({ 'token': token })
    except:
        l.log("webservice call failed; (%s)" % (sys.exc_info()[1]), funcName())
        feedCount = 0

    return feedCount

def feedScheduleAll(client, token):
    try:
        client.feed_update_reset({ 'token': token })
    except:
        l.log("webservice call failed; (%s)" % (sys.exc_info()[1]), funcName())
        return None
    
class FeedThread(threading.Thread):
    def __init__(self, url, token, requestQueue, id, isMonitor):
        threading.Thread.__init__(self, name="feed%02d" % (id,))
        self.requestQueue = requestQueue
        self.url          = url
        self.token        = token
        self.id           = id
        self.isMonitor    = isMonitor
        
    def run(self):
        processFeed(self.url, self.token, self.requestQueue, self.name, self.isMonitor)
