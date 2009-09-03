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

from monitor import Monitor

class Log():
    __share_resources = {}
    
    def __init__(self, isVerbose=None, isDebug=None, isMonitor=None):
        self.__dict__ = self.__share_resources

        if isVerbose != None:
            self.isVerbose = isVerbose
        if isDebug != None:
            self.isDebug   = isDebug
        if isMonitor != None:
            self.isMonitor = isMonitor

    def LogPrint(self, string):
        if self.isVerbose:
            print string
        else:
            syslog.syslog(string)

    def log(self, string, func=None, key=None, value=None, mon=None):
        if func == None:
            func = 'Main'            
        self.LogPrint("%15s \t %s" % (func, string))        

        if key != None and self.isMonitor == True:
            if value == 'copy-string':
                value = string
            mon.setStatus(key, value)

    def debug(self, string, func=None, key=None, value=None, mon=None):
        if func == None:
            func = 'Main'
        if self.isDebug:
            self.LogPrint("%15s \t %s" % (func, string))

        if key != None and self.isMonitor == True:
            if value == 'copy-string':
                value = string
            mon.setStatus(key, value)

    def emptyLine(self):
        print ""
