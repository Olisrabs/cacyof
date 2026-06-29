-- Run this once in your cacyof_db database if the executives table doesn't exist yet
CREATE TABLE IF NOT EXISTS `executives` (
    `id`         INT(11)      NOT NULL AUTO_INCREMENT,
    `uin`        VARCHAR(50)  NOT NULL,
    `name`       VARCHAR(255) NOT NULL,
    `office`     VARCHAR(255) NOT NULL,
    `regime`     VARCHAR(100) NOT NULL,
    `passport`   VARCHAR(255) DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `uin` (`uin`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- If the table already exists, add the passport column:
-- ALTER TABLE `executives` ADD COLUMN `passport` VARCHAR(255) DEFAULT NULL AFTER `regime`;
