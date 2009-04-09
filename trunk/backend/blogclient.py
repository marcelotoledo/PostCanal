# blogfinder.py --- blog client for backend system of blotomate

# Copyright  (C)  2009  Rafael Castilho <rafel@castilho.biz>

# Version: 1.0
# Keywords: 
# Author: Rafael Castilho <rafael@castilho.biz>
# Maintainer: Rafael Castilho <rafael@castilho.biz>
# URL: http://

# Commentary: 

# Code:
# blog client for backend system of blotomate

VERSION = "1.0.0"


import urlparse, re, httplib
from BeautifulSoup import BeautifulSoup


D_PORT = 80
D_TIMEOUT = 15

T_WPRESS     = 1000
V_WPRESS_COM =    1


def gettype(url):
    split = urlparse.urlsplit(url)
    type = None
    client = None
    soup = None

    # 
    # from location
    # 

    if type == None:

        ### WordPress.com ###
        if re.match("(.+)\.wordpress\.com", split.netloc):
            type = TypeWordPress(split.netloc, client)
            type.setver(V_WPRESS_COM)

    # 
    # from body
    # 

    if type == None:
        if client == None:
            port = split.port if split.port != None else D_PORT
            client = httplib.HTTPConnection(split.netloc, port, timeout=D_TIMEOUT)
            client.request("GET", "/")
            response = client.getresponse()
            if response.status == 200:
                #soup = BeautifulSoup(''.join(response.read()))
                soup = BeautifulSoup(response.read())

    if type == None:

        ### WordPress.com ###

        if re.search("WordPress\.com", 
                     str(soup.find("meta", content="WordPress.com"))):
            type = TypeWordPress(split.netloc, client)
            type.setver(V_WPRESS_COM)

    if type == None:

        ### Other... ###

        pass

    return type


def from_location(location, client=None):
    type = None

    if re.match("(.+)\.wordpress\.com", location):
        type = TypeWordPress(location, client)
        type.setver(V_WPRESS_COM)
        
    return type



class BlogType(object):
    location         = None
    client           = None
    id               = 0
    type             = None
    version          = None
    revision         = None
    url              = None
    url_ok           = False
    url_admin        = None
    url_admin_ok     = False
    login            = None

    def setver(self, v):
        pass

class TypeWordPress(BlogType):
    versions = { 
        V_WPRESS_COM : 
        { 
            'name'     : "wordpress.com", 
            'revision' : 1 
        }
    }

    def __init__(self, location, client=None):
        self.location = location
        self.client   = client
        self.id       = T_WPRESS
        self.type     = "WordPress"

    def setver(self, v):
        version = self.versions[v]
        self.id = self.id + v
        self.version = version['name']
        self.revision = version['revision']
        self.url = "http://" + self.location
        rel_url_admin = "/wp-login.php"
        self.url_admin = self.url + rel_url_admin
        split = re.split("\.", self.location)
        self.login = split[0]

        if self.client == None:
            self.client = httplib.HTTPConnection(self.location, 
                                                 D_PORT, 
                                                 timeout=D_TIMEOUT)

        # test url

        if self.url_ok == False:
            self.client.request("GET", "/")

            try:
                response = self.client.getresponse()
                self.url_ok = True if response.status == 200 else False
            except:
                self.url_ok = False

        # test url admin

        self.client.request("GET", rel_url_admin)

        try:
            response = self.client.getresponse()
            self.url_admin_ok = True if response.status == 200 else False
        except:
            self.url_admin_ok = False


if __name__ == '__main__':
    url = "http://test.wordpress.com/"
    print gettype(url)
    #url = "http://blog100nexo.com/"
    #print gettype(url)
    #url = "http://asdqwezxcwer.wordpress.com/"
    #print gettype(url)
    #url = "http://www.cnn.com/"
    #print gettype(url)
