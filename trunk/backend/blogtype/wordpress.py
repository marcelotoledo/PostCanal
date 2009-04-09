import re, httplib
from blogtype import BlogType
###from vendor.BeautifulSoup import BeautifulSoup


TYPE_ID                = "wordpress"
TYPE_NAME              = "WordPress"
URL_ADMIN_SUFFIX       = "/wp-login.php"
VERSION_WORDPRESS_COM  = "wordpress_com"
VERSION_WORDPRESS_TEST = "wordpress_TEST"

VERSION = { VERSION_WORDPRESS_COM  : { "name" : "wordpress.com",    "revision" : 1 } ,
            VERSION_WORDPRESS_TEST : { "name" : "wordpress (TEST)", "revision" : 1 } }


class WordPress(BlogType):
    def __init__(self, location, client=None):
        self.location = location
        self.client   = client
        self.id       = TYPE_ID
        self.name     = TYPE_NAME

    def setver(self, version):
        self.version = VERSION[version]
        self.url = "http://" + self.location
        self.url_admin = self.url + URL_ADMIN_SUFFIX
        split = re.split("\.", self.location)
        self.login = split[0]

        # test url

        if self.url_ok == False:
            self.client.request("GET", "/")

            try:
                response = self.client.getresponse()
                self.url_ok = True if response.status == 200 else False
            except:
                self.url_ok = False

        # test url admin

        self.client.request("GET", URL_ADMIN_SUFFIX)

        try:
            response = self.client.getresponse()
            self.url_admin_ok = True if response.status == 200 else False
        except:
            self.url_admin_ok = False
