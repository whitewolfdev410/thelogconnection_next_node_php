USE thelogco_2021;
CREATE TABLE order_study_set (
    id INT NOT NULL AUTO_INCREMENT,
    #about you
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email_address VARCHAR(100) NOT NULL,
    phone VARCHAR(100) NOT NULL,
    
	#shipping address
	sa_street VARCHAR(100) NULL,
	sa_apt_no VARCHAR(100) NULL,
	sa_city VARCHAR(100) NULL,
    sa_country VARCHAR(100) NULL,
    sa_state VARCHAR(100) NULL,
	sa_postal_code VARCHAR(100) NULL,

    #project information
    pi_country VARCHAR(100) NULL,
    pi_state VARCHAR(100) NULL,
    pi_build_date VARCHAR(100) NULL,
    pi_turn_key_budget VARCHAR(100) NULL,
    pi_has_purchased_land TINYINT NULL DEFAULT 0,
    pi_has_blueprint TINYINT NULL DEFAULT 0,
    
    #Design 
    op_home_plan_code_1 VARCHAR(100) NULL,
	op_home_plan_code_2 VARCHAR(100) NULL,
	op_home_plan_code_3 VARCHAR(100) NULL,
	op_price_1 VARCHAR(100) NULL,
    op_price_2 VARCHAR(100) NULL,
    op_price_3 VARCHAR(100) NULL,
    op_price_total VARCHAR(100) NULL,
    
    #credit card information
    cc_type VARCHAR(100) NULL,
    cc_holder_name VARCHAR(100) NULL,
    cc_number VARCHAR(100) NULL,
    cc_expiration VARCHAR(100) NULL,
    cc_code VARCHAR(100) NULL,

    #acknowledgement
    ack_copyright TINYINT NULL DEFAULT 0,
    ack_no_refund TINYINT NULL DEFAULT 0,

	#for audit
    client_ip VARCHAR(100) NOT NULL, 
    delete_flag TINYINT DEFAULT 0,
    created_by VARCHAR(100) NOT NULL,
    created_dttm DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_by VARCHAR(100) NOT NULL,
    updated_dttm DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) ENGINE=INNODB;