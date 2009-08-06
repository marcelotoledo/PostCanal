# conf.py --- 

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
