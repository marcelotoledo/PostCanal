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
from vendor.BeautifulSoup import BeautifulSoup


D_PORT = 80
D_TIMEOUT = 15


def gethcli(location, port=D_PORT, timeout=D_TIMEOUT):
    return httplib.HTTPConnection(location, port, timeout)

def gettype(url):
    url = urlparse.urlsplit(url)
    type = None
    client = None

    # 
    # from location
    # 

    if type == None:

        ### WordPress.com ###

        if re.match("(.+)\.wordpress\.com", url.netloc):
            from blogtype import wordpress
            if client == None: client = gethcli(url.netloc)
            type = wordpress.WordPress(url.netloc, client)
            type.setver(wordpress.VERSION_WORDPRESS_COM)

    # 
    # from body
    # 

    body = None

    if type == None:
        if client == None:
            port = url.port if url.port != None else D_PORT
            client = gethcli(url.netloc, port)
            client.request("GET", "/")

            try:
                response = client.getresponse()
                if response.status == 200:
                    body = BeautifulSoup(response.read())
            except:
                pass

    ### WordPress.com ###

    if type == None and body:
        _meta = str(body.find("meta", content="WordPress.com"))
        if re.search("WordPress\.com", _meta):
            from blogtype import wordpress
            type = wordpress.WordPress(url.netloc, client)
            type.setver(wordpress.VERSION_WORDPRESS_COM if re.match("(.+)\.wordpress\.com", url.netloc) else wordpress.VERSION_WORDPRESS_DOMAIN)

    ### Other... ###

    if type == None and body:
        pass

    return type


if __name__ == '__main__':
    url = "http://test.wordpress.com/"
    #url = "http://blog100nexo.com/"
    #url = "http://asdqwezxcwer.wordpress.com/"
    #url = "http://www.cnn.com/"
    #url = "http://www.uol.com.br/"
    type = gettype(url)

    if type:
        print "location: %s"     % (type.location)
        print "id: %s"           % (type.id)
        print "name: %s"         % (type.name)
        print "version: %s"      % (type.version)
        print "version_name: %s" % (type.version_name)
        print "url: %s"          % (type.url)
        print "url_ok: %s"       % (type.url_ok)
        print "url_admin: %s"    % (type.url_admin)
        print "url_admin_ok: %s" % (type.url_admin_ok)
