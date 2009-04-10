# blogtype.py --- abstract blog type

# Copyright  (C)  2009  Rafael Castilho <rafel@castilho.biz>

# Version: 1.0
# Keywords: 
# Author: Rafael Castilho <rafael@castilho.biz>
# Maintainer: Rafael Castilho <rafael@castilho.biz>
# URL: http://

# Commentary: 

# Code:
# abstract blog type class

class BlogType(object):
    id               = None
    label            = None
    version_id       = None
    version_label    = None
    version_revision = 0
    url              = None
    url_ok           = False
    url_admin        = None
    url_admin_ok     = False
    login            = None

    def factory(self, url, client):
        pass
    def set_version(self, version_id):
        pass
    def set_url(self, url):
        pass
    def check_url(self, client):
        pass
    def set_url_admin(self, url_admin):
        pass
    def check_url_admin(self, client):
        pass
