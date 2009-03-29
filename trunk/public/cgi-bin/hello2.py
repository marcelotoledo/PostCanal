#!/usr/bin/env python

#import sys; sys.path.append('/var/www/blotomate/public/cgi-bin')
import sys; sys.path.append('../dir')
import web

print web.header('text/plain')
for key, value in web.cgi.items():
    print "%s: %s\n"%(key, value)
