# blogger.py --- Module for blogger API
# -*- coding: utf-8 -*- 

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
import gdata.blogger.client
import gdata.client
import urlparse

config_path = os.getcwd()[:os.getcwd().find("pcd")] + "pcd"
sys.path.append(config_path)

import log
l = log.Log()

class PCDModule():
    """API to http://www.blogger.com
    """
    modName = 'blogger'

    def __init__(self, admin_url, username, password):
        self.clear()
        self._username      = username
        self._password      = password
        self._admin_url     = admin_url
        self.logBanner      = 'n/a'        
        self._authenticated = False        
        self.api            = gdata.blogger.client.BloggerClient()

    def isItMe(self):
        return 'blogger.com' in self.domain(self._admin_url) or 'blogspot.com' in self.domain(self._admin_url)

    def authenticate(self):
        try:
            self.api.client_login(self._username, self._password, source='Blogger Python API 2.0', service='blogger')
        except gdata.client.BadAuthentication:
            self._authenticated = False
        else:
            self._authenticated = True
            self._blog_id       = None
            
            # find blog
            for entry in self.api.get_blogs().entry:
                blog_url = self.domain(entry.get_html_link().href)
                
                if blog_url == self.domain(self._admin_url):
                    self._blog_id = entry.get_blog_id()
                    break
            if not self._blog_id:
                #raise BlogError('Error: blog with given admin URL not found')
                self._authenticated = False

        return self._authenticated
    
    def setTitle(self, title):
        self._title = title

    def setContent(self, content):
        self._content = content

    def getTags(self):
        pass

    def setTags(self, tags):
        self._tags = tags

    def getCategories(self):
        pass

    def setCategories(self, categories):
        pass

    def setAttachment(self, filepath):
        pass

    def clear(self):
        """Clear everything
        """
        self._title      = ''
        self._content    = ''
        self._filepath   = ''
        self._tags       = []
        self._categories = []

    def setLogBanner(self, banner):
        "Set title"
        self.logBanner = banner        

    def postEntry(self):
        if self._authenticated:
            self.api.add_post(self._blog_id, self._title, self._content, labels=self._tags)
            return True
        else:
            #print 'Need to authenticate'
            return False

    def setArticleLink(self, url):
        pass

    def domain(self, url):
        """Return domain of passed URL
        """
        return urlparse.urlsplit(url).netloc        
