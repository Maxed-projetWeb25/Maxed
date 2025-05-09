-- Create database if it doesn't exist
CREATE DATABASE IF NOT EXISTS feed;
USE feed;

-- Create post table
CREATE TABLE IF NOT EXISTS post (
    postid INT AUTO_INCREMENT PRIMARY KEY,
    userid INT NOT NULL,
    description TEXT,
    media LONGBLOB,
    posttype VARCHAR(50) NOT NULL,
    visibility VARCHAR(20) NOT NULL,
    date_posted TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (userid) REFERENCES user(id)
);

-- Create react table for likes
CREATE TABLE IF NOT EXISTS react (
    postid INT,
    userid INT,
    type VARCHAR(20) NOT NULL,
    reacttime TIMESTAMP NULL,
    PRIMARY KEY (postid, userid),
    FOREIGN KEY (postid) REFERENCES post(postid),
    FOREIGN KEY (userid) REFERENCES user(id)
);

-- Create repost table
CREATE TABLE IF NOT EXISTS repost (
    postid INT,
    userid INT,
    reposttime TIMESTAMP NULL,
    PRIMARY KEY (postid, userid),
    FOREIGN KEY (postid) REFERENCES post(postid),
    FOREIGN KEY (userid) REFERENCES user(id)
); 