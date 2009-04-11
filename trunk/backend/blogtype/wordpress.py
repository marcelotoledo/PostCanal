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


import urlparse, re, httplib
import config
import blogtype


class WordPress(blogtype.BlogType):
    url_spl = None # url split
    loc_spl = None # location split
    manager_url_spl = None # url manager split
    loc_manager_spl = None # url manager location split

    def __init__(self):
        self.type = config.WORDPRESS

    def factory(self, url, client):
        self.set_url(url)
        if self.url_accepted == False: self.check_url(client)
        self.set_manager_url(self.url_spl.scheme + "://" + self.url_spl.netloc + self.url_spl.path + config.WORDPRESS_MANAGER_URL_SUFFIX)
        self.check_manager_url(client)

    def set_version(self, name):
        self.version = name 
        self.revision = config.WORDPRESS_REVISION[name]

    def set_url(self, url):
        self.url = url
        self.url_spl = urlparse.urlsplit(url)
        self.loc_spl = re.split("\.", self.url_spl.netloc)

    def check_url(self, client):
        try:
            client.request("GET", self.url_spl.path if self.url_spl.path else "/")
            response = client.getresponse()
            self.url_accepted = True if response.status == 200 else False
        except:
            self.url_accepted = False

    def set_manager_url(self, manager_url):
        _sub = config.WORDPRESS_MANAGER_URL_SUFFIX_SUB
        manager_url = re.sub(_sub[0], _sub[1], manager_url)
        self.manager_url = manager_url
        self.manager_url_spl = urlparse.urlsplit(manager_url)
        self.loc_manager_spl = re.split("\.", self.manager_url_spl.netloc)
        self.username = self.loc_manager_spl[0]

    def check_manager_url(self, client):
        try:
            client.request("GET", self.manager_url_spl.path if self.manager_url_spl.path else "/" + config.WORDPRESS_MANAGER_URL_SUFFIX)
            response = client.getresponse()

            # casual redirects
            # domain.com > domain.wordpress.com

            if response.status in [301, 302]:
                _loc = response.getheader('Location')
                if re.search(config.WORDPRESS_MANAGER_URL_SUFFIX_SEARCH, _loc):
                    self.set_manager_url(_loc)
                    _cli = httplib.HTTPSConnection(self.manager_url_spl.netloc, httplib.HTTPS_PORT, timeout=config.WORDPRESS_CLIENT_TIMEOUT) if self.manager_url_spl.scheme == "https" else httplib.HTTPConnection(self.manager_url_spl.netloc, httplib.HTTP_PORT, timeout=config.WORDPRESS_CLIENT_TIMEOUT)
                    _cli.request("GET", self.manager_url_spl.path + self.manager_url_spl.query + self.manager_url_spl.fragment)
                    response = _cli.getresponse()

            self.manager_url_accepted = True if response.status == 200 else False
        except:
            self.manager_url_accepted = False
