CREATE TABLE `mq_xxx` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `create_dt` DATETIME NOT NULL COMMENT '创建时间',
    `exec_dt` DATETIME NOT NULL COMMENT '执行时间',
    `fail_log` TEXT CHARACTER SET ascii COLLATE ascii_general_ci NULL COMMENT '执行失败的日志',
    `type` VARCHAR(127) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL COMMENT '消息类型',
    `content` TEXT COMMENT '消息内容',
    CONSTRAINT PRIMARY KEY (`id`),
    INDEX `exec_dt_idx` (`exec_dt`)
) ENGINE=InnoDB, DEFAULT CHARACTER SET=utf8, DEFAULT COLLATE=utf8_general_ci, COMMENT='消息队列表';

CREATE TABLE `mq_fail` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `create_dt` DATETIME NOT NULL COMMENT '同消息队列表中的create_dt, 非本表中这条记录的创建时间',
    `exec_dt` DATETIME NOT NULL COMMENT '同消息队列表中的exec_dt',
    `fail_log` TEXT CHARACTER SET ascii COLLATE ascii_general_ci NULL COMMENT '同消息队列表中的fail_log',
    `type` VARCHAR(127) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL COMMENT '同消息队列表中的type',
    `content` TEXT COMMENT '同消息队列表中的content',
    CONSTRAINT PRIMARY KEY (`id`)
) ENGINE=InnoDB, DEFAULT CHARACTER SET=utf8, DEFAULT COLLATE=utf8_general_ci, COMMENT='执行失败的消息队列表, 消息队列表中执行失败的消息在尝试若干次仍旧执行失败的话, 将会被移到这个表中';