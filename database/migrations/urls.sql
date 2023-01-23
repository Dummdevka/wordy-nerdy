DROP table IF EXISTS urls;

CREATE TABLE IF NOT EXISTS urls (
    id INT(6) NOT NULL AUTO_INCREMENT,
    name VARCHAR(120),
    category_id INT(6),
    PRIMARY KEY (`id`),
    CONSTRAINT fk_category_id FOREIGN KEY (category_id)
    REFERENCES categories(id)
    ON DELETE SET NULL ON UPDATE NO ACTION
);
