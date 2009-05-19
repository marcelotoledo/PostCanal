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

DEFAULT_UPDATE_TIME = 3600 # 1 hour
MINIMUM_UPDATE_TIME = 300  # 5 minutes

# FEED UPDATE TIME
# 
# default update time for void feeds
# minimum update time for massive updated feeds
# update time is calculated with linear regression of its articles times
# 
def feed_update_time(articles):
    t = DEFAULT_UPDATE_TIME
    l = len(articles)

    if l > 2: # linear regression requires at least 3 points
        from vendor import linreg
        x = range(0, l)
        y = []
        for i in articles: y.append(i.get('article_date', 0))
        y.sort()

        try:
            l = linreg.linreg(x,y) # linear regression
            t = int(l[0])
            r = int(l[2] * 100) / 100.0 # chi square

            # minimum of 5 minutes
            if t <= MINIMUM_UPDATE_TIME : t = MINIMUM_UPDATE_TIME
            # decrease time depending on the regularity
            t = int((t - MINIMUM_UPDATE_TIME) * r) + MINIMUM_UPDATE_TIME
        except:
            t = DEFAULT_UPDATE_TIME

    return t 

def article_dump(article):
    r = {}

    # try to parse article date, otherwise, uses the current time
    _date = article.get('date_parsed')
    if _date: _date = time.mktime(_date)
    if _date == None: _date = time.time()
    r['article_date'] = int(_date)

    # link, title, author
    r['article_link'] = article.get('link', "")
    r['article_title'] = article.get('title', "")
    r['article_author'] = article.get('author', "")

    # content
    _content = article.get('content', "")

    if _content == "": _content = article.get('description', "")
    if _content == "": _content = article.get('summary', "")

    if type(_content) == type(list()):
        _content = _content[0]
        if type(_content).__name__ == 'FeedParserDict':
            _content = _content.value

    r['article_content'] = _content

    return r

def feed_dump(feed):
    r = {}
    r['feed_url'] = feed['url']
    r['feed_title'] = ''
    r['feed_description'] = ''

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

        # articles
        _articles = []
        for i in feed['articles']:
            _articles.append(article_dump(i))
        r['articles'] = _articles

        # update time
        r['feed_update_time'] = feed_update_time(_articles)

    return r

def guess_feeds(url):
    from vendor import feedfinder
    r = []
    f = feedfinder.feeds(url)
    if len(f) == 1:
        r.append(get_feed(f[0]))
    else:
        for i in f:
            r.append({'url': i, 'parsed': None, 'articles': [] })
    return r

def get_feed(url, modified=None):
    from vendor import feedparser

    # sanitizer whitelist
    _wl = ['object','param','embed']
    feedparser._HTMLSanitizer.acceptable_elements.extend(_wl)
    _wl = ['flashvars']
    feedparser._HTMLSanitizer.acceptable_attributes.extend(_wl)

    p = None
    if modified == '': modified = None

    if modified != None and re.search("etag: ", modified):
        p = feedparser.parse(url, etag=re.sub("etag: ", "", modified))
    elif modified != None and len(modified) > 0:
        p = feedparser.parse(url, modified=modified)
    else:
        p = feedparser.parse(url)

    return { 'url': url, 'parsed': p.feed, 'articles': p['entries'] }


if __name__ == '__main__':
    url = "http://www.slashdot.org"
    #url = "http://www.terra.com.br"
    #url = "http://www.cnn.com"
    #url = "rtp.pt"
    #url = "http://wergeeks.wordpress.com/feed/"
    #url = "www.uol.com.br"
    #url = "http://rss.terra.com.br/0,,EI1,00.xml"
    #url = "http://www.bovespa.com.br/rss/"
    
    feeds = []
    for f in guess_feeds(url):
        feeds.append(feed_dump(f))

    print feeds


    #feed = "http://www.gazetaesportiva.net/rss/jogoRapido.xml"
    #d = feed_dump(get_feed(feed))
    #print d
    #print feed_dump(get_feed(feed, d['modified']))
    #print feed_dump(d)
