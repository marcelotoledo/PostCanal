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
    def __init__(self):
        self.token = ""

    # blog discover

    def blog_discover(self, args):
        if args['token'] == None or args['url'] == None:
            return None

        if args['token'] != self.token:
            return None
        
        import blogclient
        return blogclient.type_dictionary(blogclient.guess_type(args['url']))

    # blog url admin test

    def blog_check_url_admin(self, args):
        if args['token'] == None or args['url'] == None or args['type'] == None or args['version'] == None: 
           return None

        if args['token'] != self.token:
            return None
 
        import blogclient
        return blogclient.type_dictionary(blogclient.check_url_admin(args['url'], args['type'], args['version']))

    # feed discover

    def feed_discover(self, args):
        if args['token'] == None or args['url'] == None:
            return None

        if args['token'] != self.token:
            return None

        # feed discover with feedfinder

        from vendor import feedfinder

        feeds = []
        if len(args['url']) > 0:
            feeds = feedfinder.feeds(args['url'])

        result = []
        feeds_len = len(feeds)

        # feed parsing with feedparser

        if feeds_len > 0:

            from vendor import feedparser

            for i in range(0, len(feeds)):
                d = feedparser.parse(feeds[i])
                e = []
                for j in d['entries']:
                    e.append({ 'title': j.title,
                               'link': j.link,
                               'description': j.description,
                               'date': j.date if hasattr(j, "date") else "",
                               'id': j.id if hasattr(j, "id") else "" })
                result.append({ 'url': feeds[i],
                                'title': d.feed.title, 
                                'description': d.feed.description,
                                'link': d.feed.link,
                                'date': d.feed.date if hasattr(d.feed, "date") else "",
                                'entries': e })

        return result


# test local

def test(token, url):
    ws = WebService()
    ws.token = token
    return ws.feed_discover({'token': token, 'url': url})

# test remote (with xmlrpc call)

def test_remote(token, url, remote):
    import xmlrpclib
    server = xmlrpclib.ServerProxy(remote)
    return server.feed_discover({'token': token, 'url': url})

if __name__ == '__main__':
    token = "c4z5mYW1pYWSJe2BzcIq1wv6n95o1E2kwuD1B0Wuo3XbHx82Vk"
    #url = "http://slashdot.org/"
    url = "http://www.uol.com.br/"
    print test(token, url)
    # remote = "http://localhost:8080/webservice/backend"
    # print test_remote(token, url, remote)
