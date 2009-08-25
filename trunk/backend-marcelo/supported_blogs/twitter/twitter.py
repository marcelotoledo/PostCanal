#!/usr/bin/python
# -*- coding: UTF-8 -*- 
# 
# Author: Richard Penman
# Date: 22/08/09
# 

import sys
import re
import urllib2

import ptt
import bitly

from blog import Blog



class Twitter(Blog):
    """API to http://www.twitter.com
    """
    MAX_CHARS = 140 # maximum number of characters supported by Twitter

    def __init__(self, admin_url, username, password):
        Blog.__init__(self, admin_url, username, password)

    #___________________________________________________________________________

    def __str__(self):
        return 'Twitter'

    #___________________________________________________________________________

    def isItMe(self):
        return 'twitter.com' in self.domain(self._admin_url)

    #___________________________________________________________________________

    def authenticate(self):
        self.api = ptt.Twitter(self._username, self._password)
        try:
            self.api.statuses.friends() # will raise exception if wrong login details
        except ptt.TwitterError:
            self._authenticated = False
        else:
            self._authenticated = True
        return self._authenticated

    #___________________________________________________________________________

    def setContent(self, content):
        suffix = ''
        # test if URL
        if self.domain(content):
            url = content
            html = urllib2.urlopen(url).read()
            title = re.search('<title>.*</title>', html, re.IGNORECASE).group()
            if title:
                # remove title tags
                title = title.replace('<title>', '').replace('</title>', '')
                # remove site info, if exists
                if '|' in title: title = title[title.rfind('|')+1:].strip()
                if '-' in title: title = title[:title.find('-')].strip()
                # create shortened url with bitly
                api = bitly.Api(login='pythonapi', apikey='R_20f7a20b92d38c21877ac4397fffadfb')
                suffix = ' ' + api.shorten(url)
                content = title
        if len(content) + len(suffix) > Twitter.MAX_CHARS:
            # shorten content
            dots = '...'
            content = content[:Twitter.MAX_CHARS - len(dots) - len(suffix)] + dots + suffix
        else:
            content = content + suffix
        self._content = content

    #___________________________________________________________________________

    def postEntry(self):
        if self._authenticated:
            self.api.statuses.update(status=self._content)
        else:
            print 'Need to authenticate'



if __name__ == '__main__':
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
            api.setContent('my test post to Twitter ')
            api.postEntry()

            api.clear()
            api.setContent('unicode test - 汉语/漢語')
            api.postEntry()

            # test bitly
            api.clear()
            api.setContent('http://news.bbc.co.uk/2/hi/uk_news/scotland/highlands_and_islands/8217162.stm')
            api.postEntry()

            api.clear()
            api.setContent('http://gizmodo.com/5343799/blackberry-bold-visual-voicemail-feature-now-live-os-drops-tuesday')
            api.postEntry()

            # test long text
            api.clear()
            api.setContent('abcdefghijklmnopqrstuvwxyz ABCDEFGHIJKLMNOPQRSTUVWXYZ abcdefghijklmnopqrstuvwxyz ABCDEFGHIJKLMNOPQRSTUVWXYZ abcdefghijklmnopqrstuvwxyz ABCDEFGHIJKLMNOPQRSTUVWXYZ abcdefghijklmnopqrstuvwxyz ABCDEFGHIJKLMNOPQRSTUVWXYZ')
            api.postEntry()

            # test 140 chars
            api.clear()
            api.setContent('0 1 2 3 4 5 6 7 8 9 10 11 12 13 14 15 16 17 18 19 20 21 22 23 24 25 26 27 28 29 30 31 32 33 34 35 36 37 38 39 40 41 42 43 44 45 46 47 48 49 ')
            api.postEntry()
        else:
            print 'Authentication failed'
