USE wordy;
DROP table IF EXISTS wd_books;

CREATE TABLE IF NOT EXISTS wd_books (
    id INT(6) NOT NULL AUTO_INCREMENT,
    title VARCHAR(120) DEFAULT 'unknown',
    sentence TEXT NOT NULL,
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
)
