# blogger_usage.py --- usage example for blogger module
# -*- encoding: utf-8 -*-

# Copyright  (C)  2009  Postcanal Inc. <http://www.postcanal.com>

# Version: 1.0
# Keywords: 
# Author: Marcelo Toledo <marcelo.toledo@postcanal.com>
# Maintainer: Marcelo Toledo <marcelo.toledo@postcanal.com>
# URL: http://

# Commentary: 



# Code:

import sys
from blogger import Blogger

try:
    admin_url = sys.argv[1]
    email     = sys.argv[2]
    password  = sys.argv[3]
except IndexError:
    print 'Usage: python %s <url> <blogger.com email> <password>' % sys.argv[0]
else:
    api = Blogger(admin_url, email, password)
    if api.authenticate():
        api.setTitle('basic test')
        api.setContent('my test post')
        api.setTags(['test', 'basic'])
        api.postEntry()
        
        api.clear()
        api.setTitle('unicode test')
        api.setContent('汉语/漢語')
        api.postEntry()
    else:
        print 'Authentication failed'
