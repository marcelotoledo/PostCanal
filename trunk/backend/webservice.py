# webservice.py --- web service client/server for backend system of blotomate

# Copyright  (C)  2009  Rafael Castilho <rafel@castilho.biz>

# Version: 1.0
# Keywords: 
# Author: Rafael Castilho <rafael@castilho.biz>
# Maintainer: Rafael Castilho <rafael@castilho.biz>
# URL: http://

# Commentary: 

# Code:
# client/server class for web services methods

VERSION = "1.0.0"


def validate_args(args, names, token):
    return (((args['token'] == None or args['token'] != token) ^ True) if 'token' in args.keys() else False) and len(filter(lambda n: n in args, names)) == len(names)


class WebService:
    def __init__(self):
        self.token = ""

    # blog discover

    def blog_discover(self, args):
        if not validate_args(args, ['url'], self.token): return None
        import blog
        return blog.type_dictionary(blog.guess_type(args['url']))

    # blog url manager test

    def blog_manager_url_check(self, args):
        if not validate_args(args, ['url', 'type', 'version'], self.token): return None
        import blog
        return blog.type_dictionary(blog.manager_url_check(args['url'], args['type'], args['version']))

    # feed discover

    def feed_discover(self, args):
        if not validate_args(args, ['url'], self.token): return None
        import aggregator
        feeds = []
        for f in aggregator.guess_feeds(args['url']):
            feeds.append(aggregator.feed_dictionary(f))
        return feeds


if __name__ == '__main__':
    token = "c4z5mYW1pYWSJe2BzcIq1wv6n95o1E2kwuD1B0Wuo3XbHx82Vk"

    # blog discover

    # url = "http://test.wordpress.com"
    # ws = WebService()
    # ws.token = token
    # print ws.blog_discover({'token': token, 'url': url})

    # feed discover

    url = "http://www.slashdot.net/"
    ws = WebService()
    ws.token = token
    print ws.feed_discover({'token': token, 'url': url})

    # import xmlrpclib
    # remote = "http://localhost:8080/webservice/backend"
    # server = xmlrpclib.ServerProxy(remote)
    # print server.feed_discover({'token': token, 'url': url})
