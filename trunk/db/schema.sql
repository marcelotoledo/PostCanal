/* drop */

DROP TABLE IF EXISTS aggregator_item;
DROP SEQUENCE IF EXISTS aggregator_item_seq;
DROP TABLE IF EXISTS user_cms_channel;
DROP SEQUENCE IF EXISTS user_cms_channel_seq;
DROP TABLE IF EXISTS aggregator_channel;
DROP SEQUENCE IF EXISTS aggregator_channel_seq;
DROP TABLE IF EXISTS channel_status;
DROP TABLE IF EXISTS user_cms;
DROP SEQUENCE IF EXISTS user_cms_seq;
DROP TABLE IF EXISTS cms_status;
DROP TABLE IF EXISTS user_information;
DROP TABLE IF EXISTS user_profile;
DROP SEQUENCE IF EXISTS user_profile_seq;
DROP TABLE IF EXISTS cms_type;
DROP TABLE IF EXISTS application_log;
DROP SEQUENCE IF EXISTS application_log_seq;

/* base */

CREATE SEQUENCE application_log_seq;
CREATE TABLE application_log
(
    application_log_id integer NOT NULL DEFAULT nextval('application_log_seq'),
    priority integer NOT NULL DEFAULT 0,
    message text NOT NULL DEFAULT '',
    created_at timestamp without time zone NOT NULL DEFAULT NOW(),
    CONSTRAINT application_log_pk PRIMARY KEY (application_log_id)
);

CREATE TABLE channel_status
(
    channel_status_id integer NOT NULL,
    error_level integer NOT NULL DEFAULT 0,
    label character varying(50) NOT NULL,
    CONSTRAINT channel_status_pk PRIMARY KEY (channel_status_id),
    CONSTRAINT channel_status_id_unique UNIQUE (channel_status_id)
);

CREATE TABLE cms_status
(
    cms_status_id integer NOT NULL,
    error_level integer NOT NULL DEFAULT 0,
    label character varying(50) NOT NULL,
    CONSTRAINT cms_status_pk PRIMARY KEY (cms_status_id),
    CONSTRAINT cms_status_id_unique UNIQUE (cms_status_id)
);

CREATE TABLE cms_type
(
    cms_type_id integer NOT NULL,
    name character varying(50) NOT NULL,
    enabled boolean NOT NULL DEFAULT false,
    CONSTRAINT cms_type_pk PRIMARY KEY (cms_type_id),
    CONSTRAINT cms_type_id_unique UNIQUE (cms_type_id)
);

/* aggregator */

CREATE SEQUENCE aggregator_channel_seq;
CREATE TABLE aggregator_channel
(
    aggregator_channel_id integer NOT NULL DEFAULT nextval('aggregator_channel_seq'),
    channel_status_id integer NOT NULL,
    title character varying(100) NOT NULL,
    link character varying(200) NOT NULL,
    description text NOT NULL DEFAULT '',
    created_at timestamp without time zone NOT NULL DEFAULT NOW(),
    updated_at timestamp without time zone NOT NULL DEFAULT NOW(),
    enabled boolean NOT NULL DEFAULT true,
    CONSTRAINT aggregator_channel_pk PRIMARY KEY (aggregator_channel_id),
    CONSTRAINT channel_status_fk FOREIGN KEY (channel_status_id) 
        REFERENCES channel_status (channel_status_id) ON DELETE RESTRICT
);

CREATE SEQUENCE aggregator_item_seq;
CREATE TABLE aggregator_item
(
    aggregator_item_id integer NOT NULL DEFAULT nextval('aggregator_item_seq'), 
    aggregator_channel_id integer NOT NULL, 
    title text NOT NULL, 
    link text NOT NULL,
    description text NOT NULL DEFAULT '',
    created_at timestamp without time zone NOT NULL DEFAULT NOW(), 
    CONSTRAINT aggregator_item_pk PRIMARY KEY (aggregator_item_id),
    CONSTRAINT aggregator_channel_fk FOREIGN KEY (aggregator_channel_id) 
        REFERENCES aggregator_channel (aggregator_channel_id) ON DELETE CASCADE
);

