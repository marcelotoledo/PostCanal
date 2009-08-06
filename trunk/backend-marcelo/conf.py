# conf.py --- 

# Copyright  (C)  2009  Postcanal Inc. <http://www.postcanal.com>

# Version: 1.0
# Keywords: 
# Author: Marcelo Toledo <marcelo.toledo@postcanal.com>
# Maintainer: Marcelo Toledo <marcelo@marcelotoledo.org>
# URL: http://

# Commentary: 



# Code:

import xmlrpclib
import log

class runtimeConfig():
    def __init__(self, config_path=None):
        self.options = {}
        self.l = log.Log()                
        if config_path == None:
            import os
            config_path = os.getcwd().replace("backend-marcelo", "") + "config/environment.xml"
            
        from xml.dom import minidom
        self.xmldoc = minidom.parse(config_path)

        self.token = self.getElement('webservice/token')
        #frontend_url = config.get('base/url')
        #frontend_url = frontend_url + config.get('webservice/frontendUrl')
        self.frontend = "http://www.postcanal.com"
        self.frontendWS = self.frontend + "/webservice"
        self.client = xmlrpclib.ServerProxy(self.frontendWS)

    def getElement(self, path):
        tag = self.xmldoc.firstChild
        for folder in path.split('/'):
            tag = tag.getElementsByTagName(folder)[0]
        return tag.firstChild.data
    

    def addOption(self, key, value):
        self.options[key] = value
        
    def printOptions(self):
        for k, v in self.options.items():
            self.l.log("Loading %15s as %s" % (k, v))
        self.l.log("")
