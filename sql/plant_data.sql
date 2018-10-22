/*
 Navicat MySQL Data Transfer

 Source Server         : plant_root
 Source Server Type    : MySQL
 Source Server Version : 80011
 Source Host           : 192.168.1.114:3306
 Source Schema         : plant_data

 Target Server Type    : MySQL
 Target Server Version : 80011
 File Encoding         : 65001

 Date: 22/10/2018 12:32:03
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for actions
-- ----------------------------
DROP TABLE IF EXISTS `actions`;
CREATE TABLE `actions`  (
  `id` int(11) NOT NULL,
  `timestamp` timestamp(0) NOT NULL DEFAULT CURRENT_TIMESTAMP(0),
  `type` int(11) NULL DEFAULT NULL,
  `action` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
  `info` varchar(1024) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for alerts
-- ----------------------------
DROP TABLE IF EXISTS `alerts`;
CREATE TABLE `alerts`  (
  `id` int(11) NOT NULL,
  `alert_type` int(6) NOT NULL,
  `is_ok` int(4) NOT NULL DEFAULT 0,
  `timestamp` timestamp(0) NOT NULL DEFAULT CURRENT_TIMESTAMP(0),
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for data
-- ----------------------------
DROP TABLE IF EXISTS `data`;
CREATE TABLE `data`  (
  `id` int(11) NOT NULL,
  `air_temp` int(11) NULL DEFAULT NULL,
  `air_hum` int(11) NULL DEFAULT NULL,
  `air_light` int(11) NULL DEFAULT NULL,
  `ground_hum` int(11) NULL DEFAULT NULL,
  `timestamp` timestamp(0) NOT NULL DEFAULT CURRENT_TIMESTAMP(0),
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = latin1 COLLATE = latin1_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for users
-- ----------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users`  (
  `id` int(11) NOT NULL,
  `username` varchar(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `password` varchar(64) CHARACTER SET latin1 COLLATE latin1_general_ci NULL DEFAULT '123456',
  `register_time` timestamp(0) NOT NULL DEFAULT CURRENT_TIMESTAMP(0),
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `username`(`username`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = latin1 COLLATE = latin1_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Procedure structure for addAlert
-- ----------------------------
DROP PROCEDURE IF EXISTS `addAlert`;
delimiter ;;
CREATE PROCEDURE `addAlert`(IN `alert_type_i` int,IN `is_ok_i` int)
BEGIN
	DECLARE id_count INT;
	SELECT count(`alerts`.id) INTO id_count FROM `alerts`;
	IF (id_count > 0) THEN
	    INSERT INTO `alerts` (id, alert_type, is_ok) VALUES (id_count, alert_type_i, is_ok_i);
  ELSE
	    INSERT INTO `alerts` (id, alert_type, is_ok) VALUES (0, alert_type_i, is_ok_i);
  END IF;
END
;;
delimiter ;

-- ----------------------------
-- Procedure structure for addData
-- ----------------------------
DROP PROCEDURE IF EXISTS `addData`;
delimiter ;;
CREATE PROCEDURE `addData`(IN `air_temp_i` int,IN `air_hum_i` int,IN `air_light_i` int,IN `ground_hum_i` int)
BEGIN
	DECLARE id_count INT;
	SELECT count(`data`.id) INTO id_count FROM `data`;
	IF (id_count > 0) THEN
	    INSERT INTO `data` (id, air_temp, air_hum, air_light, ground_hum) VALUES (id_count, air_temp_i, air_hum_i, air_light_i, ground_hum_i);
  ELSE
	    INSERT INTO `data` (id, air_temp, air_hum, air_light, ground_hum) VALUES (0, air_temp_i, air_hum_i, air_light_i, ground_hum_i);
  END IF;
END
;;
delimiter ;

SET FOREIGN_KEY_CHECKS = 1;
