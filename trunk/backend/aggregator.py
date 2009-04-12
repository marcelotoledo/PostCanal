# blogfinder.py --- feed aggregator for backend system of blotomate

# Copyright  (C)  2009  Rafael Castilho <rafel@castilho.biz>

# Version: 1.0
# Keywords: 
# Author: Rafael Castilho <rafael@castilho.biz>
# Maintainer: Rafael Castilho <rafael@castilho.biz>
# URL: http://

# Commentary: 

# Code:
# feed aggregator for backend system of blotomate

VERSION = "1.0.0"


from vendor import feedfinder, feedparser
import re


##feedparser.USER_AGENT = "BLOTOMATE/1.0 +http://blotomate.com"


def item_dictionary(item):
    r = {}
    # date, link, title, author
    r['date'] = item.get('date', "0000-00-00 00:00:00")
    r['link'] = item.get('link', "")
    r['title'] = item.get('title', "")
    r['author'] = item.get('author', "")
    # content
    r['content'] = item.get('content', "")
    if r['content'] == "": r['content'] = item.get('description', "")
    if r['content'] == "": r['content'] = item.get('summary', "")
    return r

def feed_dictionary(feed):
    r = {}
    r['url'] = feed['url']
    # modified | etag
    _modified = feed['parsed'].get('etag', "")
    if _modified != "": _modified = "etag: " + _modified
    r['modified'] = _modified
    if r['modified'] == "": r['modified'] = feed['parsed'].get('modified', "")
    # status
    r['status'] = feed['parsed'].get('status', "200")
    # link, title, description
    r['link'] = feed['parsed'].get('link', "")
    r['title'] = feed['parsed'].get('title', "")
    if r['title'] == "": r['title'] = feed['parsed'].get('subtitle', "")
    r['description'] = feed['parsed'].get('description', "")
    if r['description'] == "": r['description'] = feed['parsed'].get('info', "")
    # entries
    e = []
    for i in feed['entries']:
        e.append(item_dictionary(i))
    r['entries'] = e
    return r

def guess_feeds(url):
    r = []
    for i in feedfinder.feeds(url):
        p = feedparser.parse(i)
        r.append({ 'url': i, 'parsed': p.feed, 'entries': p['entries'] })
    return r

def get_feed(url, modified=None):
    p = None
    if modified != None and re.search("etag: ", modified):
        p = feedparser.parse(url, etag=re.sub("etag: ", "", modified))
    elif modified != None and len(modified) > 0:
        p = feedparser.parse(url, modified=modified)
    else:
        p = feedparser.parse(url)
    return { 'url': url, 'parsed': p.feed, 'entries': p['entries'] }


if __name__ == '__main__':
    url = "http://www.slashdot.org"
    
    feeds = []
    for f in guess_feeds(url):
       feeds.append(feed_dictionary(f))

    print feeds

    # feed = "http://wergeeks.net/feed/"
    # d = feed_dictionary(get_feed(feed))
    # print feed_dictionary(get_feed(feed, d['modified']))
