# tumblr.py --- tumblr api module

# Copyright  (C)  2009  Postcanal Inc. <http://www.postcanal.com>

# Version: 1.0
# Keywords: 
# Author: Peter Liu
# Maintainer: Marcelo Toledo <marcelo.toledo@postcanal.com>
# URL: http://

# Commentary: 



# Code:

import os
import sys
import re
import urllib

from util import peterBrowser

config_path = os.getcwd()[:os.getcwd().find("pcd")] + "pcd"
sys.path.append(config_path)

#import log
#l = log.Log()

class PCDModule:
    '''Class for interacting with tumblr.com'''
    #logger=LoggerFactory.getLogger('tumblr')

    modName = 'tumblr'

    # regular expression patterns to extract useful data from web page.
    url_pattern        = re.compile(r'^http://[a-z0-9-]+?\.tumblr\.com/$')
    not_found_pattern  = re.compile(r"(?ius).*?<h1>We couldn't find the page you were looking for\.</h1>")
    logging_in_pattern = re.compile(r'(?ius).*?<title>Logging in\.\.\.</title>')
    form_key_pattern   = re.compile(r'(?ius).*?<input type="hidden" id="form_key" name="form_key" value="(.+?)"/>')

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

    def isItMe(self):
        '''This function is used to identify if this module can interact
        with the URL passed or not, to do this you need to look for
        clues and return true for positive or false for negative.'''
        
        if not PCDModule.url_pattern.match(self.url):
            #l.log("Invalid url, it should comply with the regular expression")
            return False
        data = peterBrowser.getUrl(self.url)
        if PCDModule.not_found_pattern.match(data):
            return False
        return True

    def authenticate(self):
        '''This function returns true or false, respectively for sucessful
        authentication or not.'''
        
        values = {
        'email'       : self.username,
        'password'    : self.password,
        'redirect_to' : '/dashboard'
        }
        
        data = peterBrowser.getUrl(PCDModule.login_url,urllib.urlencode(values))
        if PCDModule.logging_in_pattern.match(data):
            return True
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
        data = peterBrowser.getUrl(PCDModule.post_entry)
        matcher = PCDModule.form_key_pattern.match(data)
        if not matcher:
            #l.log("Can't find the form_key value when trying to post entry")
            return False
        form_key_value = matcher.group(1)
        values = {
            'post[state]'         : '0',
            'post[publish_on]'    : '',
            'post[draft_status]'  : '',
            'post[date]'          : 'now',
            'post[tags]'          : ','.join(self.entry.tags),
            'post[slug]'          : '',
            'is_rich_text[one]'   : '0',
            'is_rich_text[two]'   : '1',
            'is_rich_text[three]' : '0',
            'form_key'            : form_key_value,
            'post[one]'           : self.entry.title,
            'post[two]'           : self.entry.content,
            'post[type]'          : 'regular',
        }
        
        data = peterBrowser.getUrl(PCDModule.post_entry, urllib.urlencode(values))
        if not data:
            return False
        return True

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

