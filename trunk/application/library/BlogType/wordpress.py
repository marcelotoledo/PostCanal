# WordPress BlogType Plugin

import sys


URL_ADMN = 'wp-admin'
URL_AUTH = 'wp-login.php?action=auth'

VERSIONS = {
#    version          admin url  admin auth url
    'wordpress.com': (URL_ADMN,  URL_AUTH),
}


class WordPress (object) :
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
    import textwrap
    USAGE = textwrap.dedent("""\
        Usage:
            wordpress.py info  version url_base  # Show information
            wordpress.py check version url       # Check URL status
            wordpress.py auth  version url_admin # URL admin authentication
    """)
    VSERR = "version \"%s\" not implemented"
    CMDS = ('info', 'check', 'auth')
    CMDERR = "command \"%s\" not implemented"

    if args is None:
        args = sys.argv[1:]

    if not args:
        print USAGE
        sys.exit(1)

    if len(args) != 3:
        print USAGE
        sys.exit(1)

    cmd = args[1]

    if blog not in CMDS:
        print CMDERR % (cmd)
        sys.exit(1)

    version = args[1].lower()

    if version not in VERSIONS:
        print VSERR % (version)
        sys.exit(1)

    p = WordPress(version, args[1])
    p.info()


if __name__ == "__main__":
    main()
