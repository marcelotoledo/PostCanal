# utils.py --- utils for pcd

# Copyright  (C)  2009  Postcanal Inc. <http://www.postcanal.com>

# Version: 1.0
# Keywords: 
# Author: Marcelo Toledo <marcelo.toledo@postcanal.com>
# Maintainer: Marcelo Toledo <marcelo.toledo@postcanal.com>
# URL: http://

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
