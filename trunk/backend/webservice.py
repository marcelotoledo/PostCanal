class BackendWebService:
    def __init__(self):
        self.token = ""

    def discover_feed(self, args):
        if args['token'] == None or args['url'] == None:
            return None

        if args['token'] != self.token:
            return None

        import sys
        import feedfinder

        feeds = []
        if len(args['url']) > 0:
            feeds = feedfinder.feeds(args['url'])

        result = ""
        for f in feeds:
            result = f + ";"

        return result
