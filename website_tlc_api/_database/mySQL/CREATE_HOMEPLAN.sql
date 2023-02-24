CREATE DATABASE thelogco_2021 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- USE LogConnection;
-- CREATE TABLE HomePlan (
--     HomePlanId INT NOT NULL AUTO_INCREMENT,
--     PlanNm VARCHAR(100) NOT NULL,
--     PlanSize VARCHAR(100) NOT NULL,
--     PlanCd VARCHAR(100) NOT NULL,
--     Dealer VARCHAR(100) NOT NULL,
--     PlanDesc VARCHAR(500) NOT NULL,
--     StyleCd VARCHAR(100) NOT NULL,
--     StyleNm VARCHAR(100) NOT NULL,
--     ImageUrl VARCHAR(200) NOT NULL,
--     VideoUrl VARCHAR(200) NOT NULL,
--     #for audit
--     DeleteInd TINYINT DEFAULT 0,
--     CreatedBy VARCHAR(100) NOT NULL,
--     CreateDttm DATETIME NOT NULL,
--     UpdatedBy VARCHAR(100) NOT NULL,
--     UpdateDttm DATETIME NOT NULL,
--     PRIMARY KEY (HomePlanId)
-- ) ENGINE=INNODB;

-- CREATE TABLE HomePlanDetail (
--     HomePlanDetailId INT NOT NULL AUTO_INCREMENT,
--     HomePlanId INT NOT NULL,
-- 	AttributeNm VARCHAR(100) NOT NULL,
--     AttributeValue VARCHAR(100) NOT NULL,
--     SortKey INT DEFAULT 0,
--     #for audit
--     DeleteInd TINYINT Default 0,
--     CreatedBy VARCHAR(100) NOT NULL,
--     CreateDttm DATETIME NOT NULL,
--     UpdatedBy VARCHAR(100) NOT NULL,
--     UpdateDttm DATETIME NOT NULL,
--     PRIMARY KEY (HomePlanDetailId),
--     CONSTRAINT FK_HomePlan_HomePlanDetail FOREIGN KEY (HomePlanId)
--     REFERENCES HomePlan(HomePlanId)
-- ) ENGINE=INNODB;

-- # AttributeNm  possible values
-- # FloorPlanImages
-- # ExteriorImages
-- # InteriorImages
