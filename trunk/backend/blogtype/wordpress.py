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


from blogtype import BlogType
import urlparse, re, httplib
###from vendor.BeautifulSoup import BeautifulSoup


D_PORT = 80
D_TIMEOUT = 15

TYPE_ID          = "wordpress"
TYPE_NAME        = "WordPress"
URL_ADMIN_SUFFIX = "/wp-login.php"

VERSION_WORDPRESS_COM    = "wordpress_com"
VERSION_WORDPRESS_DOMAIN = "wordpress_domain"

#           version                      name                revision
VERSION = { VERSION_WORDPRESS_COM    : [ "wordpress.com",    1 ],
            VERSION_WORDPRESS_DOMAIN : [ "wordpress domain", 1 ] }


class WordPress(BlogType):

    def __init__(self, location, client=None):
        self.location = location
        self.client   = client
        self.id       = TYPE_ID
        self.name     = TYPE_NAME

    def setver(self, version):
        self.version = version
        _ver = VERSION[version]
        self.version_name = "%s (%s)" % (_ver[0], _ver[1])
        self.url = "http://" + self.location

        if self.version == VERSION_WORDPRESS_COM:
            _spl = re.split("\.", self.location)
            self.login = _spl[0]
            self.url_admin = self.url + URL_ADMIN_SUFFIX

        # test url

        if self.url_ok == False:
            self.client.request("GET", "/")

            try:
                response = self.client.getresponse()
                self.url_ok = True if response.status == 200 else False
            except:
                self.url_ok = False

        # test url admin

        self.client.request("GET", URL_ADMIN_SUFFIX)

        try:
            response = self.client.getresponse()

            # url admin redirects for wordpress domain

            if self.version == VERSION_WORDPRESS_DOMAIN and response.status == 302:
                _url = urlparse.urlsplit(response.getheader('Location'))
                _spl = re.split("\.", _url.netloc)
                self.login = _spl[0]
                self.url_admin = "http://" + _url.netloc + URL_ADMIN_SUFFIX
                _cli = httplib.HTTPConnection(_url.netloc, 
                                              port=D_PORT, 
                                              timeout=D_TIMEOUT)
                _cli.request("GET", _url.path)
                response = _cli.getresponse()

            self.url_admin_ok = True if response.status == 200 else False
        except:
            self.url_admin_ok = False
