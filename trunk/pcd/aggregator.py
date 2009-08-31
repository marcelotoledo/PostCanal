# blogfinder.py --- feed aggregator for backend system of postcanal

# Copyright  (C)  2009  Postcanal Inc. <http://www.postcanal.com>

# Version: 1.0
# Keywords: 
# Author: Rafael Castilho <rafael@castilho.biz>
# Maintainer: Rafael Castilho <rafael@castilho.biz>
# URL: http://

# Commentary: 
# IMPORTANT NOTE! feedfinder does not work properly after feedparser import (???)

# Code:
# feed aggregator for backend system of postcanal


import re
import time

##feedparser.USER_AGENT = "POSTCANAL/1.0 +http://postcanal.com"

DEFAULT_UPDATE_TIME = 600
MINIMUM_UPDATE_TIME = 60
MAXIMUM_UPDATE_TIME = DEFAULT_UPDATE_TIME

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
            if t <= MINIMUM_UPDATE_TIME:
                t = MINIMUM_UPDATE_TIME
                
            # decrease time depending on the regularity
            t = int((t - MINIMUM_UPDATE_TIME) * r) + MINIMUM_UPDATE_TIME
        except:
            t = DEFAULT_UPDATE_TIME

    if t >= MAXIMUM_UPDATE_TIME:
        t = MAXIMUM_UPDATE_TIME

    return t 

def article_dump(article):
    r = {}

    # try to parse article date, otherwise, uses the current time
    _date = article.get('date_parsed')
    if _date: _date = time.mktime(_date)
    if _date == None: _date = time.time()
    r['article_date'] = int(_date - time.timezone)

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
    r['feed_status'] = feed['status']
    r['feed_link'] = ""
    r['feed_title'] = ""
    r['feed_description'] = ""
    r['articles'] = []
    r['feed_update_time'] = 0

    parsed = feed['parsed']
    
    if parsed:
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
    f = []

    try:
        f = feedfinder.feeds(url)
    except:
        pass

    if len(f) == 1:
        r.append(get_feed(f[0]))
    else:
        for i in f:
            r.append({'url': i, 'parsed': None, 'articles': [], 'status': "100" })
    return r

def get_feed(url):
    from vendor import feedparser

    # sanitizer whitelist
    _wl = ['object','param','embed']
    feedparser._HTMLSanitizer.acceptable_elements.extend(_wl)
    _wl = ['flashvars']
    feedparser._HTMLSanitizer.acceptable_attributes.extend(_wl)

    result = { 'url': url, 'parsed': None, 'articles': [], 'status': "404" }

    try:
        parsed = feedparser.parse(url)
        if parsed.feed:
            result['parsed'] = parsed.feed
            result['articles'] = parsed['entries']
            result['status'] = "200"
    except:
        result['status'] = "500"

    return result
