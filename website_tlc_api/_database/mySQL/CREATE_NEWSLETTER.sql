USE thelogco_2021;
CREATE TABLE newsletter (
    id INT NOT NULL AUTO_INCREMENT,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email_address VARCHAR(100) NOT NULL,
    post_and_beam TINYINT DEFAULT 0,
    stacked_log TINYINT DEFAULT 0,
    timber_frame TINYINT DEFAULT 0,
    #for audit
    client_ip VARCHAR(100) NOT NULL, 
    delete_flag TINYINT DEFAULT 0,
    created_by VARCHAR(100) NOT NULL,
    created_dttm DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_by VARCHAR(100) NOT NULL,
    updated_dttm DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) ENGINE=INNODB;


USE thelogco_2021;
CREATE TABLE newsletter_generic (
    email_address VARCHAR(100) NOT NULL,
    #for audit
    client_ip VARCHAR(100) NOT NULL, 
    delete_flag TINYINT DEFAULT 0,
    created_by VARCHAR(100) NOT NULL,
    created_dttm DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ,
    updated_by VARCHAR(100) NOT NULL,
    updated_dttm DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (email_address)
) ENGINE=INNODB;
