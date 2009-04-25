# wordpress.py --- wordpress blog type

# Copyright  (C)  2009  Rafael Castilho <rafel@castilho.biz>

# Version: 1.0
# Keywords: 
# Author: Rafael Castilho <rafael@castilho.biz>
# Maintainer: Rafael Castilho <rafael@castilho.biz>
# URL: http://

# Commentary: 

# Code:
# wordpress blog type class

VERSION = "1.0.0"


import os, sys, urlparse, re, httplib
import config


CLIENT_TIMEOUT = 15


class WordPress:
    ### common attributes ###

    client               = None 
    type                 = None
    version              = None
    revision             = 0
    url                  = None
    url_accepted         = False
    manager_url          = None
    manager_url_accepted = False
    username             = None
    password             = None


    url_spl = None # url split
    loc_spl = None # location split
    manager_url_spl = None # url manager split
    loc_manager_spl = None # url manager location split


    ### common methods ###

    def __init__(self, client=None):
        self.type = config.WORDPRESS
        self.client = client

    def factory(self, url):
        self.set_url(url)
        if self.url_accepted == False: self.check_url()
        self.set_manager_url(self.url_spl.scheme + "://" + self.url_spl.netloc + self.url_spl.path + config.WORDPRESS_MANAGER_URL_SUFFIX)
        self.check_manager_url()

    def set_version(self, name):
        self.version = name 
        self.revision = config.WORDPRESS_REVISION[name]

    def set_url(self, url):
        self.url = url
        self.url_spl = urlparse.urlsplit(url)
        self.loc_spl = re.split("\.", self.url_spl.netloc)

    def check_url(self):
        try:
            if self.client == None: self.client = httplib.HTTPConnection(self.url_spl.netloc, httplib.HTTP_PORT, timeout=CLIENT_TIMEOUT)
            self.client.request("GET", self.url_spl.path if self.url_spl.path else "/")
            response = self.client.getresponse()
            self.url_accepted = True if response.status == 200 else False
        except:
            self.url_accepted = False

    def set_manager_url(self, manager_url):
        self.manager_url = manager_url
        self.manager_url_spl = urlparse.urlsplit(manager_url)
        self.loc_manager_spl = re.split("\.", self.manager_url_spl.netloc)
        self.username = self.loc_manager_spl[0]

    def check_manager_url(self):
        try:
            if self.client == None: self.client = httplib.HTTPConnection(self.manager_url_spl.netloc, httplib.HTTP_PORT, timeout=CLIENT_TIMEOUT)
            self.client.request("GET", self.manager_url_spl.path if self.manager_url_spl.path else "/" + config.WORDPRESS_MANAGER_URL_SUFFIX)
            response = self.client.getresponse()
            self.manager_url_accepted = True if response.status == 200 else False
        except:
            self.manager_url_accepted = False
    
    def publish(self, args):
        sys.path.append(os.getcwd().replace("blogtype", "") + "/vendor")
        import wordpresslib

        w = wordpresslib.WordPressClient(self.manager_url, self.username, self.password)
        w.selectBlog(0)
        p = wordpresslib.WordPressPost()
        p.title = args.get('title')
        p.description = args.get('content')
        return w.newPost(p, True)
