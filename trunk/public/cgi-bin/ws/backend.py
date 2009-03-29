#!/usr/bin/env python

BLOTOMATE_BACKEND_PATH = "../../../backend"
BLOTOMATE_CONFIG_PATH  = "../../../config/environment.xml"

import sys
from SimpleXMLRPCServer import CGIXMLRPCRequestHandler

sys.path.append(BLOTOMATE_BACKEND_PATH)

import blotomate
from blotomate import BlotomateConfig

config = BlotomateConfig(BLOTOMATE_CONFIG_PATH)

import webservice
from webservice import BackendWebService

ws = BackendWebService()
ws.token = config.get(['application','webservice','token'])


handler = CGIXMLRPCRequestHandler(allow_none=True, encoding=False)
handler.register_instance(ws)
handler.handle_request()
