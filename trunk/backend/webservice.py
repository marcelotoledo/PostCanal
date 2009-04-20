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

class WebService:
    def __init__(self, config_path=None):
        from blotomate import BlotomateConfig
        self.token = BlotomateConfig(config_path).get('application/webservice/token')

    # validate args

    def validate_args(self, args, names):
        return (((args['token'] == None or args['token'] != self.token) ^ True) if 'token' in args.keys() else False) and len(filter(lambda n: n in args, names)) == len(names)

    # blog discover

    def blog_discover(self, args):
        if not self.validate_args(args, ['url']): return None
        import blog
        return blog.type_dump(blog.guess_type(args['url']))

    # blog url manager test

    def blog_manager_url_check(self, args):
        if not self.validate_args(args, ['url', 'type', 'version']): return None
        import blog
        return blog.type_dump(blog.manager_url_check(args['url'], args['type'], args['version']))

    # feed discover

    def feed_discover(self, args):
        if not self.validate_args(args, ['url']): return None
        import aggregator
        feeds = []
        for f in aggregator.guess_feeds(args['url']):
            feeds.append(aggregator.feed_dump(f))
        return feeds

def usage(argv):
    print 'Blotomate Backend %s - WebService system for blotomate.com' % VERSION
    print 'Copyright  (C)  2009 Blotomate Inc. <https://www.blotomate.com>\n'
    
    print 'Usage: %s test' % argv[0]

def test(argv):
    from blotomate import BlotomateConfig
    token = BlotomateConfig().get('application/webservice/token')

    # blog discover

    # url = "http://test.wordpress.com"
    # ws = WebService()
    # ws.token = token
    # print ws.blog_discover({'token': token, 'url': url})

    # feed discover

    url = "http://www.slashdot.org/"
    # ws = WebService()
    # ws.token = token
    # print ws.feed_discover({'token': token, 'url': url})

    import xmlrpclib
    remote = "http://localhost:8080/webservice/backend"
    server = xmlrpclib.ServerProxy(remote)
    print server.feed_discover({'token': token, 'url': url})

if __name__ == '__main__':
    import sys

    if len(sys.argv) <= 1:
        usage(sys.argv)
        sys.exit(-1)

    if sys.argv[1] == 'test':
        test(sys.argv)
