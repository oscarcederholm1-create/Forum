-- One-time repair for the existing forum database, where IDs were all zero.
-- The two legacy replies are assigned to the "hej123" topic.
UPDATE users
SET id = 1
WHERE id = 0 AND username = 'sleep';

UPDATE topics
SET id = 1, user_id = 1
WHERE id = 0 AND title = 'hej123';

UPDATE topics
SET id = 2, user_id = 1
WHERE id = 0 AND title = 'Hej!';

UPDATE posts
SET id = 1, topic_id = 1, user_id = 1
WHERE id = 0 AND content = 'hello';

UPDATE posts
SET id = 2, topic_id = 1, user_id = 1
WHERE id = 0 AND content = 'Jag heter oscar';

ALTER TABLE users
    MODIFY id int(11) NOT NULL AUTO_INCREMENT,
    ADD PRIMARY KEY (id),
    MODIFY created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP;

ALTER TABLE topics
    MODIFY id int(11) NOT NULL AUTO_INCREMENT,
    ADD PRIMARY KEY (id),
    MODIFY created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP;

ALTER TABLE posts
    MODIFY id int(11) NOT NULL AUTO_INCREMENT,
    ADD PRIMARY KEY (id),
    ADD INDEX idx_posts_topic_id (topic_id),
    MODIFY created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP;
