# twitter.py --- twitter API interface

# Copyright  (C)  2009  Postcanal Inc. <http://www.postcanal.com>

# Version: 1.0
# Keywords: 
# Author: Richard Penman
# Maintainer: Marcelo Toledo <marcelo.toledo@postcanal.com>
# URL: http://

# Commentary: 



# Code:

import sys
import re
import urllib2
import urlparse
import twitterapi
import bitly
import log

from utils import funcName

l = log.Log()

class PCDModule():
    """API to http://www.twitter.com
    """
    MAX_CHARS = 140
    
    modName = 'twitter'

    def __init__(self, admin_url, username, password):
        self.clear()
        self._username      = username
        self._password      = password
        self._admin_url     = admin_url
        self._authenticated = False

    def isItMe(self):
        return 'twitter.com' in self.domain(self._admin_url)

    def authenticate(self):
        try:
            self.api
        except:
            self._authenticated = False
            return False
        
        self._authenticated = True
        return True

    def setTitle(self, title):
        self._title = title

    def setContent(self, content):
        api = bitly.Api(login='pythonapi', apikey='R_20f7a20b92d38c21877ac4397fffadfb')
        self._content = ' ' + api.shorten(content)

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
        l.log("Just entered postEntry", 'Twitter')
        if self._authenticated:
            l.log("I am authenticated", 'Twitter')
            # test if URL
            if len(self._title) + len(self._content) > PCDModule.MAX_CHARS:
                # shorten content
                dots = '...'
                status = self._title[:PCDModule.MAX_CHARS - len(dots) - len(self._content)] + dots + self._content
            else:
                status = self._title + self._content

            l.log("Finished fitting the string, will enter the try to publish!", 'Twitter')
            print type(status)
            print status

            try:
                l.log("Going to update status to", 'Twitter')
                self.api.PostUpdate(status)
            except:
                l.log("Failed to update status (%s) - (%s)" % (status, sys.exc_info()[1]), 'Twitter')
                return False
                
            return True
        else:
            #print 'Need to authenticate'
            return False

    def clear(self):
        """Clear everything
        """
        self._title      = ''
        self._content    = ''
        self._filepath   = ''
        self._tags       = []
        self._categories = []      

    def domain(self, url):
        """Return domain of passed URL
        """
        return urlparse.urlsplit(url).netloc        
