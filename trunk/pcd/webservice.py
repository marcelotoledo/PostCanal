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

from conf import runtimeConfig
import blog
import aggregator
        
class WebService:
    def __init__(self, config_path=None):
        r = runtimeConfig(config_path)
        self.token = r.token

    def blog_discover(self, args):
        if not self.validate_args(args, ['url']): return None
        return blog.type_dump(blog.guess_type(args['url']))

    def feed_discover(self, args):
        if not self.validate_args(args, ['url']): return None
        feeds = []
        for f in aggregator.guess_feeds(args['url']):
            feeds.append(aggregator.feed_dump(f))
        return feeds
