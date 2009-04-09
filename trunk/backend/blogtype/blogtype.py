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
    location         = None
    client           = None
    id               = 0
    name             = None
    version          = None
    version_name     = None
    url              = None
    url_ok           = False
    url_admin        = None
    url_admin_ok     = False
    login            = None

    def setver(self, version):
        pass
