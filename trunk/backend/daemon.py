# -*- coding: utf-8 -*-
# daemon.py --- daemon of the backend system of blotomate

# Copyright  (C)  2009  Rafael Castilho <rafael@castilho.biz>

# Version: 1.0
# Keywords: 
# Author: Rafael Castilho <rafael@castilho.biz>
# Maintainer: Rafael Castilho <rafael@castilho.biz>
# URL: http://

# Commentary: 

# Code:
# Run background routines

VERSION = "1.0.0"

import sys, os, time, logging, xmlrpclib

TIME_SLEEP = 1

class Daemon:
    def __init__(self, config_path=None):
        from blotomate import BlotomateConfig
        config = BlotomateConfig(config_path)

        self.token = config.get('webservice/token')
        frontend_url = config.get('base/url')
        frontend_url = frontend_url + config.get('webservice/frontendUrl')
        self.client = xmlrpclib.ServerProxy(frontend_url)

        self.feed_update_get_limit = int(config.get('backend/feedUpdateGet/limit'))

    def feed_update(self):
        update = self.client.feed_update_get({ 'token': self.token,
                                               'limit': self.feed_update_get_limit })

        if type(update) != type(list()):
            logging.error("feed update get: wrong type, expected <list>")
            pass

        if len(update) == 0:
            logging.info("feed update get: no feeds")
            pass

        from aggregator import get_feed, feed_dump

        for feed in update:

            if type(feed) != type(dict()):
                logging.error("feed update get: wrong type, expected <dict>")
                pass

            try:
                id       = int(feed['aggregator_feed_id'])
                url      = str(feed['feed_url'])
                modified = str(feed['feed_modified'])
            except:
                logging.error("feed update get: invalid feed dictionary")
                pass

            _m = "feed update post: started for url (%s) modified in (%s)"
            logging.info(_m % (url, modified))

            dump = feed_dump(get_feed(url, modified))

            if type(dump) != type(dict()):
                logging.error("feed update post: wrong type for feed dump")
                pass

            try:
                status         = dump['feed_status']
                total_articles = len(dump['articles'])
            except:
                logging.error("feed update post: invalid feed dump dictionary")
                pass

            _m = "feed update post: feed dump returned status (%s) and (%d) articles"
            logging.info(_m % (status, total_articles))

            updated = self.client.feed_update_post({ 'token' : self.token, 
                                                     'id'    : id, 
                                                     'data'  : dump })

            if type(updated) != type(int()): updated = 0

            _m = "feed update post: feed id (%d) updated (%d) articles"
            logging.info(_m % (id, updated))

    def blog_publish(self):
        pub = self.client.blog_publish_get({'token': self.token})

        if type(pub)==type(dict()):
            from blog import init_type

            id = int(pub.get('id'))

            t = init_type(pub.get('blog_type'),pub.get('blog_version'))
            t.set_manager_url(pub.get('blog_manager_url'))
            t.username = pub.get('blog_username')
            t.password = pub.get('blog_password')

            logging.info("starting to publish blog entry id (%d)" % (id))

            try:
                p = t.publish({'title'  : pub.get('entry_title'),
                               'content': pub.get('entry_content')})
                self.client.blog_publish_set({'token': self.token, 
                                              'id': id, 
                                              'published': True})
                logging.info("post id (%d) published successfully for blog entry id (%d)" % (p, id))
            except:
                logging.info("post failed to publish for blog entry id (%d)" % (id))
                self.client.blog_publish_set({'token': self.token, 
                                              'id': id, 
                                              'published': False})
        else:
            logging.info("no blog entry to publish")

def start(argv):
    base_path = os.path.abspath("../")
    config_path = base_path + "/config/environment.xml"
    daemon = Daemon(config_path)

    # logger
    logging.basicConfig(level=logging.DEBUG, 
                        format="[%(asctime)s] [%(levelname)s] %(message)s")

    logging.info("daemon started")

    while True:
        time.sleep(TIME_SLEEP)
        daemon.feed_update()
        daemon.blog_publish()

def usage(argv):
    print 'Blotomate Daemon %s - Daemon system for blotomate.com' % VERSION
    print 'Copyright  (C)  2009 Blotomate Inc. <https://www.blotomate.com>\n'
    
    print 'Usage: %s start|test' % argv[0]

def test(argv):
    pass

if __name__ == "__main__":
    if len(sys.argv) <= 1:
        usage(sys.argv)
        sys.exit(-1)

    if sys.argv[1] == 'start':
        start(sys.argv)
