# tumblr_usage.py --- tumblr module usage
# -*- coding: utf-8 -*-

# Copyright  (C)  2009  Marcelo Toledo <marcelo.toledo@postcanal.com>

# Version: 1.0
# Keywords: 
# Author: Marcelo Toledo <marcelo.toledo@postcanal.com>
# Maintainer: Marcelo Toledo <marcelo.toledo@postcanal.com>
# URL: http://

# Commentary: 



# Code:

'''Demo program which uses the live-journal module.'''

import sys
from tumblr import PCDModule

Tumblr = PCDModule

#username='peter4test', password='peter2win'
#b = Tumblr('http://peter4test.tumblr.com/', None, 'mituzhishi@gmail.com', 'peter2win')
b = Tumblr('http://postcanal.tumblr.com', None, 'marcelo@marcelotoledo.org', 'adv70076')

if not b.isItMe():
    print 'isItMe() False: invalid url'
    sys.exit()

if not b.authenticate():
    print "Authentication failed"
    sys.exit()

print "My available tags:"
for tag in b.getTags():
    print tag

print "My available categories:"
for cat in b.getCategories():
    print cat

b.setTitle('draft title')
b.setContent('draft content')

# technology exists and was retrieved through getCategories, but
# business doesn't exist so behind the scenes it must be added to
# the system and linked with this post somehow
catList = ['technology', 'business']
b.setCategories(catList)

# The same might happen here if a tag is new
tagsList = ['技术', '商业']
b.setTags(tagsList)

b.setAttachment('/home/marcelo/file.jpg')

if b.postEntry():
    print "Article posted!"
else:
    print "Article NOT posted!"

b.clear()
b.setTitle('标题')
b.setContent('内容')
if b.postEntry():
    print "Article posted!"
else:
    print "Article NOT posted!"
