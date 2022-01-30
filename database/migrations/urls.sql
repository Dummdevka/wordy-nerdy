DROP table IF EXISTS urls;

CREATE TABLE IF NOT EXISTS urls (
    id INT(6) NOT NULL AUTO_INCREMENT,
    name VARCHAR(120),
    category_id INT(6), -- so a url can only have 1 category?
    PRIMARY KEY (`id`),
    CONSTRAINT fk_category_id FOREIGN KEY (category_id)
    REFERENCES categories(id)
    ON DELETE SET NULL ON UPDATE NO ACTION
);

-- I might consider putting these inserts into their own files so you can run the two things separately
-- Also, what categories are 2 and 3? They don't seem to exist in the migrations! D=
insert into urls (name, category_id) values ('http://thefashionguitar.com/', 2);
insert into urls (name, category_id) values ('https://reflectionsofthenaturalworld.com/', 3);
insert into urls (name, category_id) values ('https://johnmuirlaws.com/', 3);
