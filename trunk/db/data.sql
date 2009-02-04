/* base */

INSERT INTO channel_status (channel_status_id, error_level, label) VALUES (11, 0, 'channel_ok');
INSERT INTO channel_status (channel_status_id, error_level, label) VALUES (21, 2, 'channel_link_3xx');
INSERT INTO channel_status (channel_status_id, error_level, label) VALUES (22, 2, 'channel_link_4xx');
INSERT INTO channel_status (channel_status_id, error_level, label) VALUES (23, 2, 'channel_link_5xx');
INSERT INTO channel_status (channel_status_id, error_level, label) VALUES (41, 1, 'channel_no_itens');

-- INSERT INTO cms_status (cms_status_id, error_level, label) VALUES (11, 0, 'CMS_OK');
-- INSERT INTO cms_status (cms_status_id, error_level, label) VALUES (21, 2, 'URL_BASE_3XX');
-- INSERT INTO cms_status (cms_status_id, error_level, label) VALUES (22, 2, 'URL_BASE_4XX');
-- INSERT INTO cms_status (cms_status_id, error_level, label) VALUES (23, 2, 'URL_BASE_5XX');
-- INSERT INTO cms_status (cms_status_id, error_level, label) VALUES (24, 2, 'URL_ADMIN_3XX');
-- INSERT INTO cms_status (cms_status_id, error_level, label) VALUES (25, 2, 'URL_ADMIN_4XX');
-- INSERT INTO cms_status (cms_status_id, error_level, label) VALUES (26, 2, 'URL_ADMIN_5XX');
-- INSERT INTO cms_status (cms_status_id, error_level, label) VALUES (31, 2, 'CMS_TYPE_UNKNOWN');
-- INSERT INTO cms_status (cms_status_id, error_level, label) VALUES (41, 1, 'ADMIN_LOGIN_FAILURE');

/* aggregator */

-- INSERT INTO aggregator_channel (channel_status_id, title, link, description) VALUES (11, 'Slashdot', 'http://rss.slashdot.org/Slashdot/slashdot', 'News for nerds, stuff that matters');

/* user */

INSERT INTO user_profile (login_email, login_password_md5, register_confirmation) VALUES ('rafael@castilho.biz', MD5('castilho'), true);
INSERT INTO user_information (user_profile_id) VALUES (CURRVAL('user_profile_seq'));

/* cms type */

INSERT INTO cms_type (name, version, enabled) VALUES ('WordPress', 'WordPress.com', true);
-- INSERT INTO cms_type_discovery (cms_type_id, name, value) VALUES (CURRVAL('cms_type_seq'), 'url', '^http:\\/\\/.+\\.wordpress\\.com$'); -- invalid (.+).wordpress.com do not return 404 response status
INSERT INTO cms_type_discovery (cms_type_id, name, value) VALUES (CURRVAL('cms_type_seq'), 'html', '<meta[^>]+(content)+[^>]+(wordpress\.com)+[^>]+>');
