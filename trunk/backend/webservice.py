# webservice.py --- web service client/server of backend system of blotomate

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

    # discover feeds

    def discover_feeds(self, args):
        if args['token'] == None or args['url'] == None:
            return None

        if args['token'] != self.token:
            return None

        # discover feeds with feedfinder

        import feedfinder

        feeds = []
        if len(args['url']) > 0:
            feeds = feedfinder.feeds(args['url'])

        result = {}
        feeds_len = len(feeds)

        # parse feeds with feedparser

        if feeds_len > 0:

            import feedparser

            for i in range(0, len(feeds)):
                d = feedparser.parse(feeds[i])
                result['feed_' + str(i)] = { "title": d.feed.title, "url": feeds[i] }

        return result


# test local

def test(token, url):
    ws = WebService()
    ws.token = token
    return ws.discover_feeds({'token': token, 'url': url})

# test remote (with xmlrpc call)

def test_remote(token, url, remote):
    import xmlrpclib
    server = xmlrpclib.ServerProxy(remote)
    return server.discover_feeds({'token': token, 'url': url})

if __name__ == '__main__':
    token = "c4z5mYW1pYWSJe2BzcIq1wv6n95o1E2kwuD1B0Wuo3XbHx82Vk"
    url = "http://slashdot.org/"
    print test(token, url)
    remote = "http://localhost:8080/webservice/backend"
    print test_remote(token, url, remote)
