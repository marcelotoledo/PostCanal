# WordPress CMSType Plugin

import sys


URL_ADMN = 'wp-admin'
URL_AUTH = 'wp-login.php?action=auth'

VERSIONS = {
#   version           admin url  admin auth url
    'wordpress.com': (URL_ADMN,  URL_AUTH),
    }


class WordPress (object) :
    """Arguments are: plugin version and base URL"""
    def __init__(self, version, url):
        self.url = url
        self.version = version

    def get_url_admin(self):
        """Get admin URL from base URL"""
        return "%s/%s" % (self.url, VERSIONS[self.version][0])

    def get_url_admin_auth(self):
        """Get admin authentication URL from base URL"""
        return "%s/%s" % (self.url, VERSIONS[self.version][1])

    def info(self):
        print "%s\t%s" % ("url_admin", self.get_url_admin())
        print "%s\t%s" % ("url_admin_auth", self.get_url_admin_auth())


def main(args = None):
    USAGE = "Usage: wordpress.py version url"
    VSERR = "version \"%s\" not implemented"

    if args is None:
        args = sys.argv[1:]

    if not args:
        print USAGE
        sys.exit(1)

    if len(args) != 2:
        print USAGE
        sys.exit(1)

    version = args[0].lower()

    if version not in VERSIONS:
        print VSERR % (version)
        sys.exit(1)

    p = WordPress(version, args[1])
    p.info()


if __name__ == "__main__":
    main()
