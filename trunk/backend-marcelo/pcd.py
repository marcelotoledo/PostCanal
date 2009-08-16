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
from utils     import Usage, tCount, addToQueue
from feed      import getNextFeed, pendingFeeds, scheduleAll, FeedThread
from post      import getNextPost, pendingPosts
from autoQueue import autoQueue
from iface     import openConnection

import sys
import time
import log
import threading
import Queue

if __name__ == "__main__":
    u = Usage()
    u.banner()
    u.usage()

    l = log.Log(u.options.verbose, u.options.debug)    

    r = runtimeConfig()
    r.addOption("Debug",      str(u.options.debug))
    r.addOption("Verbose",    str(u.options.verbose))
    r.addOption("token",      r.token)
    r.addOption("Frontend",   r.frontend)
    r.addOption("FrontendWS", r.frontendWS)
    r.printOptions()

    scheduleAll(r.client, r.token)

    MAX_THREADS   = 20
    MIN_THREADS   = 3
    THREADS_RATIO = 3
    requestQueue = Queue.Queue()
    postQueue    = Queue.Queue()

    # while True:
    #     feedCount = pendingFeeds(r.client, r.token)
    #     feedList  = getNextFeed(r.client, r.token, feedCount)
        
    #     if int(feedCount) > 1:
    #         l.log("Queueing %d items" % int(feedCount))
    #         for feed in feedList:
    #             requestQueue.put(feed)
                
    #     queueSize   = requestQueue.qsize()
    #     #threadCount = threading.activeCount() - 1
    #     threadCount = tCount(threading.enumerate(), "feed")
    #     maxCurrSize = int(round(queueSize / THREADS_RATIO))
        
    #     if maxCurrSize > MAX_THREADS:
    #         maxCurrSize = MAX_THREADS
    #     elif maxCurrSize == 0:
    #         maxCurrSize = MIN_THREADS
            
    #     newThreads  = maxCurrSize - threadCount

    #     l.debug("####################################")
    #     l.debug("QueueSize   = %d" % queueSize)
    #     l.debug("threadCount = %d" % threadCount)
    #     l.debug("maxCurrSize = %d" % maxCurrSize)
    #     l.debug("newThreads  = %d" % newThreads)
    #     l.debug("####################################")
        
    #     if newThreads > 0:
    #         l.log("Opening %d new threads" % newThreads)
    #         for i in range(newThreads):
    #             FeedThread(r.frontendWS, r.token, requestQueue, i).start()
    #     elif newThreads < 0:
    #         for i in range(newThreads * -1):
    #             requestQueue.put('kill')
                
    #     time.sleep(1)


    postCount = pendingPosts(r.client, r.token)
    postList  = getNextPost(r.client, r.token, postCount)

    print "postCount: " + str(postCount)
    print "postList : " + str(postList)

#    addToQueue(postQueue, postList)

#    if int(postCount) > 1:
#        l.log("Queueing %d items" % int(postCount))
#        for post in postList:
#            postQueue.put(post)
    
    # queueSize   = postQueue.qsize()
    # threadCount = tCount(threading.enumerate(), "post")
    # maxCurrSize = int(round(queueSize / THREADS_RATIO))

    # if maxCurrSize > MAX_THREADS:
    #     maxCurrSize = MAX_THREADS
    # elif maxCurrSize == 0:
    #     maxCurrSize = MIN_THREADS
            
    # newThreads  = maxCurrSize - threadCount

    # l.debug("####################################")
    # l.debug("QueueSize   = %d" % queueSize)
    # l.debug("threadCount = %d" % threadCount)
    # l.debug("maxCurrSize = %d" % maxCurrSize)
    # l.debug("newThreads  = %d" % newThreads)
    # l.debug("####################################")
        
    # if newThreads > 0:
    #     l.log("Opening %d new threads" % newThreads)
    #     for i in range(newThreads):
    #         FeedThread(r.frontendWS, r.token, requestQueue, i).start()
    #     elif newThreads < 0:
    #         for i in range(newThreads * -1):
    #             requestQueue.put('kill')    

#    print threading.enumerate()
    
    # TODO
    #Publish(r.client, r.token)
    #autoQueue(r.client, r.token)
