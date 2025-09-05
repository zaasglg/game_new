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

 Date: 24/07/2025 17:55:49
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for bets
-- ----------------------------
DROP TABLE IF EXISTS `bets`;
CREATE TABLE `bets` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user` varchar(32) CHARACTER SET utf8mb3 COLLATE utf8mb3_german2_ci DEFAULT NULL,
  `sid` varchar(32) CHARACTER SET utf8mb3 COLLATE utf8mb3_german2_ci DEFAULT NULL,
  `bet` decimal(10,2) DEFAULT '0.00',
  `cf` decimal(10,2) DEFAULT '0.00',
  `result` decimal(10,2) DEFAULT '0.00',
  `game` int DEFAULT '0',
  `type` enum('auto','manual') CHARACTER SET utf8mb3 COLLATE utf8mb3_german2_ci DEFAULT 'auto',
  `src` int DEFAULT '1',
  `status` int DEFAULT '2',
  `date` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=113799 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_german2_ci;

SET FOREIGN_KEY_CHECKS = 1;
