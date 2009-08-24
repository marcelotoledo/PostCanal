#!/usr/bin/python
# -*- coding: UTF-8 -*- 
# 
# Author: Richard Penman
# Date: 22/08/09
# 

import sys

import ptt
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
        if len(content) > Twitter.MAX_CHARS:
            print 'Warning: only the first %d characters will be posted' % Twitter.MAX_CHARS
            self._content = content[:Twitter.MAX_CHARS]
        else:
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
            api.setContent('my test post to Twitter ')
            api.postEntry()

            api.clear()
            api.setContent('unicode test - 汉语/漢語')
            api.postEntry()
        else:
            print 'Authentication failed'
