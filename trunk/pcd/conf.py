# conf.py --- 

# Copyright  (C)  2009  Postcanal Inc. <http://www.postcanal.com>

# Version: 1.0
# Keywords: 
# Author: Marcelo Toledo <marcelo.toledo@postcanal.com>
# Maintainer: Marcelo Toledo <marcelo@marcelotoledo.org>
# URL: http://

# Commentary: 



# Code:

from xml.dom import minidom
from iface   import openConnection

import os
import xmlrpclib
import log

l = log.Log()

class runtimeConfig():
    def __init__(self, config_path=None):
        self.options = []
        self.l = log.Log()                
        if config_path == None:
            config_path = os.getcwd().replace("pcd", "") + "config/environment.xml"
        
        #frontend_url = config.get('base/url')
        #frontend_url = frontend_url + config.get('webservice/frontendUrl')

        self.xmldoc     = minidom.parse(config_path)                
        self.token      = self.getElement('webservice/token')
        self.frontend   = "http://www.postcanal.com"
        self.frontendWS = self.frontend + "/webservice"
        self.client     = openConnection(self.frontendWS)

    def getElement(self, path):
        tag = self.xmldoc.firstChild
        for folder in path.split('/'):
            tag = tag.getElementsByTagName(folder)[0]
        return tag.firstChild.data
    

    def addOption(self, key, value):
        self.options.append({ key : value })
        
    def printOptions(self):
        for item in self.options:
            for k, v in item.items():
                self.l.log("Loading %15s as %s" % (k, v))
        print ""
