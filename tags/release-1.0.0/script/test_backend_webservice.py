#!/usr/bin/env python

import os
import sys

pcdDir = '../pcd'

paths = [ pcdDir, pcdDir + '/vendor', pcdDir + '/modules' ]
for item in paths:
    sys.path.append(item)

basePath = os.path.abspath("../")

# ==============================================================================

from conf import runtimeConfig

config = runtimeConfig(pcdDir)
token = config.token
remote = "%s%s" % (config.getElement('base/url'), 
                   config.getElement('webservice/backendUrl'))

import xmlrpclib

server = xmlrpclib.ServerProxy(remote)

# ==============================================================================

print "---------------------------------------------------------------------------------"
print "blog discover"
print "---------------------------------------------------------------------------------"

for url in [ 'http://twitter.com/marcelotoledo', 
             'http://blog.marcelotoledo.org', 
             'http://pcanal2.blogspot.com', 
             'http://postcanal.livejournal.com', 
             'http://postcanal.tumblr.com' ] :
    result = server.blog_discover({ 'token' : token, 'url' : url })
    print result
