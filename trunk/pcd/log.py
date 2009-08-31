# log.py --- log routines

# Copyright  (C)  2009  Postcanal Inc. <http://www.postcanal.com>

# Version: 1.0
# Keywords: 
# Author: Marcelo Toledo <marcelo@marcelotoledo.org>
# Maintainer: Marcelo Toledo <marcelo@marcelotoledo.org>
# URL: http://

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
        self.LogPrint("%20s \t %s" % (func, string))

    def debug(self, string, func=None):
        if func == None:
            func = 'Main'
        if self.isDebug:
            self.LogPrint("%20s \t %s" % (func, string))

    def emptyLine(self):
        print ""
