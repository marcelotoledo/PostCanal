# pcd.py ---  postcanal daemon

# Copyright  (C)  2009  Postcanal Inc. <http://www.postcanal.com>

# Version: 1.0
# Keywords: 
# Author: Marcelo Toledo <marcelo.toledo@postcanal.com>
#         Rafael Castilho <rafael.castilho@postcanal.com>
# Maintainer: Marcelo Toledo <marcelo@marcelotoledo.org>
# URL: http://

# Commentary: 



# Code:

from conf      import runtimeConfig
from utils     import Usage
from feed      import feedUpdate
from publish   import Publish
from autoQueue import autoQueue

import sys
import time
import log

if __name__ == "__main__":
    u = Usage()    
    u.banner()
    u.usage()

    l = log.Log(u.options.verbose, u.options.debug)    

    r = runtimeConfig()
    r.addOption("Debug",      str(u.options.debug))
    r.addOption("Verbose",    str(u.options.verbose))
    r.addOption("token",      r.token)
    r.addOption("Frontend",   r.frontend)
    r.addOption("FrontendWS", r.frontendWS)
    r.printOptions()

    while (True):
        feedUpdate(r.client, r.token)
        #Publish(r.client, r.token)
        #autoQueue(r.client, r.token)
        time.sleep(1)
