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

TIME_SLEEP = 3

class Daemon:
    def __init__(self, config_path=None):
        from postcanal import PostCanalConfig
        config = PostCanalConfig(config_path)

        self.token = config.get('webservice/token')
        frontend_url = config.get('base/url')
        frontend_url = frontend_url + config.get('webservice/frontendUrl')
        self.client = xmlrpclib.ServerProxy(frontend_url)

    def feed_update(self):
        try:
            update = self.client.feed_update_get({ 'token': self.token })
        except:
            _m = "feed update get: webservice call failed; (%s)"
            logging.error(_m % (sys.exc_info()[0].__name__))
            return None

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
                id  = int(feed['id'])
                url = str(feed['feed_url'])
            except:
                logging.error("feed update get: invalid feed dictionary")
                return None

            _m = "feed update post: started for url (%s)"
            logging.info(_m % (url))

            dump = feed_dump(get_feed(url))

            if type(dump) != type(dict()):
                logging.error("feed update post: wrong type for feed dump")
                return None

            status = ""
            total_articles = 0
            saved = 0

            try:
                status         = dump['feed_status']
                total_articles = len(dump['articles'])
            except:
                logging.error("feed update post: invalid feed dump dictionary, probably not parsed")

            _m = "feed update post: feed dump returned status (%s) and (%d) articles"
            logging.info(_m % (status, total_articles))

            try:
                saved = self.client.feed_update_post({ 'token' : self.token, 
                                                       'id'    : id, 
                                                       'data'  : dump })
            except:
                _m = "feed update post: webservice call failed; (%s)"
                logging.error(_m % (sys.exc_info()[0].__name__))

            if type(saved) != type(int()): saved = 0

            _m = "feed update post: feed id (%d) saved (%d) articles"
            logging.info(_m % (id, saved))

    def blog_publish(self):
        try:
            publish = self.client.blog_publish_get({ 'token': self.token })
        except:
            _m = "blog publish get: webservice call failed; (%s)"
            logging.error(_m % (sys.exc_info()[0].__name__))
            return None

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
                id            = entry['id']
                blog_type     = entry['blog_type']
                blog_version  = entry['blog_version']
                manager_url   = entry['blog_manager_url']
                blog_username = entry['blog_username']
                blog_password = entry['blog_password']
                entry_title   = entry['entry_title']
                entry_content = entry['entry_content']
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

            _m = "blog publish post: started for blog entry id (%s)"
            logging.info(_m % (id))

            published = False
            message = ""

            try:
                post_id = t.publish({ 'title'  : entry_title,
                                      'content': entry_content })
                _m = "blog publish post: post id (%s) "
                _m = _m + "published successfully for blog entry id (%s)"
                logging.info(_m % (str(post_id), id))
                published = True
            except xmlrpclib.Fault, message:
                _m = "blog publish post: failed to publish blog entry id (%s); (%s)"
                logging.warning(_m % (id, message))
            except:
                _m = "blog publish post: failed to publish blog entry id (%s); (%s)"
                logging.error(_m % (id, sys.exc_info()[0].__name__))

            try:
                self.client.blog_publish_set({ 'token'     : self.token, 
                                               'id'        : id, 
                                               'published' : published,
                                               'message'   : message })
            except:
                _m = "blog publish post: webservice call failed; (%s)"
                logging.error(_m % (sys.exc_info()[0].__name__))
                return None

    def queue_suggest(self):
        i = 0
        try:
            i = self.client.queue_suggest_do({ 'token': self.token })
            _m = "queue suggest: called successfuly for blog id (%s)"
            logging.info(_m % (i))
        except:
            _m = "queue suggest: called failed for blog id (%s); (%s)"
            logging.error(_m % (i, sys.exc_info()[0].__name__))
            return None


def start(argv):
    base_path = os.path.abspath("../")
    config_path = base_path + "/config/environment.xml"
    daemon = Daemon(config_path)

    # logger
    logging.basicConfig(level=logging.DEBUG, 
                        format="[%(asctime)s] [%(levelname)s] %(message)s",
                        filename="./daemon.log")

    logging.info("daemon started")

    while True:
        time.sleep(TIME_SLEEP)
        daemon.feed_update()
        daemon.blog_publish()
        daemon.queue_suggest()

def debug(argv): # run once
    base_path = os.path.abspath("../")
    config_path = base_path + "/config/environment.xml"
    daemon = Daemon(config_path)

    # logger (stdout)
    logging.basicConfig(level=logging.DEBUG, 
                        format="[%(asctime)s] [%(levelname)s] %(message)s")

    logging.info("debug started")

    daemon.feed_update()
    daemon.blog_publish()
    daemon.queue_suggest()

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

    if sys.argv[1] == 'debug':
        debug(sys.argv)
