# webservice.py --- webservice for backend

# Copyright  (C)  2009  Postcanal Inc. <http://www.postcanal.com>

# Version: 1.0
# Keywords: 
# Author: Marcelo Toledo <marcelo.toledo@postcanal.com>
#         Rafael Castilho <rafael.castilho@postcanal.com>
# Maintainer: Marcelo Toledo <marcelo.toledo@postcanal.com>
# URL: http://


# Commentary: 



# Code:

from conf      import runtimeConfig
from module    import *
from container import *

import blog
import aggregator
        
class WebService:
    def __init__(self, config_path=None):
        self.r     = runtimeConfig(config_path)
        self.token = self.r.token
        m = Module()

#    def validate_args(self, args, names):
#        return (((args['token'] == None or args['token'] != self.token) ^ True) if 'token' in args.keys() else False) and len(filter(lambda n: n in args, names)) == len(names)

    def blog_discover(self, args):
        #        if not self.validate_args(args, ['url']): return None
        #return blog.type_dump(blog.guess_type(args['url']))        
        c = TContainer()
        c.setURL(args['url'])
        
        m.loadAllModules()
        myType = m.myContainerName(args['url'])
                                   
        c.setType(myType)
        c.setURLAccepted(True)
        
        return c.getData()

    def feed_discover(self, args):
#        if not self.validate_args(args, ['url']): return None
        feeds = []
        for f in aggregator.guess_feeds(args['url']):
            feeds.append(aggregator.feed_dump(f))
        return feeds

# if __name__ == '__main__':
#     url = 'http://twitter.com/marcelotoledo'

#     c = TContainer()
#     c.setURL(url)    
    
#     m = Module()
#     m.loadAllModules()
#     myType = m.myContainerName(url)

#     c.setType(myType)
#     c.setURLAccepted(True)
    
#     print c.getData()
