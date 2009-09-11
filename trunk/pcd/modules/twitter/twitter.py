# twitter.py --- twitter API interface

# Copyright  (C)  2009  Postcanal Inc. <http://www.postcanal.com>

# Version: 1.0
# Keywords: 
# Author: Richard Penman
# Maintainer: Marcelo Toledo <marcelo.toledo@postcanal.com>
# URL: http://

# Commentary: 



# Code:

import os
import sys
import re
import urllib2
import urlparse
import twitterapi
import bitly

from HTMLParser import HTMLParser

config_path = os.getcwd()[:os.getcwd().find("pcd")] + "pcd"
sys.path.append(config_path)

import log
l = log.Log()

class PCDModule():
    """API to http://www.twitter.com
    """
    MAX_CHARS = 140
    
    modName = 'twitter'

    def __init__(self, admin_url, username, password):
        self.clear()
        self._username               = username
        self._password               = password
        self._admin_url              = admin_url
        self._article_link           = ''
        self._article_link_shortened = ''
        self.logBanner               = 'n/a'

    def isItMe(self):
        return 'twitter.com' in self.domain(self._admin_url)

    def authenticate(self):
        try:
            self.api = twitterapi.Api(self._username, self._password)
        except:
            l.log("Error authenticating - %s" % (sys.exc_info()[1]), self.logBanner)
            return False
        return True

    def setTitle(self, title):
        self._title = title

    def setContent(self, content):
        self._content = content

    def getTags(self):
        pass

    def setTags(self, tags):
        pass

    def getCategories(self):
        pass

    def setCategories(self, categories):
        pass

    def setAttachment(self, filepath):
        pass

    def postEntry(self):
        contentClean = strip_html(self._content)
        status = self._title + ': ' + contentClean + ' ' + self._article_link_shortened
        if len(status) > PCDModule.MAX_CHARS:
            dots = '... '

            data = self._title + ': ' + contentClean + ' '
            status = data[:PCDModule.MAX_CHARS - len(dots) - len(self._article_link_shortened)] + dots + self._article_link_shortened
        try:
            self.api.PostUpdate(status)
        except:
            l.log("Failed to update status (%s)" % (sys.exc_info()[1]), self.logBanner)
            return False
        return True

    def clear(self):
        """Clear everything
        """
        self._title        = ''
        self._content      = ''
        self._article_link = ''        
        self._filepath     = ''
        self._tags         = []
        self._categories   = []

    def setLogBanner(self, banner):
        "Set title"
        self.logBanner = banner

    def setArticleLink(self, url):
        try:
            bitlyApi = bitly.Api(login='mdtoledo', apikey='R_896b93325a794d611a4214c5c4b37baa')
        
            self._article_link           = url
            self._article_link_shortened = url
            self._article_link_shortened = bitlyApi.shorten(self._article_link)

            return True
        except:
            l.log("Error shortening URL - %s" % (sys.exc_info()[1]), self.logBanner)
            return False

    def domain(self, url):
        """Return domain of passed URL
        """
        return urlparse.urlsplit(url).netloc

class MLStripper(HTMLParser):
    def __init__(self):
        self.reset()
        self.fed = []
    def handle_data(self, d):
        self.fed.append(d)
    def get_data(self):
        return ''.join(self.fed)

def strip_html(html):
    s = MLStripper()
    s.feed(html)
    return s.get_data()
