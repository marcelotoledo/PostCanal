from msn_email_pub import MSNEmailPub

b = MSNEmailPub('cid-df71a64f287db7f8.peter4test@spaces.live.com')

b.setTitle('email test successful')
b.setContent('email testing content')

try:
    b.postEntry()
    print "Article posted!"
except:
    print "Article NOT posted!"
