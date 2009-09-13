#!/usr/bin/env python
#
# pcd.py ---  postcanal daemon

# Copyright  (C)  2009  Postcanal Inc. <http://www.postcanal.com>

# Version: 1.0
# Keywords: 
# Author: Marcelo Toledo <marcelo.toledo@postcanal.com>
#         Rafael Castilho <rafael.castilho@postcanal.com>
# Maintainer: Marcelo Toledo <marcelo@marcelotoledo.org>
# URL: http://

# Commentary: 



# Code:

import sys
from utils     import * 

pcdDir = getDirectory()
if pcdDir == None:
    print "Error - Environment variable PCD_DIR not set. Exiting..."
    sys.exit(1)
    
setPath(pcdDir)

from conf      import *
from feed      import getNextFeed, pendingFeeds, feedScheduleAll, FeedThread
from post      import getNextPost, pendingPosts, postScheduleAll, PostThread
from autoQueue import autoQueue
from iface     import openConnection
from module    import *
from monitor   import Monitor

import os
import time
import log
import threading
import Queue
import codecs

sys.stdout = codecs.getwriter('utf-8')(sys.stdout)

if __name__ == "__main__":
    u = Usage()
    u.usage()
    l = log.Log(u.options.verbose, u.options.debug, u.options.monitor)    
    u.banner()
    
    m   = Module(pcdDir)

    r = runtimeConfig(pcdDir)
    r.addOption("Debug",      str(u.options.debug))
    r.addOption("Verbose",    str(u.options.verbose))
    r.addOption("Monitor",    str(u.options.monitor))
    r.addOption("Directory",  pcdDir)
    r.addOption("token",      r.token)
    r.addOption("Frontend",   r.frontend)
    r.addOption("FrontendWS", r.frontendWS)

    for k, v in m.availableModules().items():
        m.loadModule(k, v)
        r.addOption(k, v)

    r.printOptions()

    mon = None
    if u.options.monitor:
        mon = Monitor()    

#    feedScheduleAll(r.client, r.token)
#    postScheduleAll(r.client, r.token)

    MAX_THREADS     = 20
    MIN_THREADS     = 3
    THREADS_RATIO   = 3

    feedQueue = Queue.Queue()
    postQueue = Queue.Queue()

    currentThreadId = 0
    
    while True:
        feedCount = pendingFeeds(r.client, r.token)
        feedList  = getNextFeed(r.client, r.token, feedCount)
        if feedCount > 0:
            addToQueue(feedQueue, feedList)
            l.log("Queued %d feed(s)" % int(feedCount))
            newFeedThreads = newThreads(feedQueue.qsize(), tCount(threading.enumerate(), "feed"),
                                        THREADS_RATIO, MAX_THREADS, MIN_THREADS)            
            currentThreadId = processThreads(newFeedThreads, FeedThread, r.frontendWS, r.token, feedQueue, currentThreadId, None, u.options.monitor)

        l.emptyLine()
        feedThreadSize = tCount(threading.enumerate(), "feed")
        l.debug("Feed queue size     = %d" % feedQueue.qsize(), None, 'feed_queue_size',     feedQueue.qsize(), mon)
        l.debug("Feed active threads = %d" % feedThreadSize,    None, 'feed_active_threads', feedThreadSize,    mon)
        l.debug("Feed new threads    = %d" % newFeedThreads,    None, 'feed_new_threads',    newFeedThreads,    mon)

        l.emptyLine()

        postCount = pendingPosts(r.client, r.token)
        postList  = getNextPost(r.client, r.token, postCount)
        if feedCount > 0:
            addToQueue(postQueue, postList)
            l.log("Queued %d post(s)" % int(postCount))
            newPostThreads = newThreads(postQueue.qsize(), tCount(threading.enumerate(), "post"),
                                        THREADS_RATIO, MAX_THREADS, MIN_THREADS)
            currentThreadId = processThreads(newPostThreads, PostThread, r.frontendWS, r.token, postQueue, currentThreadId, m, u.options.monitor)

        l.emptyLine()
        postThreadSize = tCount(threading.enumerate(), "post")
        l.debug("Post queue size     = %d" % postQueue.qsize(), None, 'post_queue_size',     postQueue.qsize(), mon)
        l.debug("Post active threads = %d" % postThreadSize,    None, 'post_active_threads', postThreadSize,    mon)
        l.debug("Post new threads    = %d" % newPostThreads,    None, 'post_new_threads',    newPostThreads,    mon)
        l.emptyLine()

        time.sleep(1)
