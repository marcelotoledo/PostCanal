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
import sys

class Module:
    def __init__(self):
        self.moduleDir        = '/pcd/modules'
        self.ignored          = [ '__init__.py', '__init__.pyc', '.svn' ]
        self.modules          = { }
        self.classes          = [ ]
        sys.path.append(os.path.abspath('../') + self.moduleDir)
        
        self.loadModuleData()

    def loadModuleData(self):
        for item in os.listdir(os.path.abspath('../') + self.moduleDir):
            if not item in self.ignored:
                #self.modules[item] = 'modules.' + item + '.' + item
                self.modules[item] = item + '.' + item
        
    def availableModules(self):
        return self.modules

    def loadModule(self, name, path):
        package  = __import__(path)
        #dynclass = getattr(getattr(package, name), name)
        dynclass = getattr(package, name)

        if dynclass not in self.classes:
            self.classes.append(dynclass)

    def loadAllModules(self):
        for k, v in self.availableModules().items():
            self.loadModule(k, v)

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

    def myContainerName(self, adminURL):
        for item in self.classes:
            # we don't have the user/pass yet, since it's not used, we pass anything
            dynClass = item.PCDModule(adminURL, 'username', 'password')
            if dynClass.isItMe():
                return dynClass.modName
        return None
