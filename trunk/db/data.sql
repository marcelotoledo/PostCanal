/* base */

INSERT INTO error_level (error_level_id, label) VALUES (0, 'OK');
INSERT INTO error_level (error_level_id, label) VALUES (1, 'WARNING');
INSERT INTO error_level (error_level_id, label) VALUES (2, 'ERROR');

INSERT INTO channel_status (channel_status_id, error_level_id, label) VALUES (11, 0, 'CHANNEL_NO_ERRORS');
INSERT INTO channel_status (channel_status_id, error_level_id, label) VALUES (21, 2, 'CHANNEL_LINK_4XX');
INSERT INTO channel_status (channel_status_id, error_level_id, label) VALUES (22, 2, 'CHANNEL_LINK_5XX');
INSERT INTO channel_status (channel_status_id, error_level_id, label) VALUES (41, 1, 'CHANNEL_NO_ITEMS');

INSERT INTO cms_status (cms_status_id, error_level_id, label) VALUES (11, 0, 'CMS_NO_ERRORS');
INSERT INTO cms_status (cms_status_id, error_level_id, label) VALUES (21, 2, 'URL_BASE_4XX');
INSERT INTO cms_status (cms_status_id, error_level_id, label) VALUES (22, 2, 'URL_BASE_5XX');
INSERT INTO cms_status (cms_status_id, error_level_id, label) VALUES (23, 2, 'URL_ADMIN_4XX');
INSERT INTO cms_status (cms_status_id, error_level_id, label) VALUES (24, 2, 'URL_ADMIN_5XX');
INSERT INTO cms_status (cms_status_id, error_level_id, label) VALUES (25, 2, 'CMS_TYPE_UNKNOWN');
INSERT INTO cms_status (cms_status_id, error_level_id, label) VALUES (41, 1, 'ADMIN_LOGIN_FAILURE');

/* aggregator */

INSERT INTO aggregator_channel (channel_status_id, title, link, description) VALUES (11, 'Slashdot', 'http://rss.slashdot.org/Slashdot/slashdot', 'News for nerds, stuff that matters');

/* user */

INSERT INTO user_profile (login_email, login_password_md5, register_confirmation) VALUES ('rafael@castilho.biz', MD5('castilho'), true);
INSERT INTO user_information (user_profile_id) VALUES (CURRVAL('user_profile_seq'));
