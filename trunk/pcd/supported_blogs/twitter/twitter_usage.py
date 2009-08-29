# twitter_usage.py --- twitter module usage
# -*- coding: utf-8 -*-

# Copyright  (C)  2009  Postcanal Inc. <http://www.postcanal.com>

# Version: 1.0
# Keywords: 
# Author: Marcelo Toledo <marcelo.toledo@postcanal.com>
# Maintainer: Marcelo Toledo <marcelo.toledo@postcanal.com>
# URL: http://

# Commentary: 



# Code:

import sys
from twitter import PCDModule

Twitter = PCDModule

try:
    admin_url = sys.argv[1]
    username = sys.argv[2]
    password = sys.argv[3]
except IndexError:
    print 'Usage: python %s <url> <twitter.com login> <password>' % sys.argv[0]
else:
    api = Twitter(admin_url, username, password)
    if api.authenticate():
        # test example cases
        api.setTitle('my test post to Twitter ')
        api.postEntry()
        
        api.clear()
        api.setTitle('unicode test - 汉语/漢語')
        api.postEntry()
        
        # test bitly
        api.clear()
        api.setTitle('Loch canoeists die after rescue')
        api.setContent('http://news.bbc.co.uk/2/hi/uk_news/scotland/highlands_and_islands/8217162.stm')
        api.postEntry()
        
        api.clear()
        api.setTitle('BlackBerry Bold Visual Voicemail Feature Now Live, OS Drops Tuesday [BlackBerry]')
        api.setContent('http://gizmodo.com/5343799/blackberry-bold-visual-voicemail-feature-now-live-os-drops-tuesday')
        api.postEntry()
        
        # test long text
        api.clear()
        api.setTitle('abcdefghijklmnopqrstuvwxyz ABCDEFGHIJKLMNOPQRSTUVWXYZ abcdefghijklmnopqrstuvwxyz ABCDEFGHIJKLMNOPQRSTUVWXYZ abcdefghijklmnopqrstuvwxyz ABCDEFGHIJKLMNOPQRSTUVWXYZ abcdefghijklmnopqrstuvwxyz ABCDEFGHIJKLMNOPQRSTUVWXYZ')
        api.postEntry()
        
        # test 140 chars
        api.clear()
        api.setTitle('0 1 2 3 4 5 6 7 8 9 10 11 12 13 14 15 16 17 18 19 20 21 22 23 24 25 26 27 28 29 30 31 32 33 34 35 36 37 38 39 40 41 42 43 44 45 46 47 48 49 ')
        api.postEntry()
    else:
        print 'Authentication failed'
