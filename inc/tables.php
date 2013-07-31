<!-- 
CREATE DATABASE db_valuoo;

CREATE TABLE tbl_mutual_funds(
id INT AUTO_INCREMENT PRIMARY KEY,
scheme_code INT NOT NULL,
scheme_name VARCHAR(255),
net_asset_value DECIMAL(15,6),
scheme_date DATE default '0000-00-00'
);
ALTER TABLE tbl_mutual_funds ENGINE = MyISAM;
ALTER TABLE tbl_mutual_funds ADD FULLTEXT (`scheme_name`);
 -->