### wordpress ###

WORDPRESS       = "wordpress"
WORDPRESS_LABEL = "WordPress"

WORDPRESS_URL_MATCH               = "(.+)\.wordpress\.com"
WORDPRESS_URL_ADMIN_SUFFIX        = "/wp-login.php"
WORDPRESS_URL_ADMIN_SUFFIX_SUB    = [ "\/wp-admin[\/]*", "/wp-login.php" ]
WORDPRESS_URL_ADMIN_SUFFIX_SEARCH = "\/wp\-login\.php"
WORDPRESS_BODY_META_FIND          = "WordPress.com"
WORDPRESS_BODY_META_SEARCH        = "WordPress\.com"

WORDPRESS_VERSION_COM    = "wordpress_com"
WORDPRESS_VERSION_DOMAIN = "wordpress_domain"

#                     id                           label               revision
WORDPRESS_VERSION = { WORDPRESS_VERSION_COM    : [ "wordpress.com",    1 ],
                      WORDPRESS_VERSION_DOMAIN : [ "wordpress domain", 1 ] }

WORDPRESS_CLIENT_TIMEOUT = 15


### etc ... ###
