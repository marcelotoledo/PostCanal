# -*- coding: utf-8 -*-
# daemon.py --- daemon of the backend system of blotomate

# Copyright  (C)  2009  Rafael Castilho <rafael@castilho.biz>

# Version: 1.0
# Keywords: 
# Author: Rafael Castilho <rafael@castilho.biz>
# Maintainer: Rafael Castilho <rafael@castilho.biz>
# URL: http://

# Commentary: 

# Code:
# Run background routines

VERSION = "1.0.0"

def debug(message, priority=0):
    print "[%s] %s" % (priority, message)

def start(argv):
    import time

    while True:
        time.sleep(1)
        debug("olá")

def usage(argv):
    print 'Blotomate Daemon %s - Daemon system for blotomate.com' % VERSION
    print 'Copyright  (C)  2009 Blotomate Inc. <https://www.blotomate.com>\n'
    
    print 'Usage: %s start|test' % argv[0]

def test(argv):
    pass

if __name__ == "__main__":
    import sys

    if len(sys.argv) <= 1:
        usage(sys.argv)
        sys.exit(-1)

    if sys.argv[1] == 'start':
        start(sys.argv)
