# -*- coding: utf-8 -*-
# htmlcrawler.py --- html keyword crawler for backend system of postcanal

# Copyright  (C)  2009  Rafael Castilho <rafel@castilho.biz>

# Version: 1.0
# Keywords: 
# Author: Rafael Castilho <rafael@castilho.biz>
# Maintainer: Rafael Castilho <rafael@castilho.biz>
# URL: http://

# Commentary: 

# Code:
# html keyword crawler


def strip_ml_tags(in_text):
    s_list = list(in_text)
    i,j = 0,0
    while i < len(s_list):
        if s_list[i] == '<':
            while s_list[i] != '>':
                s_list.pop(i)
            s_list[i] = " "
        else:
            i=i+1
    join_char=''
    return join_char.join(s_list)

def strip_accents(s):
    t = { 192: u'A', 193: u'A', 194: u'A', 195: u'A', 196: u'A', 197: u'A',
          199: u'C', 200: u'E', 201: u'E', 202: u'E', 203: u'E', 204: u'I',
          205: u'I', 206: u'I', 207: u'I', 209: u'N', 210: u'O', 211: u'O',
          212: u'O', 213: u'O', 214: u'O', 216: u'O', 217: u'U', 218: u'U',
          219: u'U', 220: u'U', 221: u'Y', 224: u'a', 225: u'a', 226: u'a',
          227: u'a', 228: u'a', 229: u'a', 231: u'c', 232: u'e', 233: u'e',
          234: u'e', 235: u'e', 236: u'i', 237: u'i', 238: u'i', 239: u'i',
          241: u'n', 242: u'o', 243: u'o', 244: u'o', 245: u'o', 246: u'o',
          248: u'o', 249: u'u', 250: u'u', 251: u'u', 252: u'u', 253: u'y',
          255: u'y'
    }
    #return unicode(s, 'utf-8').translate(t)
    return s.translate(t)

def htmlcrawler(html):
    import re
    from vendor.html2text import html2text

    s = strip_ml_tags(html);
    s = html2text(s)
    s = strip_accents(s)
    s = s.lower()
    p = re.compile('[^\w]+')
    s = p.sub(' ', s)

    l = []

    for w in set(s.rsplit(" ")):
        if(len(w)>2):
            l.append(w)

    return l


if __name__ == '__main__':
    import aggregator

    #f = aggregator.get_feed("http://rss.slashdot.org/Slashdot/slashdot")
    f = aggregator.get_feed("http://br-linux.org/feed/")
    a = aggregator.article_dump(f['articles'][0])
    l = htmlcrawler(a['article_content'])

    print l
