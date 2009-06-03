# -*- coding: utf-8 -*-
# daemon.py --- daemon of the backend system of postcanal

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
        from postcanal import PostCanalConfig
        config = PostCanalConfig(config_path)

        self.token = config.get('webservice/token')
        frontend_url = config.get('base/url')
        frontend_url = frontend_url + config.get('webservice/frontendUrl')
        self.client = xmlrpclib.ServerProxy(frontend_url)

    def feed_update(self):
        update = self.client.feed_update_get({ 'token': self.token })

        if type(update) != type(list()):
            logging.error("feed update get: wrong type, expected <list>")
            return None;

        if len(update) == 0:
            logging.info("feed update get: no feeds")
            return None

        from aggregator import get_feed, feed_dump

        for feed in update:

            if type(feed) != type(dict()):
                logging.error("feed update get: wrong type, expected <dict>")
                return None

            try:
                id       = int(feed['aggregator_feed_id'])
                url      = str(feed['feed_url'])
                modified = str(feed['feed_modified'])
            except:
                logging.error("feed update get: invalid feed dictionary")
                return None

            _m = "feed update post: started for url (%s) modified in (%s)"
            logging.info(_m % (url, modified))

            dump = feed_dump(get_feed(url, modified))

            if type(dump) != type(dict()):
                logging.error("feed update post: wrong type for feed dump")
                return None

            status = ""
            total_articles = 0
            updated = 0

            try:
                status         = dump['feed_status']
                total_articles = len(dump['articles'])
            except:
                logging.error("feed update post: invalid feed dump dictionary, probably not parsed")

            _m = "feed update post: feed dump returned status (%s) and (%d) articles"
            logging.info(_m % (status, total_articles))

            updated = self.client.feed_update_post({ 'token' : self.token, 
                                                     'id'    : id, 
                                                     'data'  : dump })

            if type(updated) != type(int()): updated = 0

            _m = "feed update post: feed id (%d) updated (%d) articles"
            logging.info(_m % (id, updated))

    def blog_publish(self):
        publish = self.client.blog_publish_get({ 'token': self.token })

        if type(publish) != type(list()):
            logging.error("blog publish get: wrong type, expected <list>")
            return None

        if len(publish) == 0:
            logging.info("blog publish get: no entries")
            return None

        from blog import init_type

        for entry in publish:

            if type(entry) != type(dict()):
                logging.error("blog publish get: wrong type, expected <dict>")
                return None

            try:
                id            = int(entry['id'])
                blog_type     = str(entry['blog_type'])
                blog_version  = str(entry['blog_version'])
                manager_url   = str(entry['blog_manager_url'])
                blog_username = str(entry['blog_username'])
                blog_password = str(entry['blog_password'])
                entry_title   = str(entry['entry_title'])
                entry_content = str(entry['entry_content'])
            except:
                err = sys.exc_info()[0].__name__
                logging.warning("blog publish get: invalid entry dictionary (%s)" % (err))

            t = init_type(blog_type, blog_version)

            if t == None:
                logging.error("blog publish get: unknown blog type")
                return None

            t.set_manager_url(manager_url)
            t.username = blog_username
            t.password = blog_password

            _m = "blog publish post: started for blog entry id (%d)"
            logging.info(_m % (id))

            published = False
            message = ""

            try:
                post_id = t.publish({ 'title'  : entry_title,
                                      'content': entry_content })
                _m = "blog publish post: post id (%d) "
                _m = _m + "published successfully for blog entry id (%d)"
                logging.info(_m % (int(post_id), id))
                published = True
            except xmlrpclib.Fault, message:
                _m = "blog publish post: failed to publish for blog entry id (%d); (%s)"
                logging.warning(_m % (id, message))
            except:
                _m = "blog publish post: failed to publish for blog entry id (%d); (%s)"
                logging.error(_m % (id, sys.exc_info()[0].__name__))

            self.client.blog_publish_set({ 'token'     : self.token, 
                                           'id'        : id, 
                                           'published' : published,
                                           'message'   : message })

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
    print 'PostCanal Daemon %s - Daemon system for postcanal.com' % VERSION
    print 'Copyright  (C)  2009 PostCanal Inc. <https://www.postcanal.com>\n'
    
    print 'Usage: %s start|test' % argv[0]

def test(argv):
    pass

if __name__ == "__main__":
    if len(sys.argv) <= 1:
        usage(sys.argv)
        sys.exit(-1)

    if sys.argv[1] == 'start':
        start(sys.argv)
