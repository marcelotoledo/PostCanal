import sys, os

base_path = os.path.abspath("../")
sys_path = base_path + "/backend"
config_path = base_path + "/config/environment.xml"

sys.path.append(sys_path)

# ==============================================================================

from blotomate import BlotomateConfig

token = BlotomateConfig(config_path).get('webservice/token')

import xmlrpclib

#remote = "http://postcanal.com/backend.py"
remote = "http://192.168.56.101/backend.py"
server = xmlrpclib.ServerProxy(remote)
print server.feed_discover({ 'token' : token, 'url' : 'http://www.slashdot.org' })
