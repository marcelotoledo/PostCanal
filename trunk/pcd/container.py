# container.py --- 

# Copyright  (C)  2009  Postcanal Inc. <http://www.postcanal.com>

# Version: 1.0
# Keywords: 
# Author: Marcelo Toledo <marcelo.toledo@postcanal.com>
# Maintainer: Marcelo Toledo <marcelo.toledo@postcanal.com>
# URL: http://

# Commentary: 



# Code:

class TContainer:
    def __init__(self, url=None):
        self.type                 = ''
        self.version              = ''
        self.revision             = 0
        self.url                  = url
        self.url_accepted         = False
        self.title                = ''
        self.manager_url          = ''
        self.manager_url_accepted = False
        self.username             = ''
        self.password             = ''
        self.login_accepted       = False
        self.publication_accepted = False

    def setType(self, mytype):
        self.type = mytype

    def setURL(self, url):
        self.url = url

    def setURLAccepted(self, value):
        self.url_accepted = value

    def setTitle(self, title):
        self.title = title

    def setManagerURL(self, url):
        self.manager_url = url

    def setUsername(self, username):
        self.username = username

    def setPassword(self, password):
        self.password = password

    def getData(self):
        result = { }
        
        result['type_name']            = self.type
        result['version_name']         = self.version
        result['revision']             = self.revision
        result['url']                  = self.url
        result['url_accepted']         = self.url_accepted
        result['title']                = self.title
        result['manager_url']          = self.manager_url
        result['manager_url_accepted'] = self.manager_url_accepted
        result['username']             = self.username
        result['password']             = self.password
        result['login_accepted']       = self.login_accepted
        result['publication_accepted'] = self.publication_accepted

        return result
