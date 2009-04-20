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

        self.token = config.get('application/webservice/token')
        frontend_url = config.get('base/url')
        frontend_url = frontend_url + config.get('application/webservice/frontend/url')
        self.client = xmlrpclib.ServerProxy(frontend_url)

    def feed_update(self):
        update = self.client.feed_update_get({'token': self.token})

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

            if self.client.feed_update_post({'token': self.token, 'id': id, 'data': dump}):
                logging.info("posting feed update successfully with id (%d)" % (id))
            else:
                logging.error("posting feed update failed width id (%d)" % (id))
        else:
            logging.warn("no feed to update")

def start(argv):
    cwd = os.getcwd()
    config_path = cwd.replace("backend", "") + "config/environment.xml"
    daemon = Daemon(config_path)

    # logger
    logging.basicConfig(level=logging.DEBUG, 
                        format="[%(asctime)s] [%(levelname)s] %(message)s")

    logging.info("daemon started")

    while True:
        time.sleep(TIME_SLEEP)
        daemon.feed_update()
        sys.exit(-1)

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
