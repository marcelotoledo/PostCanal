# postcanal.py --- postcanal utility

# Copyright  (C)  2009  Rafael Castilho <rafel@castilho.biz>

# Version: 1.0
# Keywords: 
# Author: Rafael Castilho <rafael@castilho.biz>
# Maintainer: Rafael Castilho <rafael@castilho.biz>
# URL: http://

# Commentary: 

# Code:
# postcanal utility

VERSION = "1.0.0"

class PostCanalConfig:
    def __init__(self, config_path=None):
        if config_path == None:
            import os
            config_path = os.getcwd().replace("backend", "") + "config/environment.xml"

        from xml.dom import minidom
        self.xmldoc = minidom.parse(config_path)

    def get(self, path):
        tag = self.xmldoc.firstChild
        for folder in path.split('/'):
            tag = tag.getElementsByTagName(folder)[0]
        return tag.firstChild.data
