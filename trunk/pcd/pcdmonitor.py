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

def mainBox():
    colLabel = 5
    colPost  = 20
    colFeed  = 28
    row      = 5

    feed_queue_size     = mon.getValue('feed_queue_size')
    feed_active_threads = mon.getValue('feed_active_threads')
    feed_new_threads    = mon.getValue('feed_new_threads')
    post_queue_size     = mon.getValue('post_queue_size')
    post_active_threads = mon.getValue('post_active_threads')
    post_new_threads    = mon.getValue('post_new_threads')
    
    term('@%s;%s Threads'  % (row+2, colLabel))
    term('@%s;%s Queue'    % (row+3, colLabel))
    term('@%s;%s New thr.' % (row+4, colLabel))

    term('red @%s;%s Posts' % (row, colPost))
    term('@%s;%s %s'        % (row+2, colPost, post_active_threads))
    term('@%s;%s %s'        % (row+3, colPost, post_queue_size))
    term('@%s;%s %s'        % (row+4, colPost, post_new_threads))

    term('red @%s;%s Feeds' % (row, colFeed))
    term('@%s;%s %s'        % (row+2, colFeed, feed_active_threads))
    term('@%s;%s %s'        % (row+3, colFeed, feed_queue_size))
    term('@%s;%s %s'        % (row+4, colFeed, feed_new_threads))

def title():
    term('grey')
    print "PCD - Postcanal Daemon Monitor"
    print "Copyright  (C)  2009, Postcanal Inc. <http://www.postcanal.com>"

def clock():
    term('yellow @4;5 %s' % strftime('%H:%M:%S', time.localtime()))

while True:
    try:
        term('@@')
        title()
        clock()
        mainBox()
        time.sleep(1)
    except KeyboardInterrupt:
        break
    except:
        raise
    term('@30;1')
    print
