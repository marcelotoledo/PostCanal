''' This module contains the MSN class.'''

import xmlrpclib
import urllib
import sys
import smtplib

class MSNEmailPub:
    """ This class implements the MSN Email publishing."""
    def __init__(self, receiver):
        """
        initiation method.
        """
        self.receiver = receiver
        self.entry=BlogEntry()

    def setTitle(self,title):
        "Set title"
        self.entry.title=title

    def setContent(self,content):
        "Set body content"
        self.entry.content=content

    def postEntry(self, publish=True, blogid=1):
        fromaddr = 'peter4test@gmail.com'
        toaddrs  = self.receiver
        msg = '''Subject: %s
%s
        '''%(self.entry.title,self.entry.content)
        username = 'peter4test@gmail.com'
        password = 'peter2win'
        server = smtplib.SMTP('smtp.gmail.com:587')
        server.starttls()
        server.login(username,password)
        server.sendmail(fromaddr, toaddrs, msg)
        server.quit()


class BlogEntry:
    '''class for a blog entry'''
    def __init__(self,title=None,content=None):
        '''initiate a blog entry object'''
        self.title=title
        self.content=content

    def clear(self):
        '''clear the title and content of the blog entry'''
        self.title=None
        self.content=None
