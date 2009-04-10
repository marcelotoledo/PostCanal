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
    url_admin_spl = None # url admin split
    loc_admin_spl = None # url admin location split

    def __init__(self):
        self.id    = config.WORDPRESS
        self.label = config.WORDPRESS_LABEL

    def factory(self, url, client):
        self.set_url(url)
        self.check_url(client)
        self.set_url_admin(self.url_spl.scheme + "://" + self.url_spl.netloc + self.url_spl.path + config.WORDPRESS_URL_ADMIN_SUFFIX)
        self.check_url_admin(client)

    def set_version(self, version_id):
        self.version_id = version_id
        self.version_label = config.WORDPRESS_VERSION[version_id][0]
        self.version_revision = config.WORDPRESS_VERSION[version_id][1]

    def set_url(self, url):
        self.url = url
        self.url_spl = urlparse.urlsplit(url)
        self.loc_spl = re.split("\.", self.url_spl.netloc)

    def check_url(self, client):
        try:
            client.request("GET", self.url_spl.path if self.url_spl.path else "/")
            response = client.getresponse()
            self.url_ok = True if response.status == 200 else False
        except:
            self.url_ok = False

    def set_url_admin(self, url_admin):
        _sub = config.WORDPRESS_URL_ADMIN_SUFFIX_SUB
        url_admin = re.sub(_sub[0], _sub[1], url_admin)
        self.url_admin = url_admin
        self.url_admin_spl = urlparse.urlsplit(url_admin)
        self.loc_admin_spl = re.split("\.", self.url_admin_spl.netloc)
        self.login = self.loc_admin_spl[0]

    def check_url_admin(self, client):
        try:
            client.request("GET", self.url_admin_spl.path if self.url_admin_spl.path else "/" + config.WORDPRESS_URL_ADMIN_SUFFIX)
            response = client.getresponse()

            # casual redirects
            # domain.com > domain.wordpress.com

            if response.status in [301, 302]:
                _loc = response.getheader('Location')
                if re.search(config.WORDPRESS_URL_ADMIN_SUFFIX_SEARCH, _loc):
                    self.set_url_admin(_loc)
                    _cli = httplib.HTTPSConnection(self.url_admin_spl.netloc, httplib.HTTPS_PORT, timeout=config.WORDPRESS_CLIENT_TIMEOUT) if self.url_admin_spl.scheme == "https" else httplib.HTTPConnection(self.url_admin_spl.netloc, httplib.HTTP_PORT, timeout=config.WORDPRESS_CLIENT_TIMEOUT)
                    _cli.request("GET", self.url_admin_spl.path + self.url_admin_spl.query + self.url_admin_spl.fragment)
                    response = _cli.getresponse()

            self.url_admin_ok = True if response.status == 200 else False
        except:
            self.url_admin_ok = False
