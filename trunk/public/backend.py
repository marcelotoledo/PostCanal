#!/usr/bin/env python

import sys, os
from SimpleXMLRPCServer import CGIXMLRPCRequestHandler

base_path = os.path.abspath("../")
config_path = base_path + "/config/environment.xml"

sys.path.append(base_path + "/pcd")
sys.path.append(base_path + "/pcd/vendor")

import webservice
from webservice import WebService

handler = CGIXMLRPCRequestHandler(allow_none=True, encoding=False)
handler.register_instance(WebService(config_path))
handler.handle_request()
