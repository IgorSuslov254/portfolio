CREATE TABLE amo_tokens(
    id INT PRIMARY KEY AUTO_INCREMENT,
    access_token TEXT,
    refresh_token TEXT,
    token_type TEXT,
    expires_in TEXT,
    endTokenTime TEXT
) ENGINE = InnoDB; ALTER TABLE
    amo_tokens CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;