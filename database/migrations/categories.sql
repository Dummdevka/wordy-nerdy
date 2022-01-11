DROP table IF EXISTS categories;

CREATE TABLE IF NOT EXISTS categories (
    id INT(6) NOT NULL AUTO_INCREMENT,
    name VARCHAR(120),
        PRIMARY KEY (`id`)

)