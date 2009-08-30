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

from conf      import runtimeConfig
from utils     import Usage, tCount, addToQueue, newThreads, processThreads
from feed      import getNextFeed, pendingFeeds, feedScheduleAll, FeedThread
from post      import getNextPost, pendingPosts, postScheduleAll, PostThread
from autoQueue import autoQueue
from iface     import openConnection
from module    import *

import sys
import time
import log
import threading
import Queue

if __name__ == "__main__":
    u = Usage()
    m = Module()
    
    u.banner()
    u.usage()

    l = log.Log(u.options.verbose, u.options.debug)

    r = runtimeConfig()
    r.addOption("Debug",      str(u.options.debug))
    r.addOption("Verbose",    str(u.options.verbose))
    r.addOption("token",      r.token)
    r.addOption("Frontend",   r.frontend)
    r.addOption("FrontendWS", r.frontendWS)

    for k, v in m.availableModules().items():
        m.loadModule(k, v)
        r.addOption(k, v)

    r.printOptions()        

    feedScheduleAll(r.client, r.token)
    postScheduleAll(r.client, r.token)

    MAX_THREADS     = 20
    MIN_THREADS     = 3
    THREADS_RATIO   = 3

#    MAX_THREADS     = 1
#    MIN_THREADS     = 1
#    THREADS_RATIO   = 1    
    
    feedQueue     = Queue.Queue()
    postQueue     = Queue.Queue()

    currentThreadId = 0    

    while True:
        feedCount = pendingFeeds(r.client, r.token)
        postCount = pendingPosts(r.client, r.token)        
        feedList  = getNextFeed(r.client, r.token, feedCount)        
        postList  = getNextPost(r.client, r.token, postCount)

        addToQueue(feedQueue, feedList)
        addToQueue(postQueue, postList)
        
        l.log("Queued %d feed(s)" % int(feedCount))
        l.log("Queued %d post(s)" % int(postCount))

        newFeedThreads = newThreads(feedQueue.qsize(), tCount(threading.enumerate(), "feed"),
                                    THREADS_RATIO, MAX_THREADS, MIN_THREADS)
        newPostThreads = newThreads(postQueue.qsize(), tCount(threading.enumerate(), "post"),
                                    THREADS_RATIO, MAX_THREADS, MIN_THREADS)

        l.log("New threads: Feeds (%d) - Posts (%d)" % (int(newFeedThreads), int(newPostThreads)))

        l.debug("## Post ##################################")
        l.debug("## Queue size     = %d" % postQueue.qsize())
        l.debug("## Active Threads = %d" % tCount(threading.enumerate(), "post"))
        l.debug("## New Threads    = %d" % newPostThreads)
        l.debug("## Feed ##################################")
        l.debug("## Queue size     = %d" % feedQueue.qsize())
        l.debug("## Active Threads = %d" % tCount(threading.enumerate(), "feed"))
        l.debug("## New threads    = %d" % newFeedThreads)
        l.debug("##########################################")

        currentThreadId = processThreads(newFeedThreads, FeedThread, r.frontendWS, r.token, feedQueue, currentThreadId)
        currentThreadId = processThreads(newPostThreads, PostThread, r.frontendWS, r.token, postQueue, currentThreadId, m)
        
        time.sleep(1)
        
    # TODO
    # autoQueue(r.client, r.token)
