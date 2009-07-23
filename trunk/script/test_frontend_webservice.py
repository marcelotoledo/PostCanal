import sys, os

base_path = os.path.abspath("../")
sys_path = base_path + "/backend"
sys.path.append(sys_path)

# ==============================================================================

from postcanal import PostCanalConfig

config_path = base_path + "/config/environment.xml"
token = PostCanalConfig(config_path).get('webservice/token')

import xmlrpclib

remote = "%s%s" % (PostCanalConfig(config_path).get('base/url'),
                   PostCanalConfig(config_path).get('webservice/frontendUrl'))
server = xmlrpclib.ServerProxy(remote)

print server.feed_update_get({ 'token' : token, 'total' : 3 })
