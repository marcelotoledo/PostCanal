#
# This is just an example of how to use the implement class, bot all
# methods are used and there might be errors as it's just an example and
# was not tested.
#

b = BlogExample('blog.marcelotoledo.org', 'blog.marcelotoledo.org/wp-admin', 'username', 'password')

if b.isItMe() == True:
    if b.authenticate() == False:
        print "Authentication failed"
        sys.exit()

    print "My available tags:"
    for tag in getTags():
        print tag

    print "My available categories:"
    for cat in getCategories():
        print cat

    b.setTitle('my post title')
    b.setContent('my post content body')

    # technology exists and was retrieved through getCategories, but
    # business doesn't exist so behind the scenes it must be added to
    # the system and linked with this post somehow
    catList = ['technology', 'business']    
    b.setCategories(catList)

    # The same might happen here if a tag is new
    tagsList = ['iphone', 'apple']
    b.setTags(tagsList)

    b.setAttachment('/home/marcelo/file.jpg')
    
    if b.postEntry() == True:
        print "Article posted!"
    else:
        print "Article NOT posted!"
