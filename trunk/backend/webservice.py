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
    return ((args['token'] == None or args['token'] != token) ^ True) and len(filter(lambda n: n in args, names)) == len(names)


class WebService:
    def __init__(self):
        self.token = ""

    # blog discover

    def blog_discover(self, args):
        if not validate_args(args, ['url'], self.token): return None
        import blogclient
        return blogclient.type_dictionary(blogclient.guess_type(args['url']))

    # blog url manager test

    def blog_manager_url_check(self, args):
        if not validate_args(args, ['url', 'type', 'version'], self.token): return None
        import blogclient
        return blogclient.type_dictionary(blogclient.manager_url_check(args['url'], args['type'], args['version']))

    # feed discover

    def feed_discover(self, args):
        if not validate_args(args, ['url'], self.token): return None

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

if __name__ == '__main__':
    token = "c4z5mYW1pYWSJe2BzcIq1wv6n95o1E2kwuD1B0Wuo3XbHx82Vk"

    # blog discover
    url = "http://test.wordpress.com"
    ws = WebService()
    ws.token = token
    print ws.blog_discover({'token': token, 'url': url})

    # feed discover

    # url = "http://www.uol.com.br/"
    # ws = WebService()
    # ws.token = token
    # print ws.feed_discover({'token': token, 'url': url})

    # import xmlrpclib
    # remote = "http://localhost:8080/webservice/backend"
    # server = xmlrpclib.ServerProxy(remote)
    # print server.feed_discover({'token': token, 'url': url})
