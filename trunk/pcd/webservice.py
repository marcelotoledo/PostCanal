# webservice.py --- web service client/server for backend system of postcanal

# Copyright  (C)  2009  Rafael Castilho <rafel@castilho.biz>

# Version: 1.0
# Keywords: 
# Author: Rafael Castilho <rafael@castilho.biz>
# Maintainer: Rafael Castilho <rafael@castilho.biz>
# URL: http://

# Commentary: 

# Code:
# client/server class for web services methods

from postcanal import PostCanalConfig
import blog
import aggregator
        
class WebService:

    def __init__(self, config_path=None):
        self.token = PostCanalConfig(config_path).get('webservice/token')
        #from postcanal import PostCanalConfig
        #self.token = PostCanalConfig(config_path).get('webservice/token')
        from conf import runtimeConfig
        r = runtimeConfig(config_path)
        self.token = r.token

    # validate args

    def validate_args(self, args, names):
        return (((args['token'] == None or args['token'] != self.token) ^ True) if 'token' in args.keys() else False) and len(filter(lambda n: n in args, names)) == len(names)

    # blog discover

    def blog_discover(self, args):
        if not self.validate_args(args, ['url']): return None
        return blog.type_dump(blog.guess_type(args['url']))

    # blog url manager check

    def blog_manager_url_check(self, args):
        if not self.validate_args(args, ['url', 'type', 'version']): return None
        return blog.type_dump(blog.manager_url_check(args['url'], args['type'], args['version']))

    # blog publication check

    def blog_login_check(self, args):
        if not self.validate_args(args, ['url', 'type', 'version', 'username', 'password']): return None
        return blog.type_dump(blog.login_check(args['url'], args['type'], args['version'], args['username'], args['password']))

    # blog publication check

    def blog_publication_check(self, args):
        if not self.validate_args(args, ['url', 'type', 'version', 'username', 'password']): return None
        return blog.type_dump(blog.publication_check(args['url'], args['type'], args['version'], args['username'], args['password']))

    # feed discover

    def feed_discover(self, args):
        if not self.validate_args(args, ['url']): return None
        feeds = []
        for f in aggregator.guess_feeds(args['url']):
            feeds.append(aggregator.feed_dump(f))
        return feeds
