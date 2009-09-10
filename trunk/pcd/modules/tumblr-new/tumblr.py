# tumblr.py --- tumblr api module

# Copyright  (C)  2009  Postcanal Inc. <http://www.postcanal.com>

# Version: 1.0
# Keywords: 
# Author: Marcelo Toledo <marcelo.toledo@postcanal.com>
# Maintainer: Marcelo Toledo <marcelo.toledo@postcanal.com>
# URL: http://

# Commentary: 



# Code:

import os
import sys
import re

#import urllib

from tumblrapi import Api
#from util import peterBrowser

#config_path = os.getcwd()[:os.getcwd().find("pcd")] + "pcd"
#sys.path.append(config_path)

#import log
#l = log.Log()

class PCDModule:
    '''Class for interacting with tumblr.com'''

    modName = 'tumblr'

    # regular expression patterns to extract useful data from web page.
    url_pattern        = re.compile(r'^http://[a-z0-9-]+?\.tumblr\.com$')

    # functional urls
    login_url  = 'http://www.tumblr.com/login'
    post_entry = 'http://www.tumblr.com/new/text'

    def __init__(self, url='', adminUrl=None, username='', password=''):
        '''initiation method
        parameter url sample: 'http://xxxxx.tumblr.com/', there should be a trailing '/'.
        '''
        
        self.url      = url
        self.username = username
        self.password = password
        self.entry    = BlogEntry()
        self.api      = Api(self.url, self.username, self.password)

    def isItMe(self):
        '''This function is used to identify if this module can interact
        with the URL passed or not, to do this you need to look for
        clues and return true for positive or false for negative.'''

        if not PCDModule.url_pattern.match(self.url):
            #print "URL invalida (%s) nao bate com (%s)" % (self.url, '^http://[a-z0-9-]+?\.tumblr\.com/$')
            return False
        return True
    
    def authenticate(self):
        '''This function returns true or false, respectively for sucessful
        authentication or not.'''

        try:
            self.api.auth_check()
            return True
        except:
            return False

    def setTitle(self, title):
        "Set title"
        self.entry.title = title

    def setContent(self, content):
        "Set body content"
        self.entry.content = content

    def getTags(self):
        "Return list of available tags"
        return self.entry.tags

    def setTags(self, tags):
        "Set tags"
        self.entry.tags = tags

    def getCategories(self):
        "Return list of available categories"
        return self.entry.categories

    def setCategories(self,categories):
        "Set categories"
        self.entry.categories = categories

    def setAttachment(self,attachment):
        "Add attachment"
        self.entry.attachment = attachment

    def postEntry(self):
        "Post entry"
        try:
            post = self.api.write_regular(self.entry.title, self.entry.content)
            return True
        except:
            #print "Erro ao postar artigo no tumblr: %s" % (sys.exc_info()[1])
            return False

    def clear(self):
        "Clear everything"
        self.entry.clear()

class BlogEntry:
    '''class for a blog entry'''
    def __init__(self, title=None, content=None, tags=[], categories=[], attachment=None):
        '''initiate a blog entry object'''
        self.title      = title
        self.content    = content
        self.tags       = tags
        self.categories = categories
        self.attachment = attachment

    def clear(self):
        '''clear the title and content of the blog entry'''
        self.title      = None
        self.content    = None
        self.tags       = []
        self.categories = []
        self.attachment = None
