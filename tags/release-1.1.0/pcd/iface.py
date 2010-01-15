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

# def openConnection(url):
#     if url == None:
#         return None
    
#     try:
#         client = xmlrpclib.ServerProxy(url)
#     except:
#         l.log("webservice call failed; (%s)" % (sys.exc_info()[0].__name__), funcName())
#         return None
    
#     return client

def openConnection(url):
    if url == None:
        return None

    try:
        client = xmlrpclib.ServerProxy(url, transport=TimeoutTransport())
    except:
        l.log("webservice call failed; (%s)" % (sys.exc_info()[0].__name__), funcName())
        return None
    
    return client

class TimeoutTransport(xmlrpclib.Transport):
    """Add a timeout attibute to HTTPConnection through HTTP."""
    
    def __init__(self, use_datetime=0, timeout=60):
        xmlrpclib.Transport.__init__(self, use_datetime)
        self.timeout = timeout

    def make_connection(self, host):
        """
        Extends Transport.make_connection to set the timeout on the
        connection instance.
        """
        conn = xmlrpclib.Transport.make_connection(self, host)
        conn._conn.timeout = self.timeout
        return conn
