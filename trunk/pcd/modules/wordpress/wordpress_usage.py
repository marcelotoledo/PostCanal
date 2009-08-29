# wordpress_usage.py --- wordpress usage
# -*- coding: utf-8 -*-

# Copyright  (C)  2009  Postcanal Inc. <http://www.postcanal.com>

# Version: 1.0
# Keywords: 
# Author: Marcelo Toledo <marcelo.toledo@postcanal.com>
# Maintainer: Marcelo Toledo <marcelo.toledo@postcanal.com>
# URL: http://

# Commentary: 



# Code:

import sys
from wordpress import PCDModule

Wordpress = PCDModule

try:
    admin_url = sys.argv[1]
    username = sys.argv[2]
    password = sys.argv[3]
except IndexError:
    print 'Usage: python %s <wordpress url> <login email> <password>' % sys.argv[0]
else:
    api = Wordpress(admin_url, username, password)
    if api.authenticate():            
        api.setTitle('basic test')
        api.setContent('automatic post')
        api.setCategories(['cat1', 'cat2', 'test'])
        api.postEntry()
            
        api.clear()
        api.setTitle('unicode test')
        api.setContent('汉语/漢語')
        api.postEntry()
        
        #print api.setAttachment('/home/rbp/test.png')
        #print api.getTags()
    else:
        print 'Authentication failed'
