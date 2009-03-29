#!/usr/bin/env python

from SimpleXMLRPCServer import CGIXMLRPCRequestHandler


class BackendWebService:
    def test(self, a, b):
        return a + b

handler = CGIXMLRPCRequestHandler(allow_none=False, encoding=False)
handler.register_instance(BackendWebService())
handler.handle_request()
