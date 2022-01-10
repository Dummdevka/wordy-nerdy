DROP table IF EXISTS web_examples;

CREATE TABLE IF NOT EXISTS web_examples (
    id INT(6) NOT NULL AUTO_INCREMENT,
    url_id INT(6),
    sentence TEXT NOT NULL,
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    CONSTRAINT fk_url_id FOREIGN KEY (url_id) 
    REFERENCES urls(id)
)