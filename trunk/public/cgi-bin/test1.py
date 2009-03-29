#!/usr/bin/env python

import web
from SimpleXMLRPCServer import SimpleXMLRPCDispatcher

dispatcher = SimpleXMLRPCDispatcher(allow_none=False, encoding=False)

urls = (
    '/',        'index',
    '/xmlrpc',  'xmlrpc',
)

class index:
    def GET(self):
        web.header('Content-type', 'text/html')
        print '<h1>web.py xml-rpc server</h1>'
        print 'This server provides the following methods:<ul>'
        for n in dispatcher.system_listMethods():
            print '<li><b>%s:</b> %s' % (n, dispatcher.system_methodHelp(n))
        print '</ul>'

class xmlrpc:
    def POST(self):
        print dispatcher._marshaled_dispatch(web.data())

if __name__ == '__main__':
    #dispatcher.register_instance(index())
    print web.header('text/xml')
    print "<html>Hello world!</html>"
    #web.run(urls, globals())
