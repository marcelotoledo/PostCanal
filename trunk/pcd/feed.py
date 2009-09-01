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
    mon = Monitor()

    monName = 'thread-' + name
    name = "-" + name
    
    try:
        mon.setStatus(monName, 'Opening connection with interface %s' % url)
        client = openConnection(url)
    except:
        logmsg = "Error opening connection with interface - %s" % (sys.exc_info()[0].__name__)
        mon.setStatus(monName, logmsg)
        l.log(logmsg, funcName() + name)
        return None
    
    while 1:
        mon.setStatus(monName, 'Waiting for next in the queue to arrive')
        feed = requestQueue.get()

        if feed == 'kill':
            logmsg = "I am done, ending thread"
            mon.setStatus(monName, logmsg)
            l.log(logmsg, funcName() + name)
            mon.delKey(monName)
            return None
        
        if type(feed) != type(dict()):
            logmsg = "Feed type is wrong, expected <dict>"
            mon.setStatus(monName, logmsg)
            l.log(logmsg, funcName() + name)
            continue

        try:
            id  = int(feed['id'])
            url = str(feed['feed_url'])
        except:
            logmsg = "Invalid feed dictionary"
            mon.setStatus(monName, logmsg)
            l.log(logmsg, funcName() + name)
            continue

        logmsg = "Updating %s" % (url)
        mon.setStatus(monName, logmsg)
        l.log(logmsg, funcName() + name)
    
        dump = feed_dump(get_feed(url))
    
        if type(dump) != type(dict()):
            logmsg = "Wrong type for feed dump"
            mon.setStatus(monName, logmsg)
            l.log(logmsg, funcName() + name)
            continue
    
        status         = ""
        totalArticles  = 0
        saved          = 0
        
        try:
            status        = dump['feed_status']
            totalArticles = len(dump['articles'])
        except:
            logmsg = "invalid feed dump dictionary, probably not parsed"
            mon.setStatus(monName, logmsg)
            l.log(logmsg, funcName() + name)
            continue

        logmsg = "%s has %d entries" % (url, totalArticles)
        mon.setStatus(monName, logmsg)
        l.log(logmsg, funcName() + name)
            
        try:
            saved = client.feed_update_post({ 'token' : token, 
                                              'id'    : id, 
                                              'data'  : dump })
        except:
            logmsg = "Webservice call failed; (%s)" % (sys.exc_info()[1])
            mon.setStatus(monName, logmsg)
            l.log(logmsg, funcName() + name)
            continue
        
        if type(saved) != type(int()):
            saved = 0

        logmsg = "Feed %d saved %d articles" % (id, saved)
        mon.setStatus(monName, logmsg)
        l.log(logmsg, funcName() + name)
        
        time.sleep(1)

def pendingFeeds(client, token):
    try:
        feedCount = client.feed_update_total({ 'token': token })
    except:
        l.log("webservice call failed; (%s)" % (sys.exc_info()[0].__name__), funcName())
        feedCount = 0

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
