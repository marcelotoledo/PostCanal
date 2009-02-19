/* aggregator */

-- INSERT INTO aggregator_channel (channel_status_id, title, link, description) VALUES (11, 'Slashdot', 'http://rss.slashdot.org/Slashdot/slashdot', 'News for nerds, stuff that matters');

/* user */

INSERT INTO user_profile (login_email, login_password_md5, register_confirmation) VALUES ('rafael@castilho.biz', MD5('castilho'), true);
INSERT INTO user_profile_information (user_profile_id) VALUES (CURRVAL('user_profile_seq'));

/* cms type */

INSERT INTO cms_type (name, version, enabled) VALUES ('WordPress', 'WordPress.com', true);

-- !!! VERY IMPORTANT !!!
-- the field value on cms_type_discovery table are filled with serialized arrays;
-- also you need to escape "\" with "\\" on serialized string.

INSERT INTO cms_type_discovery (cms_type_id, name, value) VALUES (CURRVAL('cms_type_seq'), 'required', 'a:2:{i:0;s:3:"url";i:1;s:4:"html";}');
INSERT INTO cms_type_discovery (cms_type_id, name, value) VALUES (CURRVAL('cms_type_seq'), 'url_replace', 'a:1:{i:0;a:2:{i:0;s:44:"/^[^\\w]*(http:\\/\\/)*(.+\\.wordpress\\.com).*$/";i:1;s:9:"http://\\2";}}');
INSERT INTO cms_type_discovery (cms_type_id, name, value) VALUES (CURRVAL('cms_type_seq'), 'url_match', 'a:1:{i:0;a:1:{i:0;s:31:"/^http:\\/\\/.+\\.wordpress\\.com$/";}}');
INSERT INTO cms_type_discovery (cms_type_id, name, value) VALUES (CURRVAL('cms_type_seq'), 'html_replace', 'a:3:{i:0;a:2:{i:0;s:19:"/<body>.+<\\/body>/i";i:1;s:0:"";}i:1;a:2:{i:0;s:24:"/.+(<head.+<\\/head>)+.+/";i:1;s:2:"\\1";}i:2;a:2:{i:0;s:56:"/.*(<meta[^>]+(content)+[^>]+(wordpress.com)+[^>]+>).*/i";i:1;s:2:"\\1";}}');
INSERT INTO cms_type_discovery (cms_type_id, name, value) VALUES (CURRVAL('cms_type_seq'), 'html_match', 'a:1:{i:0;a:1:{i:0;s:31:"/<meta[^>]+(generator)+[^>]+>/i";}}');

INSERT INTO cms_type_configuration (cms_type_id, name, value) VALUES (CURRVAL('cms_type_seq'), 'manager_url', '/wp-admin');
INSERT INTO cms_type_configuration (cms_type_id, name, value) VALUES (CURRVAL('cms_type_seq'), 'manager_action_url', '/wp-login.php');
INSERT INTO cms_type_configuration (cms_type_id, name, value) VALUES (CURRVAL('cms_type_seq'), 'manager_input_username', 'log');
INSERT INTO cms_type_configuration (cms_type_id, name, value) VALUES (CURRVAL('cms_type_seq'), 'manager_input_password', 'pwd');

INSERT INTO cms_type_configuration (cms_type_id, name, value) VALUES (CURRVAL('cms_type_seq'), 'manager_html_replace', 'a:1:{i:0;a:2:{i:0;s:23:"/.+(<form.+\\/form>).+/i";i:1;s:2:"\\1";}}');
INSERT INTO cms_type_configuration (cms_type_id, name, value) VALUES (CURRVAL('cms_type_seq'), 'manager_html_match', 'a:3:{i:0;a:1:{i:0;s:49:"/<form[^>]+(action)+[^>]+(wp.login\\.php)+[^>]+>/i";}i:1;a:1:{i:0;s:38:"/<input[^>]+(name)+[^>]+(log)+[^>]+>/i";}i:2;a:1:{i:0;s:38:"/<input[^>]+(name)+[^>]+(pwd)+[^>]+>/i";}}');
