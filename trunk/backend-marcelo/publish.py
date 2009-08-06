# publish.py --- short description

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

from utils import funcName
import log

l = log.Log()

def Publish(client, token):
    try:
        publish = client.blog_publish_get({ 'token': token })
    except:
        l.log("Webservice call failed; (%s)" %
              (sys.exc_info()[0].__name__), funcName())
        return None

    if type(publish) != type(list()):
        l.log("Wrong type, expected <list>", funcName())
        return None

    if len(publish) == 0:
        l.log("No entries to publish", funcName())
        return None

    from blog import init_type

    for entry in publish:
        if type(entry) != type(dict()):
            l.log("Wrong type, expected <dict>", funcName())
            return None

        try:
            id            = entry['id']
            blog_type     = entry['blog_type']
            blog_version  = entry['blog_version']
            manager_url   = entry['blog_manager_url']
            blog_username = entry['blog_username']
            blog_password = entry['blog_password']
            entry_title   = entry['entry_title']
            entry_content = entry['entry_content']
        except:
            l.log("Invalid entry dictionary (%s)" %
                  (sys.exc_info()[0].__name__), funcName())

        t = init_type(blog_type, blog_version)

        if t == None:
            l.log("Unknown blog type", funcName())
            return None

        t.set_manager_url(manager_url)
        t.username = blog_username
        t.password = blog_password

        l.log("Preparing to publish for %s" % (id), funcName())

        published = False
        message = ""

        try:
            post_id = t.publish({ 'title'  : entry_title,
                                  'content': entry_content })
            l.log("%s published %s" % (id, str(post_id)), funcName())
            published = True
        except xmlrpclib.Fault, message:
            l.log("Failed to publish (%s) - (%s)" % (id, message), funcName())
        except:
            l.log("Failed to publish (%s) - (%s)" % (id,
                                                     sys.exc_info()[0].__name__), funcName())

        try:
            client.blog_publish_set({ 'token'     : token, 
                                      'id'        : id, 
                                      'published' : published,
                                      'message'   : message })
        except:
            l.log("Failed to set published for %s - %s" % (id, sys.exc_info()[0].__name__), funcName())
            return None
