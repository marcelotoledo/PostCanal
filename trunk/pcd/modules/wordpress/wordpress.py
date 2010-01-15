# wordpress.py --- wordpress module
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
import urlparse
import urllib
import pyblog

config_path = os.getcwd()[:os.getcwd().find("pcd")] + "pcd"
sys.path.append(config_path)

import log
l = log.Log()

class PCDModule:
    """API for Wordpress instance
    """

    modName = 'wordpress'
    
    def __init__(self, admin_url, username, password):
        self.clear()
        self._username      = username
        self._password      = password
        self._admin_url     = admin_url
        self.logBanner      = 'n/a'
        self._authenticated = False

    def isItMe(self):
        try:
            # a wordpress site will have this special page
            return urllib.urlopen(urlparse.urljoin(self._admin_url+'/', 'xmlrpc.php')).read() == 'XML-RPC server accepts POST requests only.'
        except:
            return False

    def authenticate(self):
        try:
            self.api = pyblog.WordPress(urlparse.urljoin(self._admin_url+'/', 'xmlrpc.php'), self._username, self._password)
            self.api.get_recent_posts()
        except (pyblog.BlogError, pyblog.xmlrpclib.ProtocolError):
            self._authenticated = False
        else:
            self._authenticated = True
        return self._authenticated
    
    def setTitle(self, title):
        self._title = title

    def setContent(self, content):
        self._content = content

    def getTags(self):
        return [tag['name'] for tag in self.api.get_tags()]

    def getCategories(self):
        return [category['categoryName'] for category in self.api.get_categories()]
    
    def setCategories(self, categories):
        self._categories = categories

    def setAttachment(self, filepath):
        return self.api.upload_file({'name': filepath})['url']

    def postEntry(self):
        if self._authenticated:
            for category in self._categories:
                # need to define categories separately
                self.api.new_category({'name': category})

            data = {
                'title'       : self._title, 
                'description' : self._content,
                'categories'  : self._categories,
                'tags'        : self._tags,
            }

            self.api.new_post(data)
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

    def setLogBanner(self, banner):
        "Set title"
        self.logBanner = banner

    def setArticleLink(self, url):
        pass

    def domain(self, url):
        """Return domain of passed URL
        """
        return urlparse.urlsplit(url).netloc        
