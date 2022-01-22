DROP table IF EXISTS favorites;

CREATE TABLE IF NOT EXISTS favorites (
    id INT(6) NOT NULL AUTO_INCREMENT,
    user_id INT(6),
    sentence TEXT NOT NULL,
    PRIMARY KEY (`id`),
    CONSTRAINT fk_user_id FOREIGN KEY (user_id)
    REFERENCES users(id)
    ON DELETE CASCADE ON UPDATE NO ACTION
)ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;