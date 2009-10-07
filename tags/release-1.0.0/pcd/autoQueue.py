# autoQueue.py --- short description

# Copyright  (C)  2009  Postcanal Inc. <http://www.postcanal.com>

# Version: 1.0
# Keywords: 
# Author: Marcelo Toledo <marcelo.toledo@postcanal.com>
#         Rafael Castilho <rafael.castilho@postcanal.com>
# Maintainer: Marcelo Toledo <marcelo@marcelotoledo.org>
# URL: http://

# Commentary: 



# Code:

import log
import sys
from utils import funcName

l = log.Log()

def autoQueue(client, token):
    i = 0
    try:
        i = client.queue_suggest_do({ 'token': token })
        if i > 0:
            l.log("Enqueued for %s" % (i), funcName())
    except:
        l.log("Failed for %s - (%s)" % (i, sys.exc_info()[0].__name__), funcName())
        return None
