
-- DROP TABLE IF EXISTS wards;
-- DROP TABLE IF EXISTS districts;
-- DROP TABLE IF EXISTS provinces;
-- DROP TABLE IF EXISTS administrative_units;
-- DROP TABLE IF EXISTS administrative_regions;

-- CREATE administrative_regions TABLE
CREATE TABLE administrative_regions (
                                        id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                                        name varchar(255) NOT NULL,
                                        name_en varchar(255) NOT NULL,
                                        code_name varchar(255) NULL,
                                        code_name_en varchar(255) NULL,
                                        PRIMARY KEY (id)
);


-- CREATE administrative_units TABLE
CREATE TABLE administrative_units (
                                      id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                                      full_name varchar(255) NULL,
                                      full_name_en varchar(255) NULL,
                                      short_name varchar(255) NULL,
                                      short_name_en varchar(255) NULL,
                                      code_name varchar(255) NULL,
                                      code_name_en varchar(255) NULL,
                                      PRIMARY KEY (id)
);


-- CREATE provinces TABLE
CREATE TABLE provinces (
                           id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                           code varchar(20) NOT NULL,
                           name varchar(255) NOT NULL,
                           name_en varchar(255) NULL,
                           full_name varchar(255) NOT NULL,
                           full_name_en varchar(255) NULL,
                           code_name varchar(255) NULL,
                           administrative_unit_id integer NULL,
                           administrative_region_id integer NULL,
                           PRIMARY KEY (id)
);


-- CREATE districts TABLE
CREATE TABLE districts (
                           id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                           code varchar(20) NOT NULL,
                           name varchar(255) NOT NULL,
                           name_en varchar(255) NULL,
                           full_name varchar(255) NULL,
                           full_name_en varchar(255) NULL,
                           code_name varchar(255) NULL,
                           province_code varchar(20) NULL,
                           administrative_unit_id integer NULL,
                           PRIMARY KEY (id)
);


-- CREATE wards TABLE
CREATE TABLE wards (
                       id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                       code varchar(20) NOT NULL,
                       name varchar(255) NOT NULL,
                       name_en varchar(255) NULL,
                       full_name varchar(255) NULL,
                       full_name_en varchar(255) NULL,
                       code_name varchar(255) NULL,
                       district_code varchar(20) NULL,
                       administrative_unit_id integer NULL,
                       PRIMARY KEY (id)
);

