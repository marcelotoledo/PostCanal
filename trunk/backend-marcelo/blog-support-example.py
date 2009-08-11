# blog-support-example.py --- example how blog supports should be create

# Copyright  (C)  2009  Postcanal Inc. <http://www.postcanal.com>

# Version: 1.0
# Keywords: 
# Author: Marcelo Toledo <marcelo.toledo@postcanal.com>
# Maintainer: Marcelo Toledo <marcelo.toledo@postcanal.com>
# URL: http://

# Commentary: 



# Code:

class BlogExample:
    def __init__(url, adminUrl, username, password):
        pass

    def isItMe():
        "This function returns True or False, and for a clue if this
        blog is support by this class or not"
        pass

    def setTitle(string):
        "Set title"
        pass

    def setContent(string):
        "Set body content"
        pass

    def setTags(list):
        "Set tags"
        pass

    def setAttachment(file):
        "Add attachment"
        pass

    def postEntry():
        "Post entry"
        pass

    def clear():
        "Clear everything"
        pass
