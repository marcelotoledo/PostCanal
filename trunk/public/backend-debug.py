#!/usr/bin/env python

import sys, os
from SimpleXMLRPCServer import CGIXMLRPCRequestHandler

class WebServiceDebug:
    def test(self):
        return "ok"

handler = CGIXMLRPCRequestHandler(allow_none=True, encoding=False)
handler.register_instance(WebServiceDebug())
handler.handle_request()
