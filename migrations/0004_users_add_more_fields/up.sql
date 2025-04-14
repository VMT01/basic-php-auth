ALTER TABLE users
ADD COLUMN username VARCHAR(50) NOT NULL,
ADD CONSTRAINT unique_username UNIQUE (username),
ADD COLUMN title VARCHAR(255) NULL,
ADD COLUMN company VARCHAR(255) NOT NULL DEFAULT 'Base Inc',
ADD COLUMN dob DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
ADD COLUMN address VARCHAR(255) NULL,
ADD COLUMN avatar VARCHAR(255) NULL;
