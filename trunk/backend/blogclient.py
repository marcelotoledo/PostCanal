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

    if type == None and body:

        ### WordPress.com ###

        if re.search("WordPress\.com", str(body.find("meta", content="WordPress.com"))):
            from blogtype import wordpress
            type = wordpress.WordPress(url.netloc, client)
            type.setver(wordpress.VERSION_WORDPRESS_COM)

    if type == None and body:

        ### Other... ###

        pass

    return type


if __name__ == '__main__':
    #url = "http://test.wordpress.com/"
    #print gettype(url)
    url = "http://blog100nexo.com/"
    print gettype(url)
    #url = "http://asdqwezxcwer.wordpress.com/"
    #print gettype(url)
    #url = "http://www.cnn.com/"
    #print gettype(url)
    #url = "http://www.uol.com.br/"
    #print gettype(url)
