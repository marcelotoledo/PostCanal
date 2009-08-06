# utils.py --- utils for pcd

# Copyright  (C)  2009  Marcelo Toledo <marcelo@marcelotoledo.org>

# Version: 1.0
# Keywords: 
# Author: Marcelo Toledo <marcelo@marcelotoledo.org>
# Maintainer: Marcelo Toledo <marcelo@marcelotoledo.org>
# URL: http://

# This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.

# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.

# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

# Commentary: 



# Code:

import sys
import conf
import log
import inspect
from optparse import OptionParser

class Usage():
    def __init__(self):
        self.options = {}
        self.version = "1.0.0"

    def banner(self):
        print "PCD - PostCanal Daemon - Version %s" % self.version
        print "Copyright  (C)  2009, Postcanal Inc. All rights reserved.\n"
        
    def usage(self):
        usage = "Usage: %s [options]" % sys.argv[0]
        parser = OptionParser(usage)
        parser.add_option("-v", "--verbose", action="store_true", dest="verbose",
                          default=True, help="messages goes to stdout and syslog")
        parser.add_option("-s", "--syslog", action="store_false", dest="verbose",
                          help="messages goes to syslog")
        parser.add_option("-d", "--debug", action="store_true", dest="debug", default=False,
                          help="print debug messages")
        
        (self.options, args) = parser.parse_args()

        if len(sys.argv) <= 1:
            parser.print_help()
            sys.exit(0)

def funcName():
    frame = inspect.currentframe()
    return frame.f_back.f_code.co_name
