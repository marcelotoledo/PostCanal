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
                   config.getElement('webservice/frontendUrl'))

import xmlrpclib

server = xmlrpclib.ServerProxy(remote)

# ==============================================================================

print "---------------------------------------------------------------------------------"
print "feed update get"
print "---------------------------------------------------------------------------------"

print server.feed_update_get({ 'token' : token, 'total' : 3 })
