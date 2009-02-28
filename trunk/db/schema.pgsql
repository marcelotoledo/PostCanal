SET TIME ZONE 'UTC';

/* drop */

DROP TABLE IF EXISTS application_log CASCADE;
DROP TABLE IF EXISTS application_mailer_relay CASCADE;
DROP TABLE IF EXISTS cms_type CASCADE;
DROP TABLE IF EXISTS cms_type_discovery CASCADE;
DROP TABLE IF EXISTS cms_type_configuration CASCADE;
DROP TABLE IF EXISTS aggregator_channel CASCADE;
DROP TABLE IF EXISTS aggregator_item CASCADE;
DROP TABLE IF EXISTS user_profile CASCADE;
DROP TABLE IF EXISTS user_profile_information CASCADE;
DROP TABLE IF EXISTS user_cms CASCADE;
DROP TABLE IF EXISTS user_cms_channel CASCADE;
DROP SEQUENCE IF EXISTS application_log_seq;
DROP SEQUENCE IF EXISTS application_mailer_relay_seq;
DROP SEQUENCE IF EXISTS cms_type_seq;
DROP SEQUENCE IF EXISTS cms_type_discovery_seq;
DROP SEQUENCE IF EXISTS cms_type_configuration_seq;
DROP SEQUENCE IF EXISTS aggregator_channel_seq;
DROP SEQUENCE IF EXISTS aggregator_item_seq;
DROP SEQUENCE IF EXISTS user_profile_seq; ;
DROP SEQUENCE IF EXISTS user_cms_seq;
DROP SEQUENCE IF EXISTS user_cms_channel_seq;


/* log */

CREATE SEQUENCE application_log_seq;
CREATE TABLE application_log
(
    application_log_id integer NOT NULL DEFAULT nextval('application_log_seq'),
    priority integer NOT NULL DEFAULT 0,
    message text NOT NULL,
    method character varying(100) NOT NULL DEFAULT '',
    controller character varying(100) NOT NULL DEFAULT '',
    action character varying(100) NOT NULL DEFAULT '',
    user_profile_id integer NOT NULL DEFAULT 0,
    created_at timestamp without time zone NOT NULL DEFAULT NOW(),
    CONSTRAINT application_log_pk PRIMARY KEY (application_log_id)
);

CREATE INDEX application_log_priority_index ON application_log (priority);

/* mailer */

CREATE SEQUENCE application_mailer_relay_seq;
CREATE TABLE application_mailer_relay
(
    application_mailer_relay_id integer NOT NULL
        DEFAULT nextval('application_mailer_relay_seq'),
    recipient character varying(100) NOT NULL,
    identifier character varying(8) DEFAULT NULL,
    created_at timestamp without time zone NOT NULL DEFAULT NOW()
);

CREATE INDEX application_mailer_relay_index 
    ON application_mailer_relay (recipient, identifier, created_at);

/* cms type */

CREATE SEQUENCE cms_type_seq;
CREATE TABLE cms_type
(
    cms_type_id integer NOT NULL DEFAULT nextval('cms_type_seq'),
    name character varying(50) NOT NULL,
    version character varying(50) NOT NULL,
    maintenance boolean NOT NULL DEFAULT false,
    enabled boolean NOT NULL DEFAULT false,
    CONSTRAINT cms_type_pk PRIMARY KEY (cms_type_id),
    CONSTRAINT cms_type_id_unique UNIQUE (cms_type_id)
);

CREATE SEQUENCE cms_type_discovery_seq;
CREATE TABLE cms_type_discovery
(
    cms_type_discovery_id integer NOT NULL 
        DEFAULT nextval('cms_type_discovery_seq'),
    cms_type_id integer NOT NULL,
    name character varying(50) NOT NULL,
    value text NOT NULL,
    CONSTRAINT cms_type_discovery_pk PRIMARY KEY (cms_type_discovery_id),
    CONSTRAINT cms_type_fk FOREIGN KEY (cms_type_id) 
        REFERENCES cms_type (cms_type_id) ON DELETE CASCADE
);

CREATE INDEX cms_type_discovery_index ON cms_type_discovery (cms_type_id, name);

CREATE SEQUENCE cms_type_configuration_seq;
CREATE TABLE cms_type_configuration
(
    cms_type_configuration_id integer NOT NULL
        DEFAULT NEXTVAL('cms_type_configuration_seq'),
    cms_type_id integer NOT NULL,
    name character varying(50) NOT NULL,
    value text NOT NULL,
    CONSTRAINT cms_type_configuration_pk 
        PRIMARY KEY (cms_type_configuration_id),
    CONSTRAINT cms_type_fk FOREIGN KEY (cms_type_id) 
        REFERENCES cms_type (cms_type_id) ON DELETE CASCADE
);

CREATE INDEX cms_type_configuration_index ON cms_type_configuration (cms_type_id);

