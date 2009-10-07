# iface.py --- xmlrpc interface

# Copyright  (C)  2009  Postcanal Inc. <http://www.postcanal.com>

# Version: 1.0
# Keywords: 
# Author: Marcelo Toledo <marcelo.toledo@vexcorp.com>
# Maintainer: Marcelo Toledo <marcelo.toledo@vexcorp.com>
# URL: http://

# Commentary:

# Code:

import log
import xmlrpclib

l = log.Log()

def openConnection(url):
    if url == None:
        return None
    
    try:
        client = xmlrpclib.ServerProxy(url)
    except:
        l.log("webservice call failed; (%s)" % (sys.exc_info()[0].__name__), funcName())
        return None
    
    return client
