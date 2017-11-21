-- ----------------------------
-- Table structure for admin
-- ----------------------------
DROP TABLE IF EXISTS `admin`;
CREATE TABLE `admin` (
  `id`               INT(11)      NOT NULL AUTO_INCREMENT,
  `admin_name`       VARCHAR(255) NOT NULL,
  `admin_account`    VARCHAR(255) NOT NULL,
  `admin_password`   VARCHAR(255) NOT NULL,
  `admin_auth`       VARCHAR(255)          DEFAULT NULL,
  `admin_last_login` DATETIME              DEFAULT NULL,
  `admin_ip_address` VARCHAR(255)          DEFAULT NULL,
  PRIMARY KEY (`id`, `admin_account`)
)
  ENGINE = InnoDB
  AUTO_INCREMENT = 1
  DEFAULT CHARSET = utf8;

-- ----------------------------
-- Table structure for admin_login_records
-- ----------------------------
DROP TABLE IF EXISTS `admin_login_records`;
CREATE TABLE `admin_login_records` (
  `id`      INT(11) NOT NULL AUTO_INCREMENT,
  `account` VARCHAR(255)     DEFAULT NULL,
  `ip`      VARCHAR(255)     DEFAULT NULL,
  `ldate`   DATETIME         DEFAULT NULL,
  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  AUTO_INCREMENT = 1
  DEFAULT CHARSET = utf8mb4;

-- ----------------------------
-- Table structure for clients
-- ----------------------------
DROP TABLE IF EXISTS `clients`;
CREATE TABLE `clients` (
  `client_id`            INT(25)             NOT NULL AUTO_INCREMENT
  COMMENT '会员卡号',
  `client_nickname`      VARCHAR(512)
                         COLLATE utf8mb4_bin NOT NULL,
  `client_name`          VARCHAR(512)
                         COLLATE utf8mb4_bin NOT NULL
  COMMENT '会员姓名',
  `client_sex`           VARCHAR(1)
                         COLLATE utf8mb4_bin          DEFAULT NULL
  COMMENT '会员性别',
  `client_phone`         VARCHAR(20)
                         COLLATE utf8mb4_bin NOT NULL DEFAULT ''
  COMMENT '会员电话',
  `client_email`         VARCHAR(255)
                         COLLATE utf8mb4_bin          DEFAULT NULL,
  `client_head`          VARCHAR(255)
                         COLLATE utf8mb4_bin          DEFAULT NULL,
  `client_head_lastmod`  DATETIME                     DEFAULT NULL,
  `client_password`      VARCHAR(255)
                         COLLATE utf8mb4_bin          DEFAULT ''
  COMMENT '会员密码',
  `client_level`         TINYINT(3)                   DEFAULT '0'
  COMMENT '会员种类\\r\\n1为普通会员\\r\\n0为合作商',
  `client_wechat_openid` VARCHAR(50)
                         CHARACTER SET utf8  NOT NULL DEFAULT ''
  COMMENT '会员微信openid',
  `client_joindate`      DATE                NOT NULL,
  `client_province`      VARCHAR(60)
                         COLLATE utf8mb4_bin          DEFAULT NULL,
  `client_city`          VARCHAR(60)
                         COLLATE utf8mb4_bin          DEFAULT NULL,
  `client_address`       VARCHAR(60)
                         COLLATE utf8mb4_bin          DEFAULT ''
  COMMENT '会员住址',
  `client_money`         FLOAT(15, 2)        NOT NULL DEFAULT '0.00'
  COMMENT '会员存款',
  `client_credit`        INT(15)             NOT NULL DEFAULT '0'
  COMMENT '会员积分',
  `client_remark`        VARCHAR(255)
                         COLLATE utf8mb4_bin          DEFAULT ''
  COMMENT '会员备注',
  `client_groupid`       INT(11)                      DEFAULT '0',
  `client_storeid`       INT(10)                      DEFAULT '0'
  COMMENT '会员所属店号',
  `client_personid`      VARCHAR(255)
                         COLLATE utf8mb4_bin          DEFAULT NULL,
  `client_comid`         INT(11)                      DEFAULT '0',
  `client_autoenvrec`    TINYINT(4)                   DEFAULT '0',
  `unionid`              VARCHAR(256)
                         COLLATE utf8mb4_bin          DEFAULT NULL,
  `is_com`               TINYINT(4)                   DEFAULT '0',
  `deleted`              TINYINT(1)                   DEFAULT '0',
  PRIMARY KEY (`client_id`),
  UNIQUE KEY `index_openid` (`client_wechat_openid`) USING BTREE
)
  ENGINE = InnoDB
  AUTO_INCREMENT = 1
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_bin;

-- ----------------------------
-- Table structure for clients_group
-- ----------------------------
DROP TABLE IF EXISTS `clients_group`;
CREATE TABLE `clients_group` (
  `id`    INT(11) NOT NULL AUTO_INCREMENT,
  `gid`   INT(11)          DEFAULT NULL,
  `name`  VARCHAR(255)     DEFAULT NULL,
  `count` INT(11)          DEFAULT NULL,
  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

-- ----------------------------
-- Table structure for client_addresses
-- ----------------------------
DROP TABLE IF EXISTS `client_addresses`;
CREATE TABLE `client_addresses` (
  `aid`      INT(11)                 NOT NULL AUTO_INCREMENT,
  `uid`      INT(11)                 NOT NULL DEFAULT '0',
  `uname`    VARCHAR(255)
             COLLATE utf8_general_ci NOT NULL,
  `phone`    VARCHAR(255)
             COLLATE utf8_general_ci NOT NULL,
  `province` VARCHAR(255)
             COLLATE utf8_general_ci          DEFAULT NULL,
  `city`     VARCHAR(255)
             COLLATE utf8_general_ci          DEFAULT NULL,
  `dist`     VARCHAR(255)
             COLLATE utf8_general_ci          DEFAULT NULL,
  `addrs`    VARCHAR(255)
             COLLATE utf8_general_ci          DEFAULT NULL,
  `poscode`  VARCHAR(32)
             COLLATE utf8_general_ci          DEFAULT NULL,
  PRIMARY KEY (`aid`)
)
  ENGINE = InnoDB
  AUTO_INCREMENT = 1
  DEFAULT CHARSET = utf8
  COLLATE = utf8_general_ci;

-- ----------------------------
-- Table structure for client_autoenvs
-- ----------------------------
DROP TABLE IF EXISTS `client_autoenvs`;
CREATE TABLE `client_autoenvs` (
  `id`     INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `openid` VARCHAR(255)
           COLLATE utf8mb4_bin       DEFAULT NULL,
  `envid`  INT(11)                   DEFAULT NULL,
  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_bin;

-- ----------------------------
-- Table structure for client_credit_record
-- ----------------------------
DROP TABLE IF EXISTS `client_credit_record`;
CREATE TABLE `client_credit_record` (
  `id`      INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uid`     INT(11)                   DEFAULT NULL,
  `amount`  INT(5)                    DEFAULT NULL,
  `dt`      DATETIME                  DEFAULT NULL,
  `reltype` TINYINT(2)                DEFAULT NULL,
  `relid`   INT(11)                   DEFAULT NULL,
  `remark`  VARCHAR(255)
            CHARACTER SET utf8mb4
            COLLATE utf8mb4_bin       DEFAULT NULL,
  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

-- ----------------------------
-- Table structure for client_envelopes
-- ----------------------------
DROP TABLE IF EXISTS `client_envelopes`;
CREATE TABLE `client_envelopes` (
  `id`     INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `openid` VARCHAR(255)
           COLLATE utf8mb4_bin       DEFAULT NULL,
  `uid`    INT(11)                   DEFAULT NULL,
  `envid`  INT(11)                   DEFAULT NULL,
  `count`  INT(11)                   DEFAULT '0',
  `exp`    DATETIME                  DEFAULT NULL,
  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  AUTO_INCREMENT = 1
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_bin;

-- ----------------------------
-- Table structure for client_envelopes_type
-- ----------------------------
DROP TABLE IF EXISTS `client_envelopes_type`;
CREATE TABLE `client_envelopes_type` (
  `id`         INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`       VARCHAR(255)
               COLLATE utf8mb4_bin       DEFAULT NULL,
  `type`       INT(11)                   DEFAULT '0',
  `req_amount` FLOAT                     DEFAULT NULL,
  `dis_amount` FLOAT                     DEFAULT NULL,
  `pid`        VARCHAR(255)
               COLLATE utf8mb4_bin       DEFAULT NULL,
  `remark`     VARCHAR(255)
               COLLATE utf8mb4_bin       DEFAULT NULL,
  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  AUTO_INCREMENT = 1
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_bin;

-- ----------------------------
-- Table structure for client_level
-- ----------------------------
DROP TABLE IF EXISTS `client_level`;
CREATE TABLE `client_level` (
  `id`                INT(11) UNSIGNED   NOT NULL AUTO_INCREMENT,
  `level_name`        VARCHAR(255)
                      CHARACTER SET utf8 NOT NULL DEFAULT ''
  COMMENT '名称',
  `level_credit`      INT(11)            NOT NULL DEFAULT '0'
  COMMENT '升级积分要求',
  `level_discount`    FLOAT(5, 2)                 DEFAULT '1.00'
  COMMENT '享受折扣 1-100的百分数',
  `level_credit_feed` FLOAT(5, 2)                 DEFAULT '100.00'
  COMMENT '积分返比（一元返多少积分）',
  `upable`            TINYINT(1)                  DEFAULT '0'
  COMMENT '可升级',
  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  AUTO_INCREMENT = 1
  DEFAULT CHARSET = utf8
  COLLATE = utf8_general_ci;

-- ----------------------------
-- Table structure for client_messages
-- ----------------------------
DROP TABLE IF EXISTS `client_messages`;
CREATE TABLE `client_messages` (
  `id`        INT(11) NOT NULL AUTO_INCREMENT,
  `openid`    VARCHAR(255)     DEFAULT NULL,
  `msgtype`   TINYINT(2)       DEFAULT '0',
  `msgcont`   TEXT,
  `msgdirect` TINYINT(4)       DEFAULT '0',
  `autoreped` TINYINT(4)       DEFAULT '0',
  `send_time` DATETIME         DEFAULT NULL,
  `sreaded`   TINYINT(4)       DEFAULT '0',
  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  AUTO_INCREMENT = 1
  DEFAULT CHARSET = utf8;

-- ----------------------------
-- Table structure for client_message_session
-- ----------------------------
DROP TABLE IF EXISTS `client_message_session`;
CREATE TABLE `client_message_session` (
  `id`       INT(11) NOT NULL AUTO_INCREMENT,
  `openid`   VARCHAR(255)     DEFAULT NULL,
  `unread`   INT(11)          DEFAULT '0',
  `undesc`   VARCHAR(255)     DEFAULT NULL,
  `lasttime` DATETIME         DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniopenid` (`openid`) USING BTREE
)
  ENGINE = InnoDB
  AUTO_INCREMENT = 1
  DEFAULT CHARSET = utf8;

-- ----------------------------
-- Table structure for client_order_address
-- ----------------------------
DROP TABLE IF EXISTS `client_order_address`;
CREATE TABLE `client_order_address` (
  `addr_id`     INT(11)                 NOT NULL AUTO_INCREMENT,
  `client_id`   INT(11)                 NOT NULL DEFAULT '0',
  `name`        VARCHAR(255)
                COLLATE utf8_general_ci NOT NULL,
  `tel`         VARCHAR(255)
                COLLATE utf8_general_ci NOT NULL,
  `postal_code` VARCHAR(255)
                COLLATE utf8_general_ci          DEFAULT NULL,
  `address`     VARCHAR(255)
                COLLATE utf8_general_ci          DEFAULT NULL,
  PRIMARY KEY (`addr_id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_general_ci;

-- ----------------------------
-- Table structure for client_product_likes
-- ----------------------------
DROP TABLE IF EXISTS `client_product_likes`;
CREATE TABLE `client_product_likes` (
  `id`         INT(11)      NOT NULL AUTO_INCREMENT,
  `openid`     VARCHAR(255) NOT NULL,
  `product_id` INT(11)      NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uni` (`openid`, `product_id`) USING BTREE,
  KEY `uopenid` (`openid`) USING BTREE
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

-- ----------------------------
-- Table structure for client_sign_record
-- ----------------------------
DROP TABLE IF EXISTS `client_sign_record`;
CREATE TABLE `client_sign_record` (
  `id`     INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `dt`     DATE                      DEFAULT NULL,
  `credit` INT(11)                   DEFAULT NULL,
  `openid` VARCHAR(150)
           COLLATE utf8mb4_bin       DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `dt` (`dt`, `openid`) USING BTREE
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_bin;

-- ----------------------------
-- Table structure for companys
-- ----------------------------
DROP TABLE IF EXISTS `companys`;
CREATE TABLE `companys` (
  `id`              INT(11)      NOT NULL AUTO_INCREMENT,
  `uid`             INT(11)      NOT NULL DEFAULT '0',
  `name`            VARCHAR(255) NOT NULL,
  `email`           VARCHAR(100)          DEFAULT NULL,
  `phone`           VARCHAR(20)           DEFAULT NULL,
  `join_date`       DATE                  DEFAULT NULL,
  `openid`          VARCHAR(255)          DEFAULT NULL,
  `return_percent`  FLOAT(5, 3)           DEFAULT '0.050',
  `money`           FLOAT                 DEFAULT '0',
  `bank_name`       VARCHAR(255)          DEFAULT NULL,
  `bank_account`    VARCHAR(255)          DEFAULT NULL,
  `bank_personname` VARCHAR(255)          DEFAULT NULL,
  `alipay`          VARCHAR(255)          DEFAULT NULL
  COMMENT '支付宝账号',
  `person_id`       VARCHAR(255)          DEFAULT NULL,
  `password`        VARCHAR(255)          DEFAULT NULL,
  `login_ip`        VARCHAR(255)          DEFAULT NULL,
  `deleted`         TINYINT(4)   NOT NULL DEFAULT '0',
  `utype`           TINYINT(4)            DEFAULT NULL,
  `verifed`         TINYINT(4)            DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniname` (`name`, `email`, `phone`) USING BTREE
)
  ENGINE = InnoDB
  AUTO_INCREMENT = 1
  DEFAULT CHARSET = utf8;

-- ----------------------------
-- Table structure for company_bills
-- ----------------------------
DROP TABLE IF EXISTS `company_bills`;
CREATE TABLE `company_bills` (
  `id`          INT(11) NOT NULL AUTO_INCREMENT,
  `comid`       INT(11)          DEFAULT NULL,
  `bill_amount` FLOAT(10, 2)     DEFAULT NULL,
  `bill_time`   DATETIME         DEFAULT NULL,
  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

-- ----------------------------
-- Table structure for company_income_record
-- ----------------------------
DROP TABLE IF EXISTS `company_income_record`;
CREATE TABLE `company_income_record` (
  `record_id` INT(11)                 NOT NULL AUTO_INCREMENT,
  `amount`    FLOAT(11, 2)            NOT NULL DEFAULT '0.00',
  `date`      DATETIME                NOT NULL,
  `client_id` INT(11)                          DEFAULT NULL,
  `order_id`  INT(11)                 NOT NULL,
  `com_id`    VARCHAR(255)
              COLLATE utf8_general_ci NOT NULL,
  `pcount`    INT(11)                 NOT NULL,
  `is_seted`  TINYINT(4)                       DEFAULT '0',
  `is_reqed`  TINYINT(4)                       DEFAULT '0',
  PRIMARY KEY (`record_id`)
)
  ENGINE = InnoDB
  AUTO_INCREMENT = 1
  DEFAULT CHARSET = utf8
  COLLATE = utf8_general_ci;

-- ----------------------------
-- Table structure for company_spread_record
-- ----------------------------
DROP TABLE IF EXISTS `company_spread_record`;
CREATE TABLE `company_spread_record` (
  `rid`        INT(11)                 NOT NULL AUTO_INCREMENT,
  `com_id`     VARCHAR(255)
               COLLATE utf8_general_ci NOT NULL,
  `product_id` INT(11)                 NOT NULL,
  `readi`      INT(11)                 NOT NULL DEFAULT '1',
  `turned`     INT(11)                 NOT NULL DEFAULT '0',
  PRIMARY KEY (`rid`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_general_ci;

-- ----------------------------
-- Table structure for company_spread_record_details
-- ----------------------------
DROP TABLE IF EXISTS `company_spread_record_details`;
CREATE TABLE `company_spread_record_details` (
  `record_id`  INT(11) NOT NULL AUTO_INCREMENT,
  `spread_id`  INT(11) NOT NULL,
  `cclient_id` INT(11) NOT NULL,
  PRIMARY KEY (`record_id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_general_ci;

-- ----------------------------
-- Table structure for company_users
-- ----------------------------
DROP TABLE IF EXISTS `company_users`;
CREATE TABLE `company_users` (
  `id`     INT(11) NOT NULL AUTO_INCREMENT,
  `uid`    INT(11)          DEFAULT NULL,
  `openid` VARCHAR(255)     DEFAULT NULL,
  `comid`  INT(11)          DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniopenid` (`openid`) USING BTREE
)
  ENGINE = InnoDB
  AUTO_INCREMENT = 1
  DEFAULT CHARSET = utf8;

-- ----------------------------
-- Table structure for credit_exchange_products
-- ----------------------------
DROP TABLE IF EXISTS `credit_exchange_products`;
CREATE TABLE `credit_exchange_products` (
  `product_id`      INT(11) NOT NULL,
  `product_credits` INT(11) DEFAULT NULL,
  PRIMARY KEY (`product_id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4;

-- ----------------------------
-- Table structure for envs_robblist
-- ----------------------------
DROP TABLE IF EXISTS `envs_robblist`;
CREATE TABLE `envs_robblist` (
  `id`      INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `key`     VARCHAR(255)
            COLLATE utf8mb4_bin       DEFAULT NULL,
  `name`    VARCHAR(255)
            COLLATE utf8mb4_bin       DEFAULT NULL,
  `on`      INT(11)                   DEFAULT NULL,
  `remains` INT(11)                   DEFAULT NULL,
  `envsid`  INT(11)                   DEFAULT NULL,
  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_bin;

-- ----------------------------
-- Table structure for envs_robrecord
-- ----------------------------
DROP TABLE IF EXISTS `envs_robrecord`;
CREATE TABLE `envs_robrecord` (
  `id`     INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `openid` VARCHAR(255)
           COLLATE utf8mb4_bin       DEFAULT NULL,
  `envsid` INT(11)                   DEFAULT NULL,
  `eid`    INT(11)                   DEFAULT NULL,
  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_bin;

-- ----------------------------
-- Table structure for feedbacks
-- ----------------------------
DROP TABLE IF EXISTS `feedbacks`;
CREATE TABLE `feedbacks` (
  `id`       INT(11) NOT NULL AUTO_INCREMENT,
  `uid`      INT(11)          DEFAULT NULL,
  `feedback` TEXT,
  `ftime`    DATETIME         DEFAULT NULL,
  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  AUTO_INCREMENT = 1
  DEFAULT CHARSET = utf8
  ROW_FORMAT = COMPACT;

-- ----------------------------
-- Table structure for gmess_category
-- ----------------------------
DROP TABLE IF EXISTS `gmess_category`;
CREATE TABLE `gmess_category` (
  `id`       INT(11)      NOT NULL AUTO_INCREMENT,
  `cat_name` VARCHAR(255) NOT NULL,
  `parent`   INT(11)               DEFAULT '0',
  `sort`     TINYINT(4)            DEFAULT NULL,
  `deleted`  TINYINT(4)            DEFAULT '0',
  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

-- ----------------------------
-- Table structure for gmess_page
-- ----------------------------
DROP TABLE IF EXISTS `gmess_page`;
CREATE TABLE `gmess_page` (
  `id`             INT(11) NOT NULL AUTO_INCREMENT,
  `title`          VARCHAR(255)     DEFAULT NULL,
  `content`        TEXT,
  `desc`           VARCHAR(255)     DEFAULT NULL,
  `catimg`         VARCHAR(255)     DEFAULT NULL,
  `thumb_media_id` VARCHAR(255)     DEFAULT NULL,
  `media_id`       VARCHAR(255)     DEFAULT NULL,
  `createtime`     DATE             DEFAULT NULL,
  `category`       INT(11)          DEFAULT NULL,
  `deleted`        TINYINT(4)       DEFAULT '0',
  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  AUTO_INCREMENT = 1
  DEFAULT CHARSET = utf8;

-- ----------------------------
-- Table structure for gmess_send_stat
-- ----------------------------
DROP TABLE IF EXISTS `gmess_send_stat`;
CREATE TABLE `gmess_send_stat` (
  `id`            INT(11) NOT NULL       AUTO_INCREMENT,
  `msg_id`        INT(11) NOT NULL,
  `send_date`     DATETIME               DEFAULT NULL,
  `send_count`    INT(11)                DEFAULT NULL,
  `read_count`    INT(11)                DEFAULT '0',
  `share_count`   INT(11)                DEFAULT '0',
  `receive_count` INT(11)                DEFAULT NULL,
  `send_type`     TINYINT(4)             DEFAULT '0',
  `msg_type`      ENUM('text', 'images') DEFAULT 'images',
  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  AUTO_INCREMENT = 1
  DEFAULT CHARSET = utf8;

-- ----------------------------
-- Table structure for orders
-- ----------------------------
DROP TABLE IF EXISTS `orders`;
CREATE TABLE `orders` (
  `order_id`            INT(11)                 NOT NULL AUTO_INCREMENT
  COMMENT '订单编号',
  `client_id`           INT(11)                 NULL     DEFAULT NULL
  COMMENT '客户编号',
  `order_time`          DATETIME                NULL     DEFAULT NULL
  COMMENT '订单交易时间',
  `receive_time`        DATETIME                NULL     DEFAULT NULL
  COMMENT '收货时间',
  `send_time`           DATETIME                NULL     DEFAULT NULL
  COMMENT '发货时间',
  `order_balance`       FLOAT(10, 2)            NULL     DEFAULT 0.00
  COMMENT '余额抵现',
  `order_yunfei`        FLOAT(10, 2)            NULL     DEFAULT 0.00
  COMMENT '运费',
  `order_amount`        FLOAT(10, 2)            NULL     DEFAULT 0.00
  COMMENT '总价',
  `order_refund_amount` FLOAT(10, 2)            NULL     DEFAULT 0.00
  COMMENT '已退款金额',
  `supply_price_amount` FLOAT(10, 2)            NULL     DEFAULT 0.00
  COMMENT '供应商金额',
  `original_amount`     FLOAT(10, 2)            NULL     DEFAULT 0.00
  COMMENT '供应商总价',
  `company_com`         VARCHAR(255)
                        CHARACTER SET utf8
                        COLLATE utf8_general_ci NULL     DEFAULT '0'
  COMMENT '代理id',
  `envs_id`             INT(11)                 NULL     DEFAULT 0
  COMMENT '红包id',
  `product_count`       INT(11)                 NULL     DEFAULT 0
  COMMENT '商品数量',
  `order_dixian`        FLOAT(10, 2)            NULL     DEFAULT 0.00,
  `serial_number`       VARCHAR(30)
                        CHARACTER SET utf8
                        COLLATE utf8_general_ci NULL     DEFAULT NULL,
  `wepay_serial`        VARCHAR(50)
                        CHARACTER SET utf8
                        COLLATE utf8_general_ci NULL     DEFAULT NULL,
  `wepay_openid`        VARCHAR(255)
                        CHARACTER SET utf8
                        COLLATE utf8_general_ci NULL     DEFAULT '',
  `wepay_unionid`       VARCHAR(255)
                        CHARACTER SET utf8
                        COLLATE utf8_general_ci NULL     DEFAULT NULL,
  `bank_billno`         VARCHAR(255)
                        CHARACTER SET utf8
                        COLLATE utf8_general_ci NULL     DEFAULT '',
  `leword`              TEXT
                        CHARACTER SET utf8
                        COLLATE utf8_general_ci NULL
  COMMENT '订单备注',
  `status`              ENUM('unpay', 'payed', 'received', 'canceled', 'closed', 'refunded', 'delivering', 'reqing')
                        CHARACTER SET utf8
                        COLLATE utf8_general_ci NOT NULL DEFAULT 'unpay'
  COMMENT '订单状态',
  `express_openid`      VARCHAR(255)
                        CHARACTER SET utf8
                        COLLATE utf8_general_ci NULL     DEFAULT NULL,
  `express_code`        VARCHAR(255)
                        CHARACTER SET utf8
                        COLLATE utf8_general_ci NULL     DEFAULT NULL,
  `express_com`         VARCHAR(255)
                        CHARACTER SET utf8
                        COLLATE utf8_general_ci NULL     DEFAULT NULL,
  `exptime`             VARCHAR(255)
                        CHARACTER SET utf8
                        COLLATE utf8_general_ci NULL     DEFAULT NULL,
  `enterprise_id`       INT(11)                 NULL     DEFAULT 0,
  `reci_head`           VARCHAR(32)
                        CHARACTER SET utf8
                        COLLATE utf8_general_ci NULL     DEFAULT NULL,
  `reci_cont`           VARCHAR(32)
                        CHARACTER SET utf8
                        COLLATE utf8_general_ci NULL     DEFAULT NULL,
  `reci_tex`            FLOAT(10, 2)            NULL     DEFAULT 0.00
  COMMENT '发票税金额',
  `is_commented`        TINYINT(1)              NULL     DEFAULT 0
  COMMENT '是否已评价',
  `address_hash`        VARCHAR(255)
                        CHARACTER SET utf8
                        COLLATE utf8_general_ci NULL     DEFAULT NULL
  COMMENT '收货地址哈希',
  PRIMARY KEY (`order_id`),
  INDEX `openid` (`wepay_openid`) USING BTREE
)
  ENGINE = InnoDB
  AUTO_INCREMENT = 1
  DEFAULT CHARSET = utf8
  COLLATE = utf8_general_ci;

-- ----------------------------
-- Table structure for orders_address
-- ----------------------------
DROP TABLE IF EXISTS `orders_address`;
CREATE TABLE `orders_address` (
  `addr_id`     INT(11)                 NOT NULL AUTO_INCREMENT,
  `client_id`   INT(11)                 NOT NULL DEFAULT '0',
  `order_id`    INT(11)                 NOT NULL DEFAULT '0',
  `user_name`   VARCHAR(255)
                COLLATE utf8_general_ci NOT NULL,
  `tel_number`  VARCHAR(255)
                COLLATE utf8_general_ci NOT NULL,
  `province`    VARCHAR(255)
                COLLATE utf8_general_ci          DEFAULT NULL,
  `city`        VARCHAR(255)
                COLLATE utf8_general_ci          DEFAULT NULL,
  `postal_code` VARCHAR(255)
                COLLATE utf8_general_ci          DEFAULT NULL,
  `address`     VARCHAR(255)
                COLLATE utf8_general_ci          DEFAULT NULL,
  `hash`        VARCHAR(255)
                COLLATE utf8_general_ci          DEFAULT NULL,
  PRIMARY KEY (`addr_id`)
)
  ENGINE = InnoDB
  AUTO_INCREMENT = 1
  DEFAULT CHARSET = utf8
  COLLATE = utf8_general_ci;

-- ----------------------------
-- Table structure for orders_comment
-- ----------------------------
DROP TABLE IF EXISTS `orders_comment`;
CREATE TABLE `orders_comment` (
  `id`        INT(11) NOT NULL AUTO_INCREMENT,
  `openid`    VARCHAR(255)     DEFAULT NULL,
  `starts`    TINYINT(4)       DEFAULT NULL,
  `content`   TEXT,
  `mtime`     DATETIME         DEFAULT NULL,
  `orderid`   INT(11)          DEFAULT NULL,
  `anonymous` TINYINT(1)       DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `openid` (`openid`(191)) USING BTREE
)
  ENGINE = InnoDB
  AUTO_INCREMENT = 1
  DEFAULT CHARSET = utf8mb4;

-- ----------------------------
-- Table structure for orders_detail
-- ----------------------------
DROP TABLE IF EXISTS `orders_detail`;
CREATE TABLE `orders_detail` (
  `detail_id`              INT(11)      NOT NULL AUTO_INCREMENT,
  `order_id`               INT(20)      NOT NULL
  COMMENT '订单编号',
  `product_id`             INT(20)      NOT NULL
  COMMENT '商品编号',
  `product_count`          INT(10)      NOT NULL
  COMMENT '商品数量',
  `product_discount_price` FLOAT(11, 2) NOT NULL DEFAULT 0.00,
  `original_amount`        FLOAT(11, 2) NULL     DEFAULT NULL,
  `product_price_hash_id`  INT(11)      NOT NULL DEFAULT 0,
  `refunded`               TINYINT(1)   NOT NULL DEFAULT 0,
  PRIMARY KEY (`detail_id`)
)
  ENGINE = InnoDB
  AUTO_INCREMENT = 1
  DEFAULT CHARSET = utf8;

-- ----------------------------
-- Table structure for order_reqpay
-- ----------------------------
DROP TABLE IF EXISTS `order_reqpay`;
CREATE TABLE `order_reqpay` (
  `id`           INT(11) UNSIGNED    NOT NULL AUTO_INCREMENT,
  `openid`       VARCHAR(255)
                 COLLATE utf8mb4_bin NOT NULL DEFAULT '',
  `wepay_serial` VARCHAR(255)
                 COLLATE utf8mb4_bin NOT NULL DEFAULT '',
  `amount`       FLOAT               NOT NULL,
  `order_id`     INT(11)                      DEFAULT NULL,
  `dt`           DATETIME                     DEFAULT NULL,
  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  AUTO_INCREMENT = 1
  DEFAULT CHARSET = utf8
  COLLATE = utf8_general_ci;

-- ----------------------------
-- Table structure for pageview_records
-- ----------------------------
DROP TABLE IF EXISTS `pageview_records`;
CREATE TABLE `pageview_records` (
  `id`      INT(11) NOT NULL        AUTO_INCREMENT,
  `page`    VARCHAR(255)
            COLLATE utf8_general_ci DEFAULT NULL,
  `openid`  VARCHAR(255)
            COLLATE utf8_general_ci DEFAULT '',
  `ip`      VARCHAR(255)
            COLLATE utf8_general_ci DEFAULT NULL,
  `referer` VARCHAR(255)
            COLLATE utf8_general_ci DEFAULT NULL,
  `time`    DATETIME                DEFAULT NULL,
  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_general_ci;

-- ----------------------------
-- Table structure for products_info
-- ----------------------------
DROP TABLE IF EXISTS `products_info`;
CREATE TABLE `products_info` (
  `product_id`             INT(20)                 NOT NULL AUTO_INCREMENT
  COMMENT '商品编号',
  `product_code`           VARCHAR(100)
                           COLLATE utf8_general_ci NOT NULL DEFAULT '0'
  COMMENT '商品条码',
  `product_type`           VARCHAR(40)
                           CHARACTER SET utf8               DEFAULT NULL
  COMMENT '商品类型',
  `product_name`           VARCHAR(255)
                           CHARACTER SET utf8      NOT NULL
  COMMENT '商品名称',
  `product_subname`        VARCHAR(100)
                           CHARACTER SET utf8               DEFAULT NULL
  COMMENT '商品颜色',
  `product_size`           VARCHAR(40)
                           CHARACTER SET utf8               DEFAULT NULL
  COMMENT '商品大小',
  `product_cat`            INT(11)                 NOT NULL DEFAULT '1',
  `product_brand`          INT(11)                          DEFAULT '0',
  `product_readi`          INT(11)                 NOT NULL DEFAULT '0',
  `product_desc`           LONGTEXT
                           COLLATE utf8_general_ci,
  `product_subtitle`       TEXT
                           COLLATE utf8_general_ci,
  `product_serial`         INT(11)                          DEFAULT '0',
  `product_weight`         VARCHAR(11)
                           COLLATE utf8_general_ci          DEFAULT '0.00',
  `product_indexes`        VARCHAR(50)
                           COLLATE utf8_general_ci DEFAULT ''
  COMMENT '商品分类搜索索引',
  `product_online`         TINYINT(4)                       DEFAULT '1',
  `product_credit`         INT(11)                          DEFAULT '0',
  `product_prom`           INT(11)                          DEFAULT '0',
  `product_prom_limit`     INT(11)                          DEFAULT '0',
  `product_prom_limitdate` VARCHAR(0)
                           COLLATE utf8_general_ci          DEFAULT NULL,
  `product_prom_limitdays` INT(11)                          DEFAULT '0',
  `product_prom_discount`  INT(11)                          DEFAULT '0',
  `product_expfee`         FLOAT(5, 2)                      DEFAULT '0.00'
  COMMENT '商品快递费用',
  `product_supplier`       INT(11)                          DEFAULT '0',
  `product_storage`        VARCHAR(255)
                           COLLATE utf8_general_ci          DEFAULT ''
  COMMENT '存储条件',
  `product_origin`         VARCHAR(255)
                           COLLATE utf8_general_ci          DEFAULT ''
  COMMENT '商品产地',
  `product_unit`           VARCHAR(255)
                           COLLATE utf8_general_ci          DEFAULT ''
  COMMENT '商品单位',
  `product_instocks`       INT(11)                          DEFAULT '0'
  COMMENT '商品库存，在没有规格的时候此字段可用',
  `sell_price`             FLOAT(10, 2)                     DEFAULT '0.00'
  COMMENT '商品价格',
  `market_price`           FLOAT(10, 2)                     DEFAULT NULL
  COMMENT '市场参考价',
  `supply_price`           FLOAT(10, 2)                     DEFAULT '0.00'
  COMMENT '供货价',
  `catimg`                 VARCHAR(255)
                           COLLATE utf8_general_ci          DEFAULT NULL
  COMMENT '商品首图',
  `sort`                   INT(10)                          DEFAULT '0',
  `is_delete`              TINYINT(1)                       DEFAULT '0',
  PRIMARY KEY (`product_id`)
)
  ENGINE = InnoDB
  AUTO_INCREMENT = 1
  DEFAULT CHARSET = utf8
  COLLATE = utf8_general_ci;

-- ----------------------------
-- Table structure for product_brand
-- ----------------------------
DROP TABLE IF EXISTS `product_brand`;
CREATE TABLE `product_brand` (
  `id`         INT(11) NOT NULL AUTO_INCREMENT,
  `brand_name` VARCHAR(255)     DEFAULT NULL,
  `brand_img1` VARCHAR(255)     DEFAULT NULL,
  `brand_img2` VARCHAR(255)     DEFAULT NULL,
  `brand_cat`  INT(11)          DEFAULT NULL,
  `sort`       TINYINT(4)       DEFAULT '0',
  `deleted`    TINYINT(4)       DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniname` (`brand_name`) USING BTREE
)
  ENGINE = InnoDB
  AUTO_INCREMENT = 1
  DEFAULT CHARSET = utf8;

-- ----------------------------
-- Table structure for product_category
-- ----------------------------
DROP TABLE IF EXISTS `product_category`;
CREATE TABLE `product_category` (
  `cat_id`     INT(11)                 NOT NULL AUTO_INCREMENT,
  `cat_name`   VARCHAR(255)
               COLLATE utf8_general_ci NOT NULL,
  `cat_descs`  TEXT
               COLLATE utf8_general_ci,
  `cat_image`  VARCHAR(255)
               COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `cat_parent` INT(11)                 NOT NULL DEFAULT '0',
  `cat_level`  INT(11)                          DEFAULT '0',
  `cat_order`  INT(11)                 NOT NULL DEFAULT '0',
  PRIMARY KEY (`cat_id`)
)
  ENGINE = InnoDB
  AUTO_INCREMENT = 1
  DEFAULT CHARSET = utf8
  COLLATE = utf8_general_ci;

-- ----------------------------
-- Table structure for product_images
-- ----------------------------
DROP TABLE IF EXISTS `product_images`;
CREATE TABLE `product_images` (
  `product_id` INT(11)                 NOT NULL DEFAULT '0',
  `image_id`   INT(11)                 NOT NULL AUTO_INCREMENT,
  `image_path` VARCHAR(512)
               COLLATE utf8_general_ci NOT NULL,
  `image_sort` TINYINT(4)                       DEFAULT '0',
  `image_type` INT(11)                 NOT NULL DEFAULT '0',
  PRIMARY KEY (`image_id`),
  KEY `index_product` (`product_id`) USING BTREE
)
  ENGINE = InnoDB
  AUTO_INCREMENT = 1
  DEFAULT CHARSET = utf8
  COLLATE = utf8_general_ci;

-- ----------------------------
-- Table structure for product_onsale
-- ----------------------------
DROP TABLE IF EXISTS `product_onsale`;
CREATE TABLE `product_onsale` (
  `product_id`  INT(20)      NOT NULL AUTO_INCREMENT
  COMMENT '商品编号',
  `sale_prices` FLOAT(10, 2) NOT NULL DEFAULT '0.00'
  COMMENT '售价',
  `store_id`    INT(8)       NOT NULL DEFAULT '0'
  COMMENT '商店编号',
  `discount`    INT(3)       NOT NULL DEFAULT '100'
  COMMENT '折扣',
  PRIMARY KEY (`product_id`)
)
  ENGINE = InnoDB
  AUTO_INCREMENT = 1
  DEFAULT CHARSET = utf8
  COLLATE = utf8_general_ci;

-- ----------------------------
-- Table structure for product_serials
-- ----------------------------
DROP TABLE IF EXISTS `product_serials`;
CREATE TABLE `product_serials` (
  `id`           INT(11)    NOT NULL AUTO_INCREMENT,
  `serial_name`  VARCHAR(255)        DEFAULT NULL
  COMMENT '序列名称',
  `serial_image` VARCHAR(255)        DEFAULT NULL,
  `serial_desc`  VARCHAR(255)        DEFAULT NULL,
  `relcat`       TINYINT(4)          DEFAULT NULL,
  `relevel`      TINYINT(4)          DEFAULT NULL,
  `sort`         VARCHAR(255)        DEFAULT '0'
  COMMENT '排序',
  `deleted`      TINYINT(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  AUTO_INCREMENT = 1
  DEFAULT CHARSET = utf8;

-- ----------------------------
-- Table structure for product_spec
-- ----------------------------
DROP TABLE IF EXISTS `product_spec`;
CREATE TABLE `product_spec` (
  `id`           INT(11) NOT NULL AUTO_INCREMENT,
  `product_id`   INT(11) NOT NULL,
  `spec_det_id1` INT(11)          DEFAULT NULL,
  `spec_det_id2` INT(11)          DEFAULT NULL,
  `sale_price`   FLOAT(11, 2)     DEFAULT NULL,
  `market_price` FLOAT(11, 2)     DEFAULT '0.00',
  `instock`      INT(11)          DEFAULT '0',
  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  AUTO_INCREMENT = 1
  DEFAULT CHARSET = utf8;

-- ----------------------------
-- Table structure for product_view_record
-- ----------------------------
DROP TABLE IF EXISTS `product_view_record`;
CREATE TABLE `product_view_record` (
  `id`         INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `date`       DATETIME                  DEFAULT NULL,
  `openid`     VARCHAR(255)              DEFAULT NULL,
  `product_id` INT(11)                   DEFAULT NULL,
  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

-- ----------------------------
-- Table structure for wechat_autoresponse
-- ----------------------------
DROP TABLE IF EXISTS `wechat_autoresponse`;
CREATE TABLE `wechat_autoresponse` (
  `id`      INT(11) NOT NULL AUTO_INCREMENT,
  `key`     VARCHAR(255)     DEFAULT NULL,
  `message` TEXT,
  `rel`     INT(11)          DEFAULT '0',
  `reltype` TINYINT(4)       DEFAULT '0',
  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  AUTO_INCREMENT = 1
  DEFAULT CHARSET = utf8;

-- ----------------------------
-- Table structure for wechat_subscribe_record
-- ----------------------------
DROP TABLE IF EXISTS `wechat_subscribe_record`;
CREATE TABLE `wechat_subscribe_record` (
  `recordid` INT(11)                 NOT NULL AUTO_INCREMENT,
  `openid`   VARCHAR(255)
             COLLATE utf8_general_ci NOT NULL,
  `date`     DATE                             DEFAULT NULL,
  `dv`       TINYINT(4)                       DEFAULT '1',
  PRIMARY KEY (`recordid`)
)
  ENGINE = InnoDB
  AUTO_INCREMENT = 1
  DEFAULT CHARSET = utf8
  COLLATE = utf8_general_ci;

-- ----------------------------
-- Table structure for wshop_banners
-- ----------------------------
DROP TABLE IF EXISTS `wshop_banners`;
CREATE TABLE `wshop_banners` (
  `id`              INT(11) NOT NULL        AUTO_INCREMENT,
  `banner_name`     VARCHAR(255)
                    COLLATE utf8_general_ci DEFAULT NULL,
  `banner_href`     VARCHAR(255)
                    COLLATE utf8_general_ci DEFAULT NULL,
  `banner_image`    VARCHAR(255)
                    COLLATE utf8_general_ci DEFAULT NULL,
  `banner_position` TINYINT(4) DEFAULT '0',
  `reltype`         TINYINT(4) DEFAULT NULL,
  `relid`           VARCHAR(255)
                    COLLATE utf8_general_ci DEFAULT '0',
  `sort`            TINYINT(4) DEFAULT '0',
  `exp`             DATETIME DEFAULT NULL,
  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  AUTO_INCREMENT = 1
  DEFAULT CHARSET = utf8
  COLLATE = utf8_general_ci;

-- ----------------------------
-- Table structure for wshop_board_messages
-- ----------------------------
DROP TABLE IF EXISTS `wshop_board_messages`;
CREATE TABLE `wshop_board_messages` (
  `id`      INT(11) NOT NULL AUTO_INCREMENT,
  `title`   VARCHAR(255)     DEFAULT NULL,
  `content` TEXT,
  `mtime`   DATETIME         DEFAULT NULL,
  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  AUTO_INCREMENT = 1
  DEFAULT CHARSET = utf8;

-- ----------------------------
-- Table structure for wshop_discountcode
-- ----------------------------
DROP TABLE IF EXISTS `wshop_discountcode`;
CREATE TABLE `wshop_discountcode` (
  `id`            INT(11) NOT NULL AUTO_INCREMENT,
  `keywords`      VARCHAR(255)     DEFAULT NULL,
  `code_total`    INT(11)          DEFAULT NULL,
  `code_remains`  INT(11)          DEFAULT NULL,
  `code_discount` FLOAT(5, 2)      DEFAULT '0.00',
  `template`      VARCHAR(255)     DEFAULT NULL,
  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4;

-- ----------------------------
-- Table structure for wshop_discountcodes
-- ----------------------------
DROP TABLE IF EXISTS `wshop_discountcodes`;
CREATE TABLE `wshop_discountcodes` (
  `id`      INT(11) NOT NULL AUTO_INCREMENT,
  `codes`   VARCHAR(255)     DEFAULT NULL,
  `qid`     INT(11)          DEFAULT '0',
  `openid`  VARCHAR(255)     DEFAULT NULL,
  `rectime` DATETIME         DEFAULT NULL,
  `isvalid` TINYINT(1)       DEFAULT '0',
  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4;

-- ----------------------------
-- Table structure for wshop_discountcode_record
-- ----------------------------
DROP TABLE IF EXISTS `wshop_discountcode_record`;
CREATE TABLE `wshop_discountcode_record` (
  `id`      INT(11) NOT NULL AUTO_INCREMENT,
  `openid`  VARCHAR(255)     DEFAULT NULL,
  `rectime` DATETIME         DEFAULT NULL,
  `codeid`  INT(11)          DEFAULT NULL,
  `qid`     INT(11)          DEFAULT NULL,
  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4;

-- ----------------------------
-- Table structure for wshop_expresstaff
-- ----------------------------
DROP TABLE IF EXISTS `wshop_expresstaff`;
CREATE TABLE `wshop_expresstaff` (
  `id`        INT(11) NOT NULL,
  `openid`    VARCHAR(255) DEFAULT NULL,
  `headimg`   VARCHAR(255) DEFAULT NULL,
  `uname`     VARCHAR(255) DEFAULT NULL,
  `isnotify`  TINYINT(1)   DEFAULT '0',
  `isexpress` TINYINT(1)   DEFAULT '0',
  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4;

-- ----------------------------
-- Table structure for wshop_menu
-- ----------------------------
DROP TABLE IF EXISTS `wshop_menu`;
CREATE TABLE `wshop_menu` (
  `id`         INT(11) NOT NULL AUTO_INCREMENT,
  `relid`      INT(11)          DEFAULT NULL,
  `reltype`    TINYINT(4)       DEFAULT NULL,
  `relcontent` TEXT,
  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  AUTO_INCREMENT = 1
  DEFAULT CHARSET = utf8;

-- ----------------------------
-- Table structure for wshop_recomment_company
-- ----------------------------
DROP TABLE IF EXISTS `wshop_recomment_company`;
CREATE TABLE `wshop_recomment_company` (
  `id`      INT(11) NOT NULL                AUTO_INCREMENT,
  `title`   VARCHAR(255)                    DEFAULT NULL,
  `status`  ENUM('unfix', 'fixed', 'close') DEFAULT 'unfix',
  `content` TEXT,
  `comid`   INT(11)                         DEFAULT NULL,
  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  AUTO_INCREMENT = 1
  DEFAULT CHARSET = utf8;

-- ----------------------------
-- Table structure for wshop_search_record
-- ----------------------------
DROP TABLE IF EXISTS `wshop_search_record`;
CREATE TABLE `wshop_search_record` (
  `id`     INT(11) NOT NULL AUTO_INCREMENT,
  `key`    VARCHAR(255)     DEFAULT NULL,
  `openid` VARCHAR(255)     DEFAULT NULL,
  `time`   DATETIME         DEFAULT NULL,
  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  AUTO_INCREMENT = 1
  DEFAULT CHARSET = utf8;

-- ----------------------------
-- Table structure for wshop_settings
-- ----------------------------
DROP TABLE IF EXISTS `wshop_settings`;
CREATE TABLE `wshop_settings` (
  `key`      VARCHAR(50) NOT NULL DEFAULT '',
  `value`    VARCHAR(512)         DEFAULT NULL,
  `last_mod` DATETIME    NOT NULL,
  PRIMARY KEY (`key`),
  KEY `index_key` (`key`) USING BTREE
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

-- ----------------------------
-- Table structure for wshop_settings_expfee
-- ----------------------------
DROP TABLE IF EXISTS `wshop_settings_expfee`;
CREATE TABLE `wshop_settings_expfee` (
  `id`       INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `province` VARCHAR(255)
             COLLATE utf8mb4_bin       DEFAULT '',
  `citys`    VARCHAR(255)
             COLLATE utf8mb4_bin       DEFAULT NULL,
  `ffee`     FLOAT                     DEFAULT NULL,
  `ffeeadd`  FLOAT                     DEFAULT NULL,
  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  AUTO_INCREMENT = 1
  DEFAULT CHARSET = utf8
  COLLATE = utf8_general_ci;

-- ----------------------------
-- Table structure for wshop_settings_section
-- ----------------------------
DROP TABLE IF EXISTS `wshop_settings_section`;
CREATE TABLE `wshop_settings_section` (
  `id`     INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`   VARCHAR(255)
           COLLATE utf8mb4_bin       DEFAULT NULL,
  `pid`    VARCHAR(255)
           COLLATE utf8mb4_bin       DEFAULT NULL,
  `banner` VARCHAR(255)
           COLLATE utf8mb4_bin       DEFAULT NULL,
  `reltype` varchar(1)
           COLLATE utf8_general_ci   DEFAULT '0' ,
  COMMENT '首页版块类型0：产品分类 展示版块 1：产品列表 展示版块 2:图文消息 展示版块 3:超链接 展示版块 4:广告列表 展示版块',
  `relid`  INT(5)                    DEFAULT NULL,
  `bsort`  TINYINT(5)                DEFAULT '0',
  `ftime`  DATETIME                  DEFAULT NULL,
  `ttime`  DATETIME                  DEFAULT NULL,
  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  AUTO_INCREMENT = 1
  DEFAULT CHARSET = utf8
  COLLATE = utf8_general_ci;

-- ----------------------------
-- Table structure for wshop_spec
-- ----------------------------
DROP TABLE IF EXISTS `wshop_spec`;
CREATE TABLE `wshop_spec` (
  `id`           INT(11)      NOT NULL AUTO_INCREMENT,
  `spec_name`    VARCHAR(255) NOT NULL,
  `spec_remark`  VARCHAR(255)          DEFAULT NULL,
  `spec_deleted` TINYINT(4)            DEFAULT '0',
  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  AUTO_INCREMENT = 1
  DEFAULT CHARSET = utf8;

-- ----------------------------
-- Table structure for wshop_spec_det
-- ----------------------------
DROP TABLE IF EXISTS `wshop_spec_det`;
CREATE TABLE `wshop_spec_det` (
  `id`       INT(11)      NOT NULL AUTO_INCREMENT,
  `spec_id`  INT(11)      NOT NULL,
  `det_name` VARCHAR(255) NOT NULL,
  `det_sort` TINYINT(4)   NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  AUTO_INCREMENT = 1
  DEFAULT CHARSET = utf8;

-- ----------------------------
-- Table structure for wshop_suppliers
-- ----------------------------
DROP TABLE IF EXISTS `wshop_suppliers`;
CREATE TABLE `wshop_suppliers` (
  `id`          INT(11) NOT NULL AUTO_INCREMENT,
  `supp_name`   VARCHAR(120)     DEFAULT NULL,
  `supp_phone`  VARCHAR(255)     DEFAULT NULL,
  `supp_stime`  VARCHAR(255)     DEFAULT NULL,
  `supp_sprice` VARCHAR(255)     DEFAULT NULL,
  `supp_sarea`  VARCHAR(255)     DEFAULT NULL,
  `supp_desc`   TEXT,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniname` (`supp_name`) USING BTREE
)
  ENGINE = InnoDB
  AUTO_INCREMENT = 1
  DEFAULT CHARSET = utf8;

-- ----------------------------
-- Table structure for wshop_user_cumulate
-- ----------------------------
DROP TABLE IF EXISTS `wshop_user_cumulate`;
CREATE TABLE `wshop_user_cumulate` (
  `ref_date`      DATE NOT NULL,
  `user_source`   TINYINT(2) DEFAULT '0',
  `cumulate_user` INT(11)    DEFAULT '0',
  PRIMARY KEY (`ref_date`),
  UNIQUE KEY `ref_date` (`ref_date`, `user_source`) USING BTREE
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

-- ----------------------------
-- Table structure for order_refundment
-- ----------------------------
DROP TABLE IF EXISTS `order_refundment`;
CREATE TABLE `order_refundment` (
  `id`            INT(11) NOT NULL   AUTO_INCREMENT,
  `order_id`      INT(11)            DEFAULT NULL,
  `serial_number` VARCHAR(255)
                  CHARACTER SET utf8 DEFAULT NULL,
  `refund_amount` FLOAT(10, 2)       DEFAULT '0.00',
  `refund_time`   DATETIME           DEFAULT NULL,
  `refund_type`   TINYINT(4)         DEFAULT '0',
  `refund_serial` VARCHAR(255)
                  CHARACTER SET utf8 DEFAULT NULL,
  `payment_type`  TINYINT(2)         DEFAULT '0',
  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  AUTO_INCREMENT = 70
  DEFAULT CHARSET = utf8;

-- ----------------------------
-- Table structure for wshop_user_summary
-- ----------------------------
DROP TABLE IF EXISTS `wshop_user_summary`;
CREATE TABLE `wshop_user_summary` (
  `ref_date`    DATE NOT NULL,
  `user_source` TINYINT(2) DEFAULT NULL
  COMMENT '0代表其他（包括带参数二维码） 3代表扫二维码 17代表名片分享 35代表搜号码（即微信添加朋友页的搜索） 39代表查询微信公众帐号 43代表图文页右上角菜单',
  `new_user`    INT(11)    DEFAULT NULL,
  `cancel_user` INT(11)    DEFAULT NULL,
  PRIMARY KEY (`ref_date`),
  UNIQUE KEY `ref_date` (`ref_date`, `user_source`) USING BTREE
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

-- ----------------------------
-- Table structure for express_record
-- ----------------------------
CREATE TABLE `express_record` (
  `id`           INT(11)                 NOT NULL AUTO_INCREMENT,
  `order_id`     INT(11)                 NULL     DEFAULT NULL,
  `confirm_time` DATETIME                NULL     DEFAULT NULL,
  `send_time`    DATETIME                NULL     DEFAULT NULL,
  `costs`        VARCHAR(255)
                 CHARACTER SET utf8
                 COLLATE utf8_general_ci NULL     DEFAULT '0'
  COMMENT '配送时效',
  `openid`       VARCHAR(255)
                 CHARACTER SET utf8
                 COLLATE utf8_general_ci NULL     DEFAULT NULL,
  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  AUTO_INCREMENT = 1
  DEFAULT CHARSET = utf8;

-- ----------------------------
-- Table structure for express_record
-- ----------------------------
CREATE TABLE `wshop_logs` (
  `id`           INT(11) NOT NULL   AUTO_INCREMENT,
  `log_level`    TINYINT(2) DEFAULT 0
  COMMENT '错误级别',
  `log_info`     VARCHAR(255)
                 CHARACTER SET utf8 DEFAULT NULL
  COMMENT '错误信息',
  `log_filename` VARCHAR(255) DEFAULT NULL
  COMMENT '错误文件',
  `log_time`     DATETIME DEFAULT NULL
  COMMENT '日志时间',
  PRIMARY KEY (`id`)
)
  ENGINE = `InnoDB`
  AUTO_INCREMENT = 1
  DEFAULT CHARACTER SET utf8
  COLLATE utf8_general_ci;

-- ----------------------------
-- Table structure for wshop_settings_nav
-- ----------------------------
DROP TABLE IF EXISTS `wshop_settings_nav`;
CREATE TABLE `wshop_settings_nav` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nav_name` varchar(255) NOT NULL COMMENT '菜单名称',
  `nav_ico` varchar(255) NOT NULL COMMENT '显示ICO图片',
  `nav_type` int(11) NOT NULL COMMENT '菜单类型（0.超链接，1.产品分类）',
  `nav_content` text CHARACTER SET utf8,
  `sort` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;


-- 导入默认分组
INSERT INTO `client_level` VALUES ('0', '普通会员', '0', '100', '0', '0');

-- 导入默认系列
INSERT INTO `product_serials` VALUES ('0', '默认', NULL, NULL, NULL, NULL, '0', '0');

-- 默认分组id=0
UPDATE `client_level`
SET id = 0;

-- 默认系列id=0
UPDATE `product_serials`
SET id = 0;

-- 导入管理员账户 账号admin，密码admin
INSERT INTO `admin` VALUES
  (NULL, '超级管理员', 'admin',
   '4a0894d6e8f3b5c6ee0c519bcb98b6b7fd0affcb343ace3a093f29da4b2535604b61f0aebd60c0f0e49cc53adba3fffb',
   'stat,orde,prod,gmes,user,comp,sett', '2015-11-10 12:52:40', '8.8.8.8');

-- 导入默认设置
INSERT INTO `wshop_settings` VALUES ('company_on', '0', '2015-11-22 13:12:18');
INSERT INTO `wshop_settings` VALUES ('copyright', '© 2014-2015 iWshop All rights reserved.', '2015-11-22 13:12:18');
INSERT INTO `wshop_settings` VALUES ('credit_ex', '0.1', '2015-11-22 13:11:49');
INSERT INTO `wshop_settings` VALUES ('credit_order_amount', '100', '2015-11-22 13:11:49');
INSERT INTO `wshop_settings`
VALUES ('expcompany', 'ems,guotong,shentong,shunfeng,tiantian,yousu,yuantong,yunda,zhongtong', '2015-11-15 00:08:36');
INSERT INTO `wshop_settings` VALUES ('exp_weight1', '1000', '2015-07-23 23:24:06');
INSERT INTO `wshop_settings` VALUES ('exp_weight2', '1000', '2015-07-23 23:24:06');
INSERT INTO `wshop_settings` VALUES ('order_cancel_day', '30', '2015-11-22 13:12:18');
INSERT INTO `wshop_settings` VALUES ('order_confirm_day', '30', '2015-11-22 13:12:18');