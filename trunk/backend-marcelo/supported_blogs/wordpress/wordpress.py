#!/usr/bin/python
# -*- coding: UTF-8 -*- 
# 
# Author: Richard Penman
# Date: 22/08/09
# 

import sys
import urlparse

import pyblog

from blog import Blog



class Wordpress(Blog):
    """API for Wordpress instance
    """
    def __init__(self, admin_url, username, password):
        Blog.__init__(self, admin_url, username, password)

    #___________________________________________________________________________

    def isItMe(self):
        return True

    #___________________________________________________________________________

    def __str__(self):
        return 'Wordpress'

    #___________________________________________________________________________

    def authenticate(self):
        try:
            self.api = pyblog.WordPress(urlparse.urljoin(self._admin_url, 'xmlrpc.php'), self._username, self._password)
            self.api.get_recent_posts()
        except (pyblog.BlogError, pyblog.xmlrpclib.ProtocolError):
            self._authenticated = False
        else:
            self._authenticated = True
        return self._authenticated
    
    #___________________________________________________________________________

    def setTitle(self, title):
        self._title = title

    #___________________________________________________________________________

    def setContent(self, content):
        self._content = content

    #___________________________________________________________________________
    
    def getTags(self):
        return [tag['name'] for tag in self.api.get_tags()]

    #___________________________________________________________________________
    
    def getCategories(self):
        return [category['categoryName'] for category in self.api.get_categories()]
    
    #___________________________________________________________________________

    def setCategories(self, categories):
        self._categories = categories
    #___________________________________________________________________________

    def setAttachment(self, filepath):
        #self._filepath = filepath
        self.api.upload_file({'name': filepath})
    #___________________________________________________________________________

    def postEntry(self):
        if self._authenticated:
            for category in self._categories:
                # need to define categories separately
                self.api.new_category({'name': category})
            data = {
                'title': self._title, 
                'description': self._content,
                'categories': self._categories,
                'tags': self._tags,
            }
            self.api.new_post(data)
        else:
            print 'Need to authenticate'



if __name__ == '__main__':
    try:
        admin_url = sys.argv[1]
        username = sys.argv[2]
        password = sys.argv[3]
    except IndexError:
        print 'Usage: python %s <wordpress url> <login email> <password>' % sys.argv[0]
    else:
        api = Wordpress(admin_url, username, password)
        if api.authenticate():            
            api.setTitle('basic test')
            api.setContent('automatic post')
            api.setCategories(['cat1', 'cat2', 'test'])
            api.postEntry()
            
            api.clear()
            api.setTitle('unicode test')
            api.setContent('汉语/漢語')
            api.postEntry()
            
            #api.setAttachment('/home/rbp/test.png')
            #print api.getTags()
        else:
            print 'Authentication failed'