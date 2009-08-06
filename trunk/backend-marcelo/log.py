# log.py --- log routines

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

import syslog

class Log():
    __share_resources = {}
    
    def __init__(self, __isVerbose=None, __isDebug=None):
        self.__dict__ = self.__share_resources
        
        if __isVerbose != None:
            self.isVerbose = __isVerbose
        if __isDebug != None:
            self.isDebug   = __isDebug

    def LogPrint(self, string):
        if self.isVerbose:
            print string
        else:
            syslog.syslog(string)

    def log(self, string, func=None):
        if func == None:
            func = 'Main'            
        self.LogPrint("%15s \t %s" % (func, string))

    def debug(self, string, func=None):
        if func == None:
            func = 'Main'
        if self.isDebug:
            self.LogPrint("%15s \t %s" % (func, string))
