#!/usr/bin/python
# -*- coding: UTF-8 -*- 
# 
# Author: Richard Penman
# Date: 22/08/09
# 

import sys
import urlparse


class Blog:
    """Base class for Blog interfaces
    """
    def __init__(self, admin_url, username, password):
        self.clear()
        self._username = username
        self._password = password
        self._admin_url = admin_url
        self._authenticated = False

    #___________________________________________________________________________

    def isItMe(self):
        """This function is used to identify if this module can interact with the URL passed or not, to do this you need to look for clues and return true for positive or false for negative.
        """
        self.unsupported()
        return False

    #___________________________________________________________________________

    def authenticate(self):
        """This function returns true or false, respectively for sucessful authentication or not.
        """
        self.unsupported()
        return False

    #___________________________________________________________________________


    def setTitle(self, title):
        """Set title
        """
        self.unsupported()

    #___________________________________________________________________________

    def setContent(self, content):
        """Set body content
        """
        self.unsupported()

    #___________________________________________________________________________

    def getTags(self):
        """Return list of available tags
        """
        self.unsupported()
        return []

    #___________________________________________________________________________

    def setTags(self, tags):
        """Set tags for blog post
        """
        self.unsupported()

    #___________________________________________________________________________

    def getCategories(self):
        """Return list of available categories
        """
        self.unsupported()
        return []

    #___________________________________________________________________________

    def setCategories(self, categories):
        """Set categories
        """
        self.unsupported()

    #___________________________________________________________________________

    def setAttachment(self, filepath):
        """Add attachment
        """
        self.unsupported()

    #___________________________________________________________________________

    def postEntry(self):
        """Submit entry to blog
        """
        self.unsupported()

    #___________________________________________________________________________

    def clear(self):
        """Clear everything
        """
        self._title = ''
        self._content = ''
        self._filepath = ''
        self._tags = []
        self._categories = []

    #___________________________________________________________________________

    def unsupported(self):
        """Warning message to display for unsupported functionality
        """
        print 'Warning: %s does not support "%s()"' % (str(self), sys._getframe(1).f_code.co_name)

    #___________________________________________________________________________

    def domain(self, url):
        """Return domain of passed URL
        """
        return urlparse.urlsplit(url).netloc


class BlogError(Exception):
    pass
