
USE thelogco_2021;
CREATE TABLE price_quote (
    id INT NOT NULL AUTO_INCREMENT,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email_address VARCHAR(100) NOT NULL,
    phone VARCHAR(100) NOT NULL,
    build_place_opt VARCHAR(100) NOT NULL, #USA Canadian or Other
	build_place VARCHAR(100) NOT NULL, #relative to build_place_opt
	is_save_cookie TINYINT DEFAULT 0 NOT NULL,
    currency VARCHAR(50) NOT NULL,

    #optional
    plan_build_city VARCHAR(100) NULL,
    plan_build_country VARCHAR(100) NULL,
    plan_build_state VARCHAR(100) NULL,
    plan_build_date VARCHAR(100) NULL,
    turn_key_budget VARCHAR(100) NULL,
    has_purchased_land TINYINT(1) DEFAULT 0,
    has_blueprint TINYINT(1) DEFAULT 0,
    
    #optional contact information
	address_street VARCHAR(100) NULL,
	address_apt_no VARCHAR(100) NULL,
	address_city VARCHAR(100) NULL,
	address_country VARCHAR(100) NULL,
    address_state VARCHAR(100) NULL,
	address_postal_code VARCHAR(100) NULL,
    
    #homeplan
    plan_code VARCHAR(100) NULL,
    plan_name VARCHAR(100) NULL,
    log_style VARCHAR(100) NULL,
    all_weather_barrier VARCHAR(100) NULL,
    log_type VARCHAR(100) NULL,
    notch VARCHAR(100) NULL,
    roofing VARCHAR(100) NULL,
    tg_ceiling VARCHAR(100) NULL,
    deck VARCHAR(100) NULL,
    gables VARCHAR(100) NULL,
    floor VARCHAR(100) NULL,
    walls VARCHAR(100) NULL,
    windows VARCHAR(100) NULL,
    windows_extra VARCHAR(100) NULL,
    doors VARCHAR(100) NULL,
    doors_extra VARCHAR(100) NULL,
    log_stair VARCHAR(100) NULL,
    stair_railing VARCHAR(100) NULL,
    guard_railing VARCHAR(100) NULL,
    deck_railing VARCHAR(100) NULL,
    order_type VARCHAR(100) NULL,
    package VARCHAR(100) NULL,
    shell_price VARCHAR(100) NULL,
    materials_price VARCHAR(100) NULL,
    total_price VARCHAR(100) NULL,

	#for audit
    client_ip VARCHAR(100) NOT NULL, 
    delete_flag TINYINT(1) DEFAULT 0,
    created_by VARCHAR(100) NOT NULL,
    created_dttm DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_by VARCHAR(100) NOT NULL,
    updated_dttm DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) ENGINE=INNODB;