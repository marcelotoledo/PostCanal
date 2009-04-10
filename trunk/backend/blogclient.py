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
from blogtype import config
from vendor.BeautifulSoup import BeautifulSoup


CLIENT_TIMEOUT = 15


def init_type(type_id, version_id):
    type = None

    ### wordpress ###

    if type_id == config.WORDPRESS:
        from blogtype import wordpress
        type = wordpress.WordPress()
        type.set_version(version_id)

    return type

def url_fix(url):
    return url if re.match("(http|https)://", url) else "http://" + url

def init_client(url):
    url = urlparse.urlsplit(url_fix(url))
    return httplib.HTTPSConnection(url.netloc, httplib.HTTPS_PORT, timeout=CLIENT_TIMEOUT) if url.scheme == "https" else httplib.HTTPConnection(url.netloc, httplib.HTTP_PORT, timeout=CLIENT_TIMEOUT)

def guess_type(url):
    client = init_client(url)
    url = urlparse.urlsplit(url_fix(url))
    type = None

    # 
    # from location
    # 

    ### wordpress / wordpress.com ###

    if type == None:
        if re.match(config.WORDPRESS_URL_MATCH, url.netloc):
            type = init_type(config.WORDPRESS, config.WORDPRESS_VERSION_COM)
            type.factory("http://" + url.netloc, client)

    # 
    # from body
    # 

    body = None

    if type == None:
        client.request("GET", url.path + url.query + url.fragment)
        try:
            response = client.getresponse()
            if response.status == 200:
                body = BeautifulSoup(response.read())
        except:
            pass

    ### wordpress / wordpress.com | wordpress domain ###

    if type == None and body:
        _meta = str(body.find("meta", content=config.WORDPRESS_BODY_META_FIND))
        if re.search(config.WORDPRESS_BODY_META_SEARCH, _meta):
            type = init_type(config.WORDPRESS, config.WORDPRESS_VERSION_COM if re.match(config.WORDPRESS_URL_MATCH, url.netloc) else config.WORDPRESS_VERSION_DOMAIN)
            type.url_ok = True # avoid url re-check
            type.factory("http://" + url.netloc, client)

    ### etc... ###

    if type == None and body:
        pass

    return type

def check_url_admin(url_admin, type_id, version_id):
    type = init_type(type_id, version_id)
    type.set_url_admin(url_fix(url_admin))
    type.check_url_admin(init_client(url_admin))
    return type

def type_dictionary(type):
    result = {}
    result['id']               = type.id               if type else ""
    result['label']            = type.label            if type else ""
    result['version_id']       = type.version_id       if type else ""
    result['version_label']    = type.version_label    if type else ""
    result['version_revision'] = type.version_revision if type else 0
    result['url']              = type.url              if type else ""
    result['url_ok']           = type.url_ok           if type else False
    result['url_admin']        = type.url_admin        if type else ""
    result['url_admin_ok']     = type.url_admin_ok     if type else False
    result['login']            = type.login            if type else ""

    return result


if __name__ == '__main__':

    #url = "http://test.wordpress.com/"
    #url = "http://test1.wordpress.com/wp-admin"
    url = "http://blog100nexo.com/"
    #url = "http://asdqwezxcwer.wordpress.com/"
    #url = "http://www.cnn.com/"
    #url = "http://www.uol.com.br/"

    type = guess_type(url)
    print type_dictionary(type)

    if type:
        url_admin = type.url_admin
        type_id = type.id
        version_id = type.version_id

        type = check_url_admin(url_admin, type_id, version_id)
        print type_dictionary(type)
