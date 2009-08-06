# pcd.py ---  postcanal daemon

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

import sys
import time
from conf import runtimeConfig
from utils import Usage
from feed import feedUpdate
from publish import Publish
import log

if __name__ == "__main__":
    u = Usage()    
    u.banner()
    u.usage()

    l = log.Log(u.options.verbose, u.options.debug)    

    r = runtimeConfig()
    r.addOption("Debug", str(u.options.debug))
    r.addOption("Verbose", str(u.options.verbose))
    r.addOption("token", r.token)
    r.addOption("Frontend", r.frontend)
    r.addOption("FrontendWS", r.frontendWS)
    r.printOptions()

    while (True):
        feedUpdate(r.client, r.token)
        Publish(r.client, r.token)
        time.sleep(1)
