USE thelogco_2021;
CREATE TABLE order_plan_book (
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

    #credit card information
    cc_type VARCHAR(100) NULL,
    cc_holder_name VARCHAR(100) NULL,
    cc_number VARCHAR(100) NULL,
    cc_expiration VARCHAR(100) NULL,
    cc_code VARCHAR(100) NULL,

    #acknowledgement
    ack_copyright TINYINT(1) DEFAULT 0,
    ack_no_refund TINYINT(1) DEFAULT 0,

    #for audit
    client_ip VARCHAR(100) NOT NULL, 
    delete_flag TINYINT(1) DEFAULT 0,
    created_by VARCHAR(100) NOT NULL,
    created_dttm DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_by VARCHAR(100) NOT NULL,
    updated_dttm DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) ENGINE=INNODB;