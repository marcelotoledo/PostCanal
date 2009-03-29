#!/usr/bin/env python
# coding: utf-8
# 20080903 AF

import web
from inspect import ismethod
from SimpleXMLRPCServer import SimpleXMLRPCDispatcher

dispatcher = SimpleXMLRPCDispatcher(allow_none=False, encoding=False)

urls = (
    '/',        'index',
    '/xmlrpc',  'xmlrpc',
)

# dump a list with all available methods
class index:
    def GET(self):
        web.header('Content-type', 'text/html')
        print '<h1>web.py xml-rpc server</h1>'
        print 'This server provides the following methods:<ul>'
        for n in dispatcher.system_listMethods():
            print '<li><b>%s:</b> %s' % (n, dispatcher.system_methodHelp(n))
        print '</ul>'

# xml-rpc service
class xmlrpc:
    def POST(self):
        print dispatcher._marshaled_dispatch(web.data())

# metaclass to make all server methods run in "safe mode"
class Safe:
    def __init__(self):
        for name in dir(self):
            obj = getattr(self, name)
            if ismethod(obj) and name != '__init__':
                setattr(self, name, self.__decorator(obj))

    def __decorator(self, meth):
        def call(*args):
            try: return meth(*args)
            except Exception, e: return 'error: %s' % e
        # keep the doc string!
        call.__doc__ = meth.__doc__ or 'no help'
        return call

# methods on this class will be provided by this server
class ServerMethods(Safe):
    def sort(self, data):
        "sort(list): return the sorted version of that list"
        data.sort()
        return data

    def multiply(self, a, b):
        "multiply(a, b): return a*b"
        return a * b

if __name__ == '__main__':
    dispatcher.register_instance(ServerMethods())
    #web.run(urls, globals())
