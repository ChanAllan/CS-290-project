CREATE TABLE video_game_list (
id int(11) NOT NULL AUTO_INCREMENT,
name varchar(255) NOT NULL,
category varchar(255) NOT NULL,
rating int(11),
username varchar(255),
PRIMARY KEY (id),
UNIQUE KEY (name, username)
) ENGINE = InnoDB;