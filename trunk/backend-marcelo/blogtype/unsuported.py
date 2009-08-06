# unsuported.py --- unsuported blog type

# Copyright  (C)  2009  Rafael Castilho <rafel@castilho.biz>

# Version: 1.0
# Keywords: 
# Author: Rafael Castilho <rafael@castilho.biz>
# Maintainer: Rafael Castilho <rafael@castilho.biz>
# URL: http://

# Commentary: 

# Code:
# unsuported blog type class

VERSION = "1.0.0"


import os, sys, urlparse, re, httplib
import config


CLIENT_TIMEOUT = 15


class Unsuported:

    ### common attributes ###
    client               = None 
    type                 = None
    version              = None
    revision             = 0
    url                  = None
    url_accepted         = False
    title                = None
    manager_url          = None
    manager_url_accepted = False
    username             = None
    password             = None
    login_accepted       = False
    publication_accepted = False


    url_spl = None # url split


    ### common methods ###

    def __init__(self, client=None):
        self.type = config.UNSUPORTED
        self.client = client

    def factory(self, url):
        self.set_url(url)
        if self.url_accepted == False: self.check_url()

    def set_version(self, name):
        self.version = name 
        self.revision = config.UNSUPORTED_REVISION[name]

    def set_url(self, url):
        self.url = url
        self.url_spl = urlparse.urlsplit(url)

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

    def check_manager_url(self):
        self.manager_url_accepted = False

    def check_login(self):
        self.login_accepted = False

    def check_publication(self):
        self.publication_accepted = False

    def publish(self, args):
        return 0
