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
from utils     import Usage
from feed      import feedUpdate, getNextFeed, processFeed, pendingFeeds, scheduleAll, FeedThread
from publish   import Publish
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

    MAX_THREADS  = 5
    requestQueue = Queue.Queue()

    #while True:
    feedCount = pendingFeeds(r.client, r.token)
    feedList  = getNextFeed(r.client, r.token, feedCount)

    if int(feedCount) > 1:
        print "Adicionando %d itens no Queue" % int(feedCount)
        for feed in feedList:
            requestQueue.put(feed)

    #for i in range(MAX_THREADS):
    for i in range(10):
        FeedThread(r.frontendWS, r.token, requestQueue, i).start()
            
    #time.sleep(5)

    #for item in threading.enumerate():
    #    print item

    #print "We have %d active threads." % threading.activeCount()

    #    feed = getNextFeed(r.frontendWS, r.token)
    #    if feed != None:
    #        threadCount = threadCount + 1
    #        thread.start_new_thread(processFeed, (r.frontendWS, r.token, feed))
            
        #feedUpdate(r.client, r.token)
        #Publish(r.client, r.token)
        #autoQueue(r.client, r.token)
