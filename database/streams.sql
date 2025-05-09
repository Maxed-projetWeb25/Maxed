-- Create streams table
CREATE TABLE IF NOT EXISTS streams (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    status ENUM('active', 'ended') DEFAULT 'active',
    created_at DATETIME NOT NULL,
    ended_at DATETIME,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Create stream viewers table
CREATE TABLE IF NOT EXISTS stream_viewers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    stream_id INT NOT NULL,
    user_id INT NOT NULL,
    joined_at DATETIME NOT NULL,
    FOREIGN KEY (stream_id) REFERENCES streams(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Create stream chat messages table
CREATE TABLE IF NOT EXISTS stream_chat_messages (
    id INT PRIMARY KEY AUTO_INCREMENT,
    stream_id INT NOT NULL,
    user_id INT NOT NULL,
    message TEXT NOT NULL,
    created_at DATETIME NOT NULL,
    FOREIGN KEY (stream_id) REFERENCES streams(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
); 