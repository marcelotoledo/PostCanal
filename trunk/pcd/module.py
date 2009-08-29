# module.py --- modules interface

# Copyright  (C)  2009  Postcanal Inc. <http://www.postcanal.com>

# Version: 1.0
# Keywords: 
# Author: Marcelo Toledo <marcelo.toledo@postcanal.com>
# Maintainer: Marcelo Toledo <marcelo.toledo@postcanal.com>
# URL: http://

# Commentary: 



# Code:

import os

class Module:
    def __init__(self):
        self.moduleDir        = '/modules'
        self.ignored          = [ '__init__.py', '__init__.pyc', '.svn' ]
        self.modules          = { }
        self.classes          = [ ]
        self.loadModuleData()

    def loadModuleData(self):
        for item in os.listdir(os.getcwd() + self.moduleDir):
            if not item in self.ignored:
                self.modules[item] = 'modules.' + item + '.' + item
        
    def availableModules(self):
        return self.modules

    def loadModule(self, name, path):
        package  = __import__(path)
        dynclass = getattr(getattr(package, name), name)

        if dynclass not in self.classes:
            self.classes.append(dynclass)

    def myClass(self, adminURL, username, password):
        for item in self.classes:
            dynClass = item.PCDModule(adminURL, username, password)
            if dynClass.isItMe():
                return dynClass
        return None

    def myClassByName(self, name, adminURL, username, password):
        for item in self.classes:
            if name == item.PCDModule.modName:
                return item.PCDModule(adminURL, username, password)
        return None
