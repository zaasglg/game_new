/*
 Navicat Premium Data Transfer

 Source Server         : VALOR_GAMES_LAST
 Source Server Type    : MySQL
 Source Server Version : 80042 (8.0.42-0ubuntu0.20.04.1)
 Source Host           : localhost:3306
 Source Schema         : aviator

 Target Server Type    : MySQL
 Target Server Version : 80042 (8.0.42-0ubuntu0.20.04.1)
 File Encoding         : 65001

 Date: 24/07/2025 17:41:27
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for users
-- ----------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `uid` varchar(8) CHARACTER SET utf8mb3 COLLATE utf8mb3_german2_ci DEFAULT NULL,
  `host_id` int NOT NULL DEFAULT '0',
  `name` text CHARACTER SET utf8mb3 COLLATE utf8mb3_german2_ci,
  `real_name` text CHARACTER SET utf8mb3 COLLATE utf8mb3_german2_ci NOT NULL,
  `img` text CHARACTER SET utf8mb3 COLLATE utf8mb3_german2_ci,
  `balance` decimal(10,2) DEFAULT '500.00',
  `status` int DEFAULT NULL,
  `date` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13340 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_german2_ci;

SET FOREIGN_KEY_CHECKS = 1;
