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

    def feed_update(self):
        update = self.client.feed_update_get({'token': self.token})

        if len(update) == 0: update = {}

        id = update.get('aggregator_feed_id')
        url = update.get('feed_url')
        modified = update.get('feed_modified')

        if url:
            from aggregator import get_feed, feed_dump

            logging.info("starting feed update for url (%s) modified in (%s)" % (url, modified))

            dump = feed_dump(get_feed(url, modified))
            status = dump.get('feed_status')
            total_entries = len(dump.get('entries'))

            logging.info("getting feed update returned with status (%s) and (%d) entries" % (status, total_entries))
            updated = self.client.feed_update_post({'token': self.token, 'id': id, 'data': dump})
            logging.info("posting feed with id (%d) updated successfully with (%d) updated articles" % (id, updated))
        else:
            logging.info("no feed to update")

    def blog_publish(self):
        pub = self.client.queue_publication_get({'token': self.token})

        if type(pub)==type(dict()):
            from blog import init_type

            id = int(pub.get('id'))

            t = init_type(pub.get('blog_type'),pub.get('blog_version'))
            t.set_manager_url(pub.get('manager_url'))
            t.username = pub.get('manager_username')
            t.password = pub.get('manager_password')

            logging.info("starting to publish blog entry id (%d)" % (id))

            try:
                p = t.publish({'title'  : pub.get('entry_title'),
                               'content': pub.get('entry_content')})
                self.client.queue_publication_status({'token': self.token, 
                                                      'id': id, 
                                                      'published': True})
                logging.info("post id (%d) published successfully for blog entry id (%d)" % (p, id))
            except:
                logging.info("post failed to publish for blog entry id (%d)" % (id))
                self.client.queue_publication_status({'token': self.token, 
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