/* user */

CREATE SEQUENCE user_profile_seq;
CREATE TABLE user_profile
(
    user_profile_id integer NOT NULL DEFAULT nextval('user_profile_seq'),
    login_email character varying(100) NOT NULL,
    login_password_md5 character varying(32) NOT NULL,
    /* register confirmation: true when user confirm registration */
    register_confirmation boolean NOT NULL DEFAULT false,
    /* register last message: last register confirmation message d&t */
    register_last_message timestamp without time zone DEFAULT NULL,
    /* recovery last message: last profile recovery message d&t */
    recovery_last_message timestamp without time zone DEFAULT NULL,
    created_at timestamp without time zone NOT NULL DEFAULT NOW(),
    updated_at timestamp without time zone NOT NULL DEFAULT NOW(),
    enabled boolean NOT NULL DEFAULT true,
    CONSTRAINT user_profile_pk PRIMARY KEY (user_profile_id),
    CONSTRAINT login_email_unique UNIQUE (login_email)
);

CREATE TABLE user_information
(
    user_profile_id integer NOT NULL,
    name character varying(100) NOT NULL DEFAULT '',
    /* TODO: location, language, etc. */
    CONSTRAINT user_profile_fk FOREIGN KEY (user_profile_id) 
        REFERENCES user_profile (user_profile_id) ON DELETE CASCADE
);

CREATE SEQUENCE user_cms_seq;
CREATE TABLE user_cms
(
    user_cms_id integer NOT NULL DEFAULT nextval('user_cms_seq'),
    user_profile_id integer NOT NULL,
    cms_type_id integer NOT NULL,
    cms_status_id integer NOT NULL,
    name character varying(100) NOT NULL,
    url_base character varying(200) NOT NULL,
    url_admin character varying(200) NOT NULL,
    admin_username character varying(100) NOT NULL,
    admin_password character varying(100) NOT NULL,
    created_at timestamp without time zone NOT NULL DEFAULT NOW(),
    updated_at timestamp without time zone NOT NULL DEFAULT NOW(),
    enabled boolean NOT NULL DEFAULT true,
    CONSTRAINT user_cms_pk PRIMARY KEY (user_cms_id),
    CONSTRAINT user_profile_fk FOREIGN KEY (user_profile_id) 
        REFERENCES user_profile (user_profile_id) ON DELETE CASCADE,
    CONSTRAINT cms_type_fk FOREIGN KEY (cms_type_id) 
        REFERENCES cms_type (cms_type_id) ON DELETE RESTRICT,
    CONSTRAINT cms_status_fk FOREIGN KEY (cms_status_id) 
        REFERENCES cms_status (cms_status_id) ON DELETE RESTRICT
);

CREATE SEQUENCE user_cms_channel_seq;
CREATE TABLE user_cms_channel
(
    user_cms_channel_id integer NOT NULL DEFAULT nextval('user_cms_channel_seq'),
    user_cms_id integer NOT NULL,
    aggregator_channel_id integer NOT NULL,
    title character varying(100) NOT NULL,
    description text NOT NULL DEFAULT '',
    created_at timestamp without time zone NOT NULL DEFAULT NOW(),
    updated_at timestamp without time zone NOT NULL DEFAULT NOW(),
    enabled boolean NOT NULL DEFAULT true,
    CONSTRAINT user_cms_channel_pk PRIMARY KEY (user_cms_channel_id),
    CONSTRAINT user_cms_fk FOREIGN KEY (user_cms_id) 
        REFERENCES user_cms (user_cms_id) ON DELETE CASCADE,
    CONSTRAINT aggregator_channel_fk FOREIGN KEY (aggregator_channel_id) 
        REFERENCES aggregator_channel (aggregator_channel_id) ON DELETE RESTRICT
);
