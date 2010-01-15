# pcdmonitor.py --- pcd monitor

# Copyright  (C)  2009  Postcanal Inc. <http://www.postcanal.com>

# Version: 1.0
# Keywords: 
# Author: Marcelo Toledo <marcelo.toledo@postcanal.com>
# Maintainer: Marcelo Toledo <marcelo.toledo@postcanal.com>
# URL: http://

# Commentary: 



# Code:

from ansi    import *
from monitor import Monitor

import time

mon = Monitor()

mon.delAll()

def mainBox():
    colLabel = 2
    colPost  = 13
    colFeed  = 25
    row      = 4

    feed_queue_size     = mon.getValue('feed_queue_size')
    feed_active_threads = mon.getValue('feed_active_threads')
    feed_new_threads    = mon.getValue('feed_new_threads')
    post_queue_size     = mon.getValue('post_queue_size')
    post_active_threads = mon.getValue('post_active_threads')
    post_new_threads    = mon.getValue('post_new_threads')

    term('red @%s;%s %5s %5s %5s' % (row,   colPost,
                                     'Feeds', 'Posts', 'Total'))
        
    term('@%s;%s %8s %5s %5s %5s' % (row+1, colLabel, 'Threads',
                                     feed_active_threads, post_active_threads,
                                     str(int(feed_active_threads) + int(post_active_threads))))

    term('@%s;%s %8s %5s %5s %5s' % (row+2, colLabel, 'Queue',
                                     feed_queue_size, post_queue_size,
                                     str(int(feed_queue_size) + int(post_queue_size))))

    term('@%s;%s %8s %5s %5s %5s' % (row+3, colLabel, 'New Thr.',
                                     feed_new_threads, post_new_threads,
                                     str(int(feed_new_threads) + int(post_new_threads))))


def title():
    term('grey')
    print "PCD - Postcanal Daemon Monitor"
    print "Copyright  (C)  2009, Postcanal Inc. <http://www.postcanal.com>"

def clock():
    term('yellow @4;5 %s' % strftime('%H:%M:%S', time.localtime()))

def showThreads():
    types   = ['feed', 'post']
    row     = 9

    i = 0
    for myType in types:
        label = '%s Threads' % myType.capitalize()
        term('red @%s;2 %10s' % (row, label))

        row += 2

        res = mon.getThreads(myType)
        if res == None:
            return None

        for item in res:
            term('@%s;2 %s - %s' % (row, item[0], item[1]))
            row += 1            
        row += 1

while True:
    try:
        term('@@')
        title()
        #clock()
        mainBox()
        showThreads()
        time.sleep(1)
    except KeyboardInterrupt:
        break
    except:
        raise
    term('@30;1')
    print
