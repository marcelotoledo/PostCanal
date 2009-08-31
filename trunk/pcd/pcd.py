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
from monitor   import Monitor

import sys
import time
import log
import threading
import Queue
import codecs

sys.stdout = codecs.getwriter('utf-8')(sys.stdout)
sys.path.append(os.getcwd())

if __name__ == "__main__":
    u   = Usage()
    m   = Module()
    mon = Monitor()
    
    u.banner()
    u.usage()

    l = log.Log(u.options.verbose, u.options.debug)

    r = runtimeConfig()
    r.addOption("Debug",      str(u.options.debug))
    r.addOption("Verbose",    str(u.options.verbose))
    r.addOption("Monitor",    str(u.options.monitor))
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
        feedList  = getNextFeed(r.client, r.token, feedCount)
        if feedCount > 0:
            addToQueue(feedQueue, feedList)
            l.log("Queued %d feed(s)" % int(feedCount))            
            newFeedThreads = newThreads(feedQueue.qsize(), tCount(threading.enumerate(), "feed"),
                                        THREADS_RATIO, MAX_THREADS, MIN_THREADS)            
            currentThreadId = processThreads(newFeedThreads, FeedThread, r.frontendWS, r.token, feedQueue, currentThreadId)

        l.emptyLine()
        l.debug("Feed queue size     = %d" % feedQueue.qsize())
        l.debug("Feed active threads = %d" % tCount(threading.enumerate(), "feed"))
        l.debug("Feed new threads    = %d" % newFeedThreads)
        l.emptyLine()

        mon.setStatus('feed_queue_size',     feedQueue.qsize())
        mon.setStatus('feed_active_threads', tCount(threading.enumerate(), "feed"))
        mon.setStatus('feed_new_threads',    newFeedThreads)
        
        
        postCount = pendingPosts(r.client, r.token)        
        postList  = getNextPost(r.client, r.token, postCount)
        if feedCount > 0:
            addToQueue(postQueue, postList)
            l.log("Queued %d post(s)" % int(postCount))
            newPostThreads = newThreads(postQueue.qsize(), tCount(threading.enumerate(), "post"),
                                        THREADS_RATIO, MAX_THREADS, MIN_THREADS)
            currentThreadId = processThreads(newPostThreads, PostThread, r.frontendWS, r.token, postQueue, currentThreadId, m)

        l.emptyLine()
        l.debug("Post queue size     = %d" % postQueue.qsize())
        l.debug("Post active threads = %d" % tCount(threading.enumerate(), "post"))
        l.debug("Post new threads    = %d" % newPostThreads)
        l.emptyLine()

        mon.setStatus('post_queue_size',     postQueue.qsize())
        mon.setStatus('post_active_threads', tCount(threading.enumerate(), "post"))
        mon.setStatus('post_new_threads',    newPostThreads)
                
        time.sleep(1)
