ALTER TABLE users
DROP COLUMN username,
DROP CONSTRAINT unique_username,
DROP COLUMN title,
DROP COLUMN company,
DROP COLUMN dob,
DROP COLUMN address,
DROP COLUMN avatar;
