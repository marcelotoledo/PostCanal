# main.py --- main file of the backend system of blotomate

# Copyright  (C)  2009  Marcelo Toledo <marcelo@marcelotoledo.org>

# Version: 1.0
# Keywords: 
# Author: Marcelo Toledo <marcelo@marcelotoledo.org>
# Maintainer: Marcelo Toledo <marcelo@marcelotoledo.org>
# URL: http://

# Commentary: 

# Code:
# find all rss feeds within one click of a URL

VERSION = "1.0.0"

import sys
from vendor import feedfinder

def usage():
    print 'Blotomate Backend %s - Backend system for blotomate.com' % VERSION
    print 'Copyright  (C)  2009 Blotomate Inc. <https://www.blotomate.com>\n'
    
    print 'Usage: %s [url]' % sys.argv[0]

if __name__ == "__main__":
    if len(sys.argv) <= 1:
        usage()
        sys.exit(-1)

    feeds = feedfinder.feeds(sys.argv[1])

    feeds_len = len(feeds)
    
    print "Feed for %s: " % sys.argv[1]
    if feeds_len == 0:
        print "No feed available."
    else:
        print "Preferencial: %s" % feeds[0]
        if feeds_len > 1:
            print "Others:       %s" % feeds[1:]
