'''Demo program which uses the live-journal module.'''

import sys
from live_journal import LiveJournal

#user's blog address: http://petertest.livejournal.com/;
#username='petertest', password='peter2win'
b = LiveJournal('http://petertest.livejournal.com/', None, 'petertest', 'peter2win')

if not b.isItMe():
    print 'isItMe() False: invalid url'
    sys.exit()

if not b.authenticate():
    print "Authentication failed"
    sys.exit()

print "My available tags:"
for tag in b.getTags():
    print tag

print "My available categories:"
for cat in b.getCategories():
    print cat

b.setTitle('final title 1')
b.setContent('final content 1')

# technology exists and was retrieved through getCategories, but
# business doesn't exist so behind the scenes it must be added to
# the system and linked with this post somehow
catList = ['technology', 'business']
b.setCategories(catList)

# The same might happen here if a tag is new
tagsList = ['iphone', 'apple']
b.setTags(tagsList)

b.setAttachment('/home/marcelo/file.jpg')

if b.postEntry():
    print "Article posted!"
else:
    print "Article NOT posted!"

b.clear()
b.setTitle('final title 2')
b.setContent('final content 2')
if b.postEntry():
    print "Article posted!"
else:
    print "Article NOT posted!"
