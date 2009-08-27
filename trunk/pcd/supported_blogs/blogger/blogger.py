#!/usr/bin/python
# -*- coding: UTF-8 -*- 
# 
# Author: Richard Penman
# Date: 22/08/09
# 

import sys

import gdata.blogger.client
import gdata.client

from blog import Blog, BlogError



class Blogger(Blog):
    """API to http://www.blogger.com
    """
    def __init__(self, admin_url, username, password):
        Blog.__init__(self, admin_url, username, password)
        self.api = gdata.blogger.client.BloggerClient()

    #___________________________________________________________________________

    def isItMe(self):
        return 'blogger.com' in self.domain(self._admin_url) or 'blogspot.com' in self.domain(self._admin_url)

    #___________________________________________________________________________

    def __str__(self):
        return 'Blogger'

    #___________________________________________________________________________

    def authenticate(self):
        try:
            self.api.client_login(self._username, self._password, source='Blogger Python API 2.0', service='blogger')
        except gdata.client.BadAuthentication:
            self._authenticated = False
        else:
            self._authenticated = True
            self._blog_id = None
            # find blog
            for entry in self.api.get_blogs().entry:
                blog_url = self.domain(entry.get_html_link().href)
                if blog_url == self.domain(self._admin_url):
                    self._blog_id = entry.get_blog_id()
                    break
            if not self._blog_id:
                raise BlogError('Error: blog with given admin URL not found')
        return self._authenticated
    
    #___________________________________________________________________________

    def setTitle(self, title):
        self._title = title

    #___________________________________________________________________________

    def setContent(self, content):
        self._content = content

    #___________________________________________________________________________

    def setTags(self, tags):
        self._tags = tags

    #___________________________________________________________________________

    def postEntry(self):
        if self._authenticated:
            self.api.add_post(self._blog_id, self._title, self._content, labels=self._tags)
        else:
            print 'Need to authenticate'



if __name__ == '__main__':
    try:
        admin_url = sys.argv[1]
        email = sys.argv[2]
        password = sys.argv[3]
    except IndexError:
        print 'Usage: python %s <url> <blogger.com email> <password>' % sys.argv[0]
    else:
        api = Blogger(admin_url, email, password)
        if api.authenticate():
            api.setTitle('basic test')
            api.setContent('my test post')
            api.setTags(['test', 'basic'])
            api.postEntry()

            api.clear()
            api.setTitle('unicode test')
            api.setContent('汉语/漢語')
            api.postEntry()
        else:
            print 'Authentication failed'
