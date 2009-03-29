#!/usr/bin/env python

import sys; sys.path.append('/var/www/blotomate/public/cgi-bin')
import web

print web.header()
print "<html>Hello world!</html>"
