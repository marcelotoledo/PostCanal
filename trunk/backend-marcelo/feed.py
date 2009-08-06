# feed.py --- short description

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

import log, sys
from utils import funcName

l = log.Log()

def feedUpdate(client, token):
    try:
        update = client.feed_update_get({ 'token': token })
    except:
        l.log("webservice call failed; (%s)" % (sys.exc_info()[0].__name__), funcName())
        return None

    if type(update) != type(list()):
        l.log("wrong type, expected <list>", funcName())
        return None;
    
    if len(update) == 0:
        l.log("No feeds to update", funcName())
        return None
    
    from aggregator import get_feed, feed_dump
    
    for feed in update:        
        if type(feed) != type(dict()):
            l.log("Feed type is wrong, expected <dict>", funcName())
            return None
        
        try:
            id  = int(feed['id'])
            url = str(feed['feed_url'])
        except:
            l.log("Invalid feed dictionary", funcName())
            return None
        
        l.log("Updating %s" % (url), funcName())
        
        dump = feed_dump(get_feed(url))
        
        if type(dump) != type(dict()):
            l.log("Wrong type for feed dump", funcName())
            return None
        
        status = ""
        total_articles = 0
        saved = 0
        
        try:
            status         = dump['feed_status']
            total_articles = len(dump['articles'])
        except:
            l.log("invalid feed dump dictionary, probably not parsed", funcName())
            
        l.log("%s has %d new entries" %
              (url, total_articles), funcName())
            
        try:
            saved = client.feed_update_post({ 'token' : token, 
                                              'id'    : id, 
                                              'data'  : dump })
        except:
            l.log("feedUpdate post: webservice call failed; (%s)" %
                  (sys.exc_info()[0].__name__), funcName())
            
            if type(saved) != type(int()): saved = 0
            
            l.log("feedUpdate post: feed id (%d) saved (%d) articles" %
                  (id, saved), funcName())
