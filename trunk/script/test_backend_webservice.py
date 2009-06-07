import sys, os

base_path = os.path.abspath("../")
sys_path = base_path + "/backend"
sys.path.append(sys_path)

# ==============================================================================

from postcanal import PostCanalConfig

config_path = base_path + "/config/environment.xml"
token = PostCanalConfig(config_path).get('webservice/token')

import xmlrpclib

remote = "http://192.168.56.101/backend.py"
server = xmlrpclib.ServerProxy(remote)


# print "---------------------------------------------------------------------------------"
# print "blog discover"
# print "---------------------------------------------------------------------------------"
# url = "http://test.wordpress.com"
# result = server.blog_discover({ 'token' : token, 'url' : url })
# print "url = %s" % (url)
# print "url accepted = %s" % (result['url_accepted'])
# print "type = %s" % (result['type'])
# print "type = %s" % (result['version'])
# url = "http://politicalticker.blogs.cnn.com"
# result = server.blog_discover({ 'token' : token, 'url' : url })
# print "url = %s" % (url)
# print "url accepted = %s" % (result['url_accepted'])
# print "type = %s" % (result['type'])
# print "type = %s" % (result['version'])
# 
# print "---------------------------------------------------------------------------------"
# print "blog manager url check"
# print "---------------------------------------------------------------------------------"
# manager_url = "http://test.wordpress.com/xmlrpc.php"
# result = server.blog_manager_url_check({ 'token' : token, 'url' : url , 'type' : 'wordpress', 'version' : 'wordpress_com' })
# print "manager url = %s" % (manager_url)
# print "manager url accepted = %s" % (result['manager_url_accepted'])
# 
# manager_url = "http://politicalticker.blogs.cnn.com/xmlrpc.php"
# result = server.blog_manager_url_check({ 'token' : token, 'url' : url, 'type' : 'wordpress', 'version' : 'wordpress_domain' })
# print "manager url = %s" % (manager_url)
# print "manager url accepted = %s" % (result['manager_url_accepted'])
# 
# print "---------------------------------------------------------------------------------"
# print "blog login check"
# print "---------------------------------------------------------------------------------"
# result = server.blog_login_check({ 'token' : token, 'url' : 'http://castilho1024.wordpress.com/xmlrpc.php', 'type' : 'wordpress', 'version' : 'wordpress_domain', 'username' : 'castilho1024', 'password' : 'castilho' })
# print "login accepted = %s" % (result['login_accepted'])
# 
# print "---------------------------------------------------------------------------------"
# print "blog publication check"
# print "---------------------------------------------------------------------------------"
# result = server.blog_publication_check({ 'token' : token, 'url' : 'http://castilho1024.wordpress.com/xmlrpc.php', 'type' : 'wordpress', 'version' : 'wordpress_domain', 'username' : 'castilho1024', 'password' : 'castilho' })
# print "publication accepted = %s" % (result['publication_accepted'])

def print_feed_discover_result(r):
    print "feed url = %s" % (r['feed_url'])
    print "feed title = %s" % (r['feed_title'])
    print "feed description = %s" % (r['feed_description'])
    print "feed status = %s" % (r['feed_status'])
    print "feed link = %s" % (r['feed_link'])
    print "feed title = %s" % (r['feed_title'])
    print "feed description = %s" % (r['feed_description'])
    print "len(articles) = %d" % (len(r['articles']))
    print "feed update time = %d" % (r['feed_update_time'])

print "---------------------------------------------------------------------------------"
print "feed discover"
print "---------------------------------------------------------------------------------"
url = "http://www.google.com"
for r in server.feed_discover({ 'token' : token, 'url' : url }):
    print_feed_discover_result(r)
print ""
url = "http://www.slashdot.org"
for r in server.feed_discover({ 'token' : token, 'url' : url }):
    print_feed_discover_result(r)
print ""
url = "http://www.cnn.com"
for r in server.feed_discover({ 'token' : token, 'url' : url }):
    print_feed_discover_result(r)
print ""
url = "http://br-linux.org/feed/rss/"
for r in server.feed_discover({ 'token' : token, 'url' : url }):
    print_feed_discover_result(r)
print ""
url = "http://www.bovespa.com.br/rss/"
for r in server.feed_discover({ 'token' : token, 'url' : url }):
    print_feed_discover_result(r)
