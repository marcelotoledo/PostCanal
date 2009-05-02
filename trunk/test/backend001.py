import sys, os

base_path = os.path.abspath("../")
sys_path = base_path + "/backend"
config_path = base_path + "/config/environment.xml"

sys.path.append(sys_path)

# ==============================================================================

from blotomate import BlotomateConfig
token = BlotomateConfig(config_path).get('webservice/token')

from webservice import WebService

url = "castilho"
ws = WebService(config_path)
ws.token = token
print ws.blog_discover({'token': token, 'url': url})


# from vendor import feedfinder
# url = "http://www.youtube.com/mdtoledo"
# print feedfinder.feeds(url)

#from vendor import linreg
#x = [1, 2, 3]
#y = [412, 238421, 312903]
#l = linreg.linreg(x,y)
#print "%d %d" % (int(l[0]), int(l[2] * 100))

#import xmlrpclib
#remote = "http://localhost:8080/webservice/frontend"
#server = xmlrpclib.ServerProxy(remote)
#print server.queue_publication_get({'token': "c4z5mYW1pYWSJe2BzcIq1wv6n95o1E2kwuD1B0Wuo3XbHx82Vk"})
#
#import xmlrpclib  
#server = xmlrpclib.ServerProxy( "http://castilho1024.wordpress.com/xmlrpc.php" )
#post = server.metaWeblog.getPost(1, "castilho1024", "castilho" )
#print post

#from vendor import wordpresslib
#url = 'http://castilho1024.wordpress.com/xmlrpc.php'
#wp = wordpresslib.WordPressClient(url, 'castilho1024', 'castilho')
#wp.selectBlog(0)
#post = wordpresslib.WordPressPost()
#post.title = 'Post title'
#post.description = 'Post content'
#idPost = wp.newPost(post, True)
