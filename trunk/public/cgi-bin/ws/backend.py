#!/usr/bin/env python

import sys, os
from SimpleXMLRPCServer import CGIXMLRPCRequestHandler

cwd = os.getcwd()
sys_path = cwd.replace("public/cgi-bin/ws", "") + "backend"
config_path = cwd.replace("public/cgi-bin/ws", "") + "config/environment.xml"

sys.path.append(sys_path)

import webservice
from webservice import WebService

handler = CGIXMLRPCRequestHandler(allow_none=True, encoding=False)
handler.register_instance(WebService(config_path))
handler.handle_request()
