### wordpress ###

WORDPRESS = "wordpress"

WORDPRESS_URL_MATCH                 = "(.+)\.wordpress\.com"
WORDPRESS_MANAGER_URL_SUFFIX        = "/wp-login.php"
WORDPRESS_MANAGER_URL_SUFFIX_SUB    = [ "\/wp-admin[\/]*", "/wp-login.php" ]
WORDPRESS_MANAGER_URL_SUFFIX_SEARCH = "\/wp\-login\.php"
WORDPRESS_BODY_META_FIND            = "WordPress.com"
WORDPRESS_BODY_META_SEARCH          = "WordPress\.com"

WORDPRESS_VERSION_COM    = "wordpress_com"
WORDPRESS_VERSION_DOMAIN = "wordpress_domain"

#                      version_id                 revision
WORDPRESS_REVISION = { WORDPRESS_VERSION_COM    : 1,
                       WORDPRESS_VERSION_DOMAIN : 1 }

WORDPRESS_CLIENT_TIMEOUT = 15


### etc ... ###
