# blogfinder.py --- feed aggregator for backend system of blotomate

# Copyright  (C)  2009  Rafael Castilho <rafel@castilho.biz>

# Version: 1.0
# Keywords: 
# Author: Rafael Castilho <rafael@castilho.biz>
# Maintainer: Rafael Castilho <rafael@castilho.biz>
# URL: http://

# Commentary: 
# IMPORTANT NOTE! feedfinder does not work properly after feedparser import (???)

# Code:
# feed aggregator for backend system of blotomate

VERSION = "1.0.0"


import re
import time

##feedparser.USER_AGENT = "BLOTOMATE/1.0 +http://blotomate.com"

def feed_update_time(entries):
    t = 3600 # defaults to 1 hour
    l = len(entries)

    if l > 2: # linear regression requires at least 3 points
        from vendor import linreg
        x = range(0, l)
        y = []
        for i in entries: y.append(i.get('item_date', 0))
        y.sort()

        l = linreg.linreg(x,y) # linear regression
        t = int(l[0])
        r = int(l[2] * 100) / 100.0 # chi square

        # minimum of 5 minutes
        if t <= 300 : t = 300
        # decrease time depending on the regularity
        t = int((t - 300) * r) + 300

    return t 

def item_dump(item):
    r = {}
    # date, link, title, author
    _date = item.get('date_parsed', "")
    if _date != "": _date = time.mktime(_date)
    if _date == "": _date = time.time()
    r['item_date'] = int(_date)
    r['item_link'] = item.get('link', "")
    r['item_title'] = item.get('title', "")
    r['item_author'] = item.get('author', "")
    # content
    _content = item.get('content', "")
    if _content == "": _content = item.get('description', "")
    if _content == "": _content = item.get('summary', "")
    r['item_content'] = _content
    return r

def feed_dump(feed):
    r = {}
    r['feed_url'] = feed['url']
    parsed = feed['parsed']
    
    if parsed:
        # modified | etag
        _modified = parsed.get('etag', "")
        if _modified != "": _modified = "etag: " + _modified
        if _modified == "": parsed.get('modified', "")
        r['feed_modified'] = _modified

        # status
        r['feed_status'] = parsed.get('status', "200")

        # link, title, description
        r['feed_link'] = parsed.get('link', "")
        r['feed_title'] = parsed.get('title', "")
        _description = parsed.get('description', "")
        if _description == "": _description = parsed.get('info', "")
        r['feed_description'] = _description

        # entries
        _entries = []
        for i in feed['entries']:
            _entries.append(item_dump(i))
        r['entries'] = _entries

        # update time
        r['feed_update_time'] = feed_update_time(_entries)

    return r

def guess_feeds(url):
    from vendor import feedfinder
    r = []
    f = feedfinder.feeds(url)
    if len(f) == 1:
        from vendor import feedparser
        i = f[0]
        p = feedparser.parse(i)
        r.append({ 'url': i, 'parsed': p.feed, 'entries': p['entries'] })
    else:
        for i in f:
            r.append({'url': i, 'parsed': None, 'entries': [] })
    return r

def get_feed(url, modified=None):
    from vendor import feedparser

    p = None
    if modified == '': modified = None

    if modified != None and re.search("etag: ", modified):
        p = feedparser.parse(url, etag=re.sub("etag: ", "", modified))
    elif modified != None and len(modified) > 0:
        p = feedparser.parse(url, modified=modified)
    else:
        p = feedparser.parse(url)

    return { 'url': url, 'parsed': p.feed, 'entries': p['entries'] }


if __name__ == '__main__':
    #url = "http://www.slashdot.org"
    #url = "http://www.terra.com.br"
    #url = "http://www.cnn.com"
    #url = "rtp.pt"
    #url = "http://wergeeks.wordpress.com/feed/"
    #url = "www.uol.com.br"
    url = "http://rss.terra.com.br/0,,EI1,00.xml"
    
    feeds = []
    for f in guess_feeds(url):
        feeds.append(feed_dump(f))

    print feeds


    #feed = "http://www.gazetaesportiva.net/rss/jogoRapido.xml"
    #d = feed_dump(get_feed(feed))
    #print d
    #print feed_dump(get_feed(feed, d['modified']))
    #print feed_dump(d)
