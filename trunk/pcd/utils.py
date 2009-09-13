# utils.py --- utils for pcd

# Copyright  (C)  2009  Postcanal Inc. <http://www.postcanal.com>

# Version: 1.0
# Keywords: 
# Author: Marcelo Toledo <marcelo.toledo@postcanal.com>
# Maintainer: Marcelo Toledo <marcelo.toledo@postcanal.com>
# URL: http://

# Commentary: 



# Code:

import os
import sys
import conf
import log
import inspect
import threading

from optparse import OptionParser

l = log.Log()

class Usage():
    def __init__(self):
        self.options = {}
        self.version = "1.0.0"

    def banner(self):
        l.log("PCD - PostCanal Daemon - Version %s" % self.version)
        l.log("Copyright  (C)  2009, Postcanal Inc. All rights reserved.\n")
        
        
    def usage(self):
        usage = "Usage: %s [options]" % sys.argv[0]
        parser = OptionParser(usage)
        parser.add_option("-v", "--verbose", action="store_true", dest="verbose",
                          default=True, help="messages goes to stdout and syslog")
        parser.add_option("-s", "--syslog", action="store_false", dest="verbose",
                          help="messages goes to syslog")
        parser.add_option("-d", "--debug", action="store_true", dest="debug", default=False,
                          help="print debug messages")
        parser.add_option("-m", "--monitor", action="store_true", dest="monitor", default=False,
                          help="activate pcd monitor updates")
        
        (self.options, args) = parser.parse_args()

        if len(sys.argv) <= 1:
            parser.print_help()
            sys.exit(0)

def funcName():
    frame = inspect.currentframe()
    return frame.f_back.f_code.co_name

def tCount(threadList, name):
    count = 0
    for t in threadList:
        if name in t.getName():
            count += 1
    return count

def addToQueue(queue, myList):
    if type(myList) == type(list()):
        for item in myList:
            queue.put(item)

def newThreads(queueSize, threadCount, threadRatio, maxThreads, minThreads):
    maxCurrSize = int(round(queueSize / threadRatio))
    
    if maxCurrSize > maxThreads:
        maxCurrSize = maxThreads
    elif maxCurrSize == 0 or maxCurrSize < minThreads:
        maxCurrSize = minThreads

    return maxCurrSize - threadCount

def processThreads(newThreads, Class, url, token, queue, currentThreadId, module=None, isMonitor=False):
    if newThreads > 0:
        for i in range(newThreads):
            if module == None:
                # feed class
                Class(url, token, queue, currentThreadId, isMonitor).start()
            else:
                # post class
                Class(url, token, queue, currentThreadId, module, isMonitor).start()
            currentThreadId += 1
    elif newThreads < 0:
        for i in range(newThreads * -1):
            queue.put('kill')

    MAX_THREAD_ID = 10
    if currentThreadId >= MAX_THREAD_ID:
        currentThreadId = 0

    return currentThreadId

def getDirectory():
    import os
    try:
        return os.environ['PCD_DIR']
    except:
        return None

def setPath(pcdDir):
    import sys

    paths = [ pcdDir, pcdDir + '/vendor', pcdDir + '/modules' ]

    for item in paths:
        #print "Setting %s in path..." % item
        sys.path.append(item)
