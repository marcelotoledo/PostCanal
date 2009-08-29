# live_journal.py --- Live Journal module

# Copyright  (C)  2009  Postcanal Inc. <http://www.postcanal.com>

# Version: 1.0
# Keywords: 
# Author: Peter Liu
# Maintainer: Marcelo Toledo <marcelo.toledo@postcanal.com>
# URL: http://

# Commentary: 



# Code:

'''This is the module for interacting with LiveJournal.com.
It includes the main class LiveJournal'''

import re
import hashlib
import urllib

from datetime import datetime
from util     import LoggerFactory
from util     import peterBrowser

class PCDModule:
    '''Class for interacting with LiveJournal.com'''
    #logger=LoggerFactory.getLogger('LiveJournal')

    modName = 'livejournal'

    #regular expression patterns to extract useful data from web page.
    url_pattern          = re.compile(r'^http://[a-z0-9_]+?\.livejournal\.com/$')
    home_pattern         = re.compile(r"(?ius).*?><a href='http://[a-z0-9_]+?\.livejournal\.com/profile'><")
    chal_pattern         = re.compile(r"(?ius).*?name='chal' .+? value='(.+?)' />")
    logout_pattern       = re.compile(r"(?ius).*?action='http://www\.livejournal\.com/logout\.bml\?ret=1' method='post'>")
    lj_form_auth_pattern = re.compile(r'(?ius).*?name="lj_form_auth" value="(.+?)" />')
    post_success_pattern = re.compile(r"(?ius).*?<p>Now that you've posted, you can:</p>")

    #functional urls
    login_get  = 'http://www.livejournal.com/?returnto='
    login_post = 'http://www.livejournal.com/login.bml?ret=1'
    post_entry = 'http://www.livejournal.com/update.bml'

    def __init__(self, url='', adminUrl=None, username='', password=''):
        '''initiation method
        parameter url sample: 'http://xxxxx.livejournal.com/', there should be a trailing '/'.
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
            #PCDModule.logger.warn('invalid url, it should comply with the regular expression "^http://[a-z0-9_]+?\.livejournal\.com/$"')
            return False
        
        data = peterBrowser.getUrl(self.url)
        if not PCDModule.home_pattern.match(data):
            return False
        return True

    def authenticate(self):
        '''This function returns true or false, respectively for sucessful
        authentication or not.'''
        
        data = peterBrowser.getUrl('%s%s'%(PCDModule.login_get,self.url))
        matcher = PCDModule.chal_pattern.match(data)
        if not matcher:
            #PCDModule.logger.error("Can't find the chal value when trying to authenticate")
            return False
        
        chal_value = matcher.group(1)
        
        values={'returnto': self.url,
            'mode' : 'login',
            'user' : self.username,
            'password' : ''}
        
        values['chal']     = chal_value
        values['response'] = hashlib.md5(chal_value+hashlib.md5(self.password).hexdigest()).hexdigest()
        
        data = peterBrowser.getUrl(PCDModule.login_post, urllib.urlencode(values))
        if PCDModule.logout_pattern.match(data):
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

    def setCategories(self, categories):
        "Set categories"
        self.entry.categories = categories

    def setAttachment(self, attachment):
        "Add attachment"
        self.entry.attachment = attachment

    def postEntry(self):
        "Post entry"
        
        data = peterBrowser.getUrl(PCDModule.post_entry)
        matcher = PCDModule.lj_form_auth_pattern.match(data)
        if not matcher:
            #PCDModule.logger.error("Can't find the lj_form_auth value when trying to post entry")
            return False
        
        lj_form_auth_value = matcher.group(1)
        matcher = PCDModule.chal_pattern.match(data)
        if not matcher:
            #PCDModule.logger.error("Can't find the chal value when trying to post entry")
            return False
        
        chal_value = matcher.group(1)
        dt         = datetime.now()
        
        values={
            'user' : self.username,
            'date_ymd_mm' : '%d'%dt.month,
            'date_ymd_dd' : '%d'%dt.day,
            'date_ymd_yyyy' : '%d'%dt.year,
            'hour' : '%d'%dt.hour,
            'min' : '%d'%dt.minute,
            'date_diff' : '1',
            'subject' : self.entry.title,
            'event' : self.entry.content,
            'switched_rte_on' : '1',
            'prop_taglist' : ','.join(self.entry.tags),
            'prop_pingback' : 'J',
            'security' : 'public',
            'action:update' : 'Post to petertest',
        }
        
        values['lj_form_auth'] = lj_form_auth_value
        values['chal']         = chal_value        
        values['response']     = hashlib.md5(chal_value+hashlib.md5(self.password).hexdigest()).hexdigest()
        
        data = peterBrowser.getUrl(PCDModule.post_entry, urllib.urlencode(values))
        if PCDModule.post_success_pattern.match(data):
            return True
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
        self.title   = None
        self.content = None
