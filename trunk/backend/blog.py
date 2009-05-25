# blogfinder.py --- blog client for backend system of postcanal

# Copyright  (C)  2009  Rafael Castilho <rafel@castilho.biz>

# Version: 1.0
# Keywords: 
# Author: Rafael Castilho <rafael@castilho.biz>
# Maintainer: Rafael Castilho <rafael@castilho.biz>
# URL: http://

# Commentary: 

# Code:
# blog client for backend system of postcanal

VERSION = "1.0.0"


import urlparse, re, httplib
from blogtype import config
from vendor.BeautifulSoup import BeautifulSoup

CLIENT_TIMEOUT = 15


def init_type(name, version_id, client=None):
    type = None

    ### wordpress ###

    if name == config.WORDPRESS:
        from blogtype import wordpress
        type = wordpress.WordPress(client)
        type.set_version(version_id)

    return type

def url_fix(url):
    return url if re.match("(http|https)://", url) else "http://" + url

def guess_type(url):
    url = urlparse.urlsplit(url_fix(url))
    client = httplib.HTTPSConnection(url.netloc, httplib.HTTPS_PORT, timeout=CLIENT_TIMEOUT) if url.scheme == "https" else httplib.HTTPConnection(url.netloc, httplib.HTTP_PORT, timeout=CLIENT_TIMEOUT)
    type = None

    # 
    # from location
    # 

    ### wordpress / wordpress.com ###

    if type == None:
        if re.match(config.WORDPRESS_URL_MATCH, url.netloc):
            type = init_type(config.WORDPRESS, config.WORDPRESS_VERSION_COM, client)
            type.factory("http://" + url.netloc)

    # 
    # from body
    # 

    body = None

    if type == None:
        try:
            client.request("GET", url.path + url.query + url.fragment)
            response = client.getresponse()
            if response.status == 200:
                body = BeautifulSoup(response.read())
        except:
            pass

    ### wordpress / wordpress.com | wordpress domain ###

    if type == None and body:
        _meta = str(body.find("meta", content=config.WORDPRESS_BODY_META_FIND))
        if re.search(config.WORDPRESS_BODY_META_SEARCH, _meta):
            type = init_type(config.WORDPRESS, config.WORDPRESS_VERSION_COM if re.match(config.WORDPRESS_URL_MATCH, url.netloc) else config.WORDPRESS_VERSION_DOMAIN, client)
            type.url_accepted = True # avoid url re-check
            type.factory("http://" + url.netloc)

    ### etc... ###

    if type == None and body:
        pass

    return type

def manager_url_check(manager_url, type_name, version_name):
    type = init_type(type_name, version_name)
    type.set_manager_url(url_fix(manager_url))
    type.check_manager_url()
    return type

def login_check(manager_url, type_name, version_name, username, password):
    type = init_type(type_name, version_name)
    type.set_manager_url(url_fix(manager_url))
    type.username = username
    type.password = password
    type.check_login()
    return type

def publication_check(manager_url, type_name, version_name, username, password):
    type = init_type(type_name, version_name)
    type.set_manager_url(url_fix(manager_url))
    type.username = username
    type.password = password
    type.check_publication()
    return type

def type_dump(type):
    result = {}

    result['type']                 = type.type                 if type else ""
    result['version']              = type.version              if type else ""
    result['revision']             = type.revision             if type else 0
    result['url']                  = type.url                  if type else ""
    result['url_accepted']         = type.url_accepted         if type else False
    result['manager_url']          = type.manager_url          if type else ""
    result['manager_url_accepted'] = type.manager_url_accepted if type else False
    result['username']             = type.username             if type else ""
    result['password']             = type.password             if type else ""
    result['login_accepted']       = type.login_accepted       if type else False
    result['publication_accepted'] = type.publication_accepted if type else False

    return result


if __name__ == '__main__':

    #url = "http://test.wordpress.com/"
    url = "http://blog100nexo.com/"
    #url = "http://asdqwezxcwer.wordpress.com/"
    #url = "http://www.cnn.com/"
    #url = "http://www.uol.com.br/"

    d = type_dump(guess_type(url))
    print d
    m = type_dump(manager_url_check(d['manager_url'], d['type'], d['version']))
    print m
