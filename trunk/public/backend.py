#!/usr/bin/env python

import os
import sys

def getDirectory():
    try:
        return os.environ['PCD_DIR']
    except:
        return None

def setPath(pcdDir):
    paths = [ pcdDir, pcdDir + '/vendor', pcdDir + '/modules' ]
    for item in paths:
        sys.path.append(item)

pcdDir = getDirectory()
if pcdDir == None:
    print "Error - Environment variable PCD_DIR not set. Exiting..."
    sys.exit(1)
    
setPath(pcdDir)

import webservice
from SimpleXMLRPCServer import CGIXMLRPCRequestHandler
from webservice         import WebService

handler = CGIXMLRPCRequestHandler(allow_none=True, encoding=False)
handler.register_instance(WebService(pcdDir))
handler.handle_request()