/* aggregator */

CREATE SEQUENCE aggregator_channel_seq;
CREATE TABLE aggregator_channel
(
    aggregator_channel_id integer NOT NULL DEFAULT nextval('aggregator_channel_seq'),
    title character varying(100) NOT NULL,
    link character varying(200) NOT NULL,
    description text NOT NULL DEFAULT '',
    status character varying(50) NOT NULL,
    created_at timestamp without time zone NOT NULL DEFAULT NOW(),
    updated_at timestamp without time zone DEFAULT NULL,
    enabled boolean NOT NULL DEFAULT true,
    CONSTRAINT aggregator_channel_pk PRIMARY KEY (aggregator_channel_id)
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
    uid character varying(8) NOT NULL,
    login_email character varying(100) NOT NULL,
    login_password_md5 character varying(32) NOT NULL,
    register_confirmation boolean NOT NULL DEFAULT false,
    created_at timestamp without time zone NOT NULL DEFAULT NOW(),
    updated_at timestamp without time zone DEFAULT NULL,
    enabled boolean NOT NULL DEFAULT true,
    CONSTRAINT user_profile_pk PRIMARY KEY (user_profile_id)
);

CREATE INDEX user_profile_enabled_index 
    ON user_profile (user_profile_id) WHERE enabled is TRUE;
CREATE INDEX user_profile_email_index
    ON user_profile (login_email) WHERE enabled is TRUE;
CREATE INDEX user_profile_login_index
    ON user_profile (login_email, login_password_md5) WHERE enabled is TRUE;
CREATE INDEX user_profile_uid_index
    ON user_profile (login_email, uid) WHERE enabled is TRUE;

CREATE TABLE user_profile_information
(
    user_profile_id integer NOT NULL,
    name character varying(100) NOT NULL DEFAULT '',
    /* TODO: location, language, etc. */
    email_update character varying(100) NOT NULL,
    register_message_time timestamp without time zone DEFAULT NULL,
    register_confirmation_time timestamp without time zone DEFAULT NULL,
    last_login_time timestamp without time zone DEFAULT NULL,
    recovery_message_time timestamp without time zone DEFAULT NULL,
    email_update_message_time timestamp without time zone DEFAULT NULL,
    created_at timestamp without time zone NOT NULL DEFAULT NOW(),
    updated_at timestamp without time zone DEFAULT NULL,
    CONSTRAINT user_profile_fk FOREIGN KEY (user_profile_id) 
        REFERENCES user_profile (user_profile_id) ON DELETE CASCADE
);

CREATE SEQUENCE user_cms_seq;
CREATE TABLE user_cms
(
    user_cms_id integer NOT NULL DEFAULT nextval('user_cms_seq'),
    user_profile_id integer NOT NULL,
    cms_type_id integer NOT NULL,
    cid character varying(8) NOT NULL,
    name character varying(100) NOT NULL,
    url character varying(200) NOT NULL,
    manager_url character varying(200) NOT NULL,
    manager_username character varying(100) NOT NULL,
    manager_password character varying(100) NOT NULL,
    status character varying(50) NOT NULL,
    created_at timestamp without time zone NOT NULL DEFAULT NOW(),
    updated_at timestamp without time zone DEFAULT NULL,
    enabled boolean NOT NULL DEFAULT true,
    CONSTRAINT user_cms_pk PRIMARY KEY (user_cms_id),
    CONSTRAINT user_profile_fk FOREIGN KEY (user_profile_id) 
        REFERENCES user_profile (user_profile_id) ON DELETE CASCADE,
    CONSTRAINT cms_type_fk FOREIGN KEY (cms_type_id) 
        REFERENCES cms_type (cms_type_id) ON DELETE RESTRICT
);

CREATE INDEX user_cms_cid_index ON user_cms (user_profile_id, cid);

CREATE SEQUENCE user_cms_channel_seq;
CREATE TABLE user_cms_channel
(
    user_cms_channel_id integer NOT NULL 
        DEFAULT nextval('user_cms_channel_seq'),
    user_cms_id integer NOT NULL,
    aggregator_channel_id integer NOT NULL,
    title character varying(100) NOT NULL,
    description text NOT NULL DEFAULT '',
    created_at timestamp without time zone NOT NULL DEFAULT NOW(),
    updated_at timestamp without time zone DEFAULT NULL,
    enabled boolean NOT NULL DEFAULT true,
    CONSTRAINT user_cms_channel_pk PRIMARY KEY (user_cms_channel_id),
    CONSTRAINT user_cms_fk FOREIGN KEY (user_cms_id) 
        REFERENCES user_cms (user_cms_id) ON DELETE CASCADE,
    CONSTRAINT aggregator_channel_fk FOREIGN KEY (aggregator_channel_id) 
        REFERENCES aggregator_channel (aggregator_channel_id) ON DELETE RESTRICT
);
