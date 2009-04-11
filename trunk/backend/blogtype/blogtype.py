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
    type                 = None
    version              = None
    revision             = 0
    url                  = None
    url_accepted         = False
    manager_url          = None
    manager_url_accepted = False
    username             = None

    def factory(self, url, client):
        pass
    def set_version(self, version):
        pass
    def set_url(self, url):
        pass
    def check_url(self, client):
        pass
    def set_manager_url(self, manager_url):
        pass
    def check_manager_url(self, client):
        pass
