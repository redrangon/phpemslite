/*
 Navicat Premium Dump SQL

 Source Server         : localhost
 Source Server Type    : MySQL
 Source Server Version : 80029 (8.0.29)
 Source Host           : localhost:3306
 Source Schema         : phpemsvue

 Target Server Type    : MySQL
 Target Server Version : 80029 (8.0.29)
 File Encoding         : 65001

 Date: 19/07/2026 09:07:53
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for x2_app
-- ----------------------------
DROP TABLE IF EXISTS `x2_app`;
CREATE TABLE `x2_app`  (
  `appid` int NOT NULL AUTO_INCREMENT,
  `appcode` varchar(24) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `appname` varchar(48) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `appthumb` varchar(240) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `appstatus` int NOT NULL DEFAULT 0,
  `appsetting` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  PRIMARY KEY (`appid`) USING BTREE,
  INDEX `appstatus`(`appstatus`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_520_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Table structure for x2_ask
-- ----------------------------
DROP TABLE IF EXISTS `x2_ask`;
CREATE TABLE `x2_ask`  (
  `askid` int NOT NULL AUTO_INCREMENT,
  `askuserid` int NULL DEFAULT NULL,
  `asktitle` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NULL DEFAULT NULL,
  `asktime` int NULL DEFAULT NULL,
  `askcoin` int NULL DEFAULT NULL,
  `askcontent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NULL,
  `askisshow` int NULL DEFAULT NULL,
  `askstatus` int NULL DEFAULT NULL,
  `askorder` int NULL DEFAULT NULL,
  PRIMARY KEY (`askid`) USING BTREE,
  INDEX `askuserid`(`askuserid` ASC) USING BTREE,
  INDEX `askstatus`(`askstatus` ASC) USING BTREE,
  INDEX `askisshow`(`askisshow` ASC) USING BTREE,
  INDEX `askorder`(`askorder` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_520_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of x2_ask
-- ----------------------------

-- ----------------------------
-- Table structure for x2_ask_answer
-- ----------------------------
DROP TABLE IF EXISTS `x2_ask_answer`;
CREATE TABLE `x2_ask_answer`  (
  `asrid` int NOT NULL AUTO_INCREMENT,
  `asruserid` int NULL DEFAULT NULL,
  `asraskid` int NULL DEFAULT NULL,
  `asrcontent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NULL,
  `asrtime` int NULL DEFAULT NULL,
  `asrstatus` int NULL DEFAULT NULL,
  PRIMARY KEY (`asrid`) USING BTREE,
  INDEX `asruserid`(`asruserid` ASC) USING BTREE,
  INDEX `asraskid`(`asraskid` ASC) USING BTREE,
  INDEX `asrstatus`(`asrstatus` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_520_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of x2_ask_answer
-- ----------------------------

-- ----------------------------
-- Table structure for x2_cert
-- ----------------------------
DROP TABLE IF EXISTS `x2_cert`;
CREATE TABLE `x2_cert`  (
  `ceid` int NOT NULL AUTO_INCREMENT,
  `cetitle` varchar(72) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `ceicon` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NULL DEFAULT NULL,
  `cethumb` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NULL DEFAULT NULL,
  `cedays` int NULL DEFAULT NULL,
  `cetime` int NULL DEFAULT NULL,
  `cenumber` int NULL DEFAULT NULL,
  `cetpl` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NULL DEFAULT NULL,
  `cetags` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NULL,
  `cedescribe` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `cetext` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NULL,
  PRIMARY KEY (`ceid`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_520_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of x2_cert
-- ----------------------------

-- ----------------------------
-- Table structure for x2_cert_member
-- ----------------------------
DROP TABLE IF EXISTS `x2_cert_member`;
CREATE TABLE `x2_cert_member`  (
  `cemid` int NOT NULL AUTO_INCREMENT,
  `cemceid` int NOT NULL,
  `cempassport` varchar(24) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `cemtime` int NOT NULL,
  `cemstatus` tinyint(1) NOT NULL,
  `cemsn` varchar(48) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NULL DEFAULT NULL,
  `cemexpirytime` int NULL DEFAULT NULL,
  PRIMARY KEY (`cemid`) USING BTREE,
  INDEX `cempassport`(`cempassport`) USING BTREE,
  INDEX `cemtime`(`cemtime`) USING BTREE,
  INDEX `cemstatus`(`cemstatus`) USING BTREE,
  INDEX `cemsn`(`cemsn`) USING BTREE,
  INDEX `cemceid`(`cemceid`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_520_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of x2_cert_member
-- ----------------------------

-- ----------------------------
-- Table structure for x2_content
-- ----------------------------
DROP TABLE IF EXISTS `x2_content`;
CREATE TABLE `x2_content`  (
  `contentid` int NOT NULL AUTO_INCREMENT,
  `contentcatid` int NOT NULL DEFAULT 0,
  `contentmoduleid` int NOT NULL DEFAULT 0,
  `contentuserid` int NOT NULL DEFAULT 0,
  `contentusername` varchar(48) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `contentmodifier` varchar(48) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `contenttitle` varchar(240) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `contenttags` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `contentkeywords` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `contentthumb` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `contentlink` varchar(240) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `contentinputtime` int NOT NULL DEFAULT 0,
  `contentmodifytime` int NOT NULL DEFAULT 0,
  `contentsequence` int NOT NULL DEFAULT 0,
  `contentdescribe` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `contentinfo` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `contentstatus` int NOT NULL DEFAULT 0,
  `contenttemplate` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `contenttext` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `contentview` int NULL DEFAULT NULL,
  PRIMARY KEY (`contentid`) USING BTREE,
  INDEX `contentuserid`(`contentuserid`, `contentinputtime`, `contentmodifytime`, `contentsequence`) USING BTREE,
  INDEX `contentmoduleid`(`contentmoduleid`) USING BTREE,
  INDEX `contentcatid`(`contentcatid`) USING BTREE,
  INDEX `contentstatus`(`contentstatus`) USING BTREE,
  INDEX `contenttags`(`contenttags`) USING BTREE,
  FULLTEXT INDEX `contentkeywords`(`contentkeywords`)
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_520_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Table structure for x2_content_category
-- ----------------------------
DROP TABLE IF EXISTS `x2_content_category`;
CREATE TABLE `x2_content_category`  (
  `catid` int NOT NULL AUTO_INCREMENT,
  `catlite` int NOT NULL DEFAULT 0,
  `catname` varchar(240) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `catthumb` varchar(240) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `caturl` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `catuseurl` int NOT NULL DEFAULT 0,
  `catparent` int NULL DEFAULT 0,
  `catdes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  PRIMARY KEY (`catid`) USING BTREE,
  INDEX `catlite`(`catlite`, `catparent`) USING BTREE,
  INDEX `catuseurl`(`catuseurl`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_520_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of x2_content_category
-- ----------------------------

-- ----------------------------
-- Table structure for x2_course
-- ----------------------------
DROP TABLE IF EXISTS `x2_course`;
CREATE TABLE `x2_course`  (
  `courseid` int NOT NULL AUTO_INCREMENT,
  `coursetitle` varchar(240) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NULL DEFAULT NULL,
  `coursemodule` varchar(24) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NULL DEFAULT NULL,
  `coursecsid` int NULL DEFAULT NULL,
  `coursedirid` int NULL DEFAULT NULL,
  `coursethumb` varchar(240) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NULL DEFAULT NULL,
  `courseuserid` int NULL DEFAULT NULL,
  `courseinputtime` int NULL DEFAULT NULL,
  `coursemodifytime` int NULL DEFAULT NULL,
  `coursesequence` int NULL DEFAULT NULL,
  `coursedescribe` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NULL,
  `coursepath` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `coursepasstime` int NOT NULL,
  PRIMARY KEY (`courseid`) USING BTREE,
  INDEX `coursemodule`(`coursemodule`) USING BTREE,
  INDEX `coursecsid`(`coursecsid`) USING BTREE,
  INDEX `coursedirid`(`coursedirid`) USING BTREE,
  INDEX `coursesequence`(`coursesequence`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_520_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Table structure for x2_course_category
-- ----------------------------
DROP TABLE IF EXISTS `x2_course_category`;
CREATE TABLE `x2_course_category`  (
  `catid` int NOT NULL AUTO_INCREMENT,
  `catname` varchar(240) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `catthumb` varchar(240) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `catparent` int NULL DEFAULT 0,
  `catlite` int NOT NULL DEFAULT 0,
  `caturl` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `catuseurl` int NOT NULL DEFAULT 0,
  `catdes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  PRIMARY KEY (`catid`) USING BTREE,
  INDEX `catlite`(`catlite`, `catparent`) USING BTREE,
  INDEX `catuseurl`(`catuseurl`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_520_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of x2_course_category
-- ----------------------------

-- ----------------------------
-- Table structure for x2_course_log
-- ----------------------------
DROP TABLE IF EXISTS `x2_course_log`;
CREATE TABLE `x2_course_log`  (
  `logid` int NOT NULL AUTO_INCREMENT,
  `logpassport` varchar(24) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NULL DEFAULT '0',
  `logplanid` int NOT NULL,
  `logcourseid` int NULL DEFAULT 0,
  `logtime` int NULL DEFAULT 0,
  `logstatus` int NULL DEFAULT NULL,
  `logendtime` int NULL DEFAULT NULL,
  `logprogress` int NULL DEFAULT NULL,
  `logfaces` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NULL,
  PRIMARY KEY (`logid`) USING BTREE,
  INDEX `loguserid`(`logpassport`, `logcourseid`) USING BTREE,
  INDEX `logtime`(`logtime`) USING BTREE,
  INDEX `logplanid`(`logplanid`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_520_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of x2_course_log
-- ----------------------------

-- ----------------------------
-- Table structure for x2_course_member
-- ----------------------------
DROP TABLE IF EXISTS `x2_course_member`;
CREATE TABLE `x2_course_member`  (
  `cmid` int NOT NULL AUTO_INCREMENT,
  `cmpassport` varchar(24) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NULL DEFAULT NULL,
  `cmcsid` int NULL DEFAULT NULL,
  `cmstarttime` int NULL DEFAULT NULL,
  `cmendtime` int NULL DEFAULT NULL,
  PRIMARY KEY (`cmid`) USING BTREE,
  INDEX `cmpassport`(`cmpassport` ASC) USING BTREE,
  INDEX `cmcsid`(`cmcsid` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_520_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of x2_course_member
-- ----------------------------

-- ----------------------------
-- Table structure for x2_course_price
-- ----------------------------
DROP TABLE IF EXISTS `x2_course_price`;
CREATE TABLE `x2_course_price`  (
  `cpid` int NOT NULL AUTO_INCREMENT,
  `cpcsid` int NULL DEFAULT NULL,
  `cpdays` int NULL DEFAULT NULL,
  `cpamount` int NULL DEFAULT NULL,
  PRIMARY KEY (`cpid`) USING BTREE,
  INDEX `cpcsid`(`cpcsid` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_520_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of x2_course_price
-- ----------------------------

-- ----------------------------
-- Table structure for x2_course_progress
-- ----------------------------
DROP TABLE IF EXISTS `x2_course_progress`;
CREATE TABLE `x2_course_progress`  (
  `cpid` int NOT NULL AUTO_INCREMENT,
  `cppassport` varchar(24) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NULL DEFAULT NULL,
  `cpcsid` int NULL DEFAULT NULL,
  `cpstatus` int NULL DEFAULT NULL,
  PRIMARY KEY (`cpid`) USING BTREE,
  INDEX `cppassport`(`cppassport` ASC) USING BTREE,
  INDEX `cpcsid`(`cpcsid` ASC) USING BTREE,
  INDEX `cpstatus`(`cpstatus` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_520_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of x2_course_progress
-- ----------------------------

-- ----------------------------
-- Table structure for x2_course_session
-- ----------------------------
DROP TABLE IF EXISTS `x2_course_session`;
CREATE TABLE `x2_course_session`  (
  `csnid` int NOT NULL AUTO_INCREMENT,
  `csnpassport` varchar(24) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NULL DEFAULT NULL,
  `csncsid` int NULL DEFAULT NULL,
  `csnfadtime` int NULL DEFAULT NULL,
  PRIMARY KEY (`csnid`) USING BTREE,
  UNIQUE INDEX `csnpassport`(`csnpassport` ASC) USING BTREE,
  INDEX `csncsid`(`csncsid` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_520_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of x2_course_session
-- ----------------------------

-- ----------------------------
-- Table structure for x2_course_subject
-- ----------------------------
DROP TABLE IF EXISTS `x2_course_subject`;
CREATE TABLE `x2_course_subject`  (
  `csid` int NOT NULL AUTO_INCREMENT,
  `cstitle` varchar(240) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NULL DEFAULT '',
  `cscatid` int NULL DEFAULT NULL,
  `csuserid` int NULL DEFAULT 0,
  `cstime` int NULL DEFAULT 0,
  `csthumb` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NULL DEFAULT '',
  `cssequence` int NULL DEFAULT NULL,
  `csnumber` tinyint(1) NULL DEFAULT NULL,
  `csprogress` tinyint(1) NULL DEFAULT NULL,
  `csfacetime` int NULL DEFAULT NULL,
  `csdescribe` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NULL,
  `cstext` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NULL,
  PRIMARY KEY (`csid`) USING BTREE,
  INDEX `csbasicid`(`cstime`) USING BTREE,
  INDEX `cscatid`(`cscatid`) USING BTREE,
  INDEX `cuserid`(`csuserid`) USING BTREE,
  INDEX `cssequence`(`cssequence`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_520_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of x2_course_subject
-- ----------------------------

-- ----------------------------
-- Table structure for x2_exam_basic
-- ----------------------------
DROP TABLE IF EXISTS `x2_exam_basic`;
CREATE TABLE `x2_exam_basic`  (
  `basicid` int NOT NULL AUTO_INCREMENT,
  `basic` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `basicsubjectid` int NOT NULL DEFAULT 0,
  `basicnumber` int NULL DEFAULT NULL,
  `basicfacetime` int NULL DEFAULT NULL,
  `basicpoint` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `basicexam` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `basicthumb` varchar(240) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `basicdescribe` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `basictext` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NULL,
  PRIMARY KEY (`basicid`) USING BTREE,
  INDEX `basicsubjectid`(`basicsubjectid`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_520_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of x2_exam_basic
-- ----------------------------

-- ----------------------------
-- Table structure for x2_exam_exercise
-- ----------------------------
DROP TABLE IF EXISTS `x2_exam_exercise`;
CREATE TABLE `x2_exam_exercise`  (
  `exerid` int NOT NULL AUTO_INCREMENT,
  `exeruserid` int NOT NULL,
  `exerplanid` int NULL DEFAULT NULL,
  `exerbasicid` int NOT NULL,
  `exerpointid` int NOT NULL,
  `exernumber` int NOT NULL,
  `exerqutype` int NOT NULL,
  PRIMARY KEY (`exerid`) USING BTREE,
  INDEX `exeruserid`(`exeruserid`) USING BTREE,
  INDEX `exerbasicid`(`exerbasicid`) USING BTREE,
  INDEX `exerknowsid`(`exerpointid`) USING BTREE,
  INDEX `exerplanid`(`exerplanid`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_520_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of x2_exam_exercise
-- ----------------------------

-- ----------------------------
-- Table structure for x2_exam_favor
-- ----------------------------
DROP TABLE IF EXISTS `x2_exam_favor`;
CREATE TABLE `x2_exam_favor`  (
  `favorid` int NOT NULL AUTO_INCREMENT,
  `favorpassport` varchar(24) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '0',
  `favorsubjectid` int NOT NULL DEFAULT 0,
  `favorquestionid` int NOT NULL DEFAULT 0,
  `favortime` int NOT NULL DEFAULT 0,
  PRIMARY KEY (`favorid`) USING BTREE,
  INDEX `favoruserid`(`favorpassport`, `favorquestionid`, `favortime`) USING BTREE,
  INDEX `favorsubjectid`(`favorsubjectid`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_520_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of x2_exam_favor
-- ----------------------------

-- ----------------------------
-- Table structure for x2_exam_feedback
-- ----------------------------
DROP TABLE IF EXISTS `x2_exam_feedback`;
CREATE TABLE `x2_exam_feedback`  (
  `fbid` int NOT NULL AUTO_INCREMENT,
  `fbquestionid` int NOT NULL,
  `fbtype` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `fbcontent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `fbuserid` int NOT NULL,
  `fbtime` int NOT NULL,
  `fbstatus` tinyint NOT NULL,
  `fbdoneuserid` int NOT NULL,
  `fbdonetime` int NOT NULL,
  PRIMARY KEY (`fbid`) USING BTREE,
  INDEX `fbquestionid`(`fbquestionid`, `fbuserid`) USING BTREE,
  INDEX `fbtype`(`fbtype`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_520_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of x2_exam_feedback
-- ----------------------------

-- ----------------------------
-- Table structure for x2_exam_history
-- ----------------------------
DROP TABLE IF EXISTS `x2_exam_history`;
CREATE TABLE `x2_exam_history`  (
  `ehid` int NOT NULL AUTO_INCREMENT,
  `ehplanid` int NOT NULL,
  `ehpaperid` int NOT NULL DEFAULT 0,
  `ehexam` varchar(240) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `ehtype` int NOT NULL DEFAULT 0,
  `ehbasicid` int NOT NULL DEFAULT 0,
  `ehtime` int NOT NULL DEFAULT 0,
  `ehscore` decimal(10, 2) NOT NULL DEFAULT 0.00,
  `ehpassport` varchar(24) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `ehstarttime` int NOT NULL DEFAULT 0,
  `ehendtime` int NOT NULL,
  `ehstatus` int NOT NULL DEFAULT 1,
  `ehdecide` int NOT NULL DEFAULT 0,
  `ehneedresit` tinyint NOT NULL,
  `ehispass` tinyint NULL DEFAULT NULL,
  `ehteacher` varchar(72) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NULL DEFAULT NULL,
  `ehdecidetime` int NULL DEFAULT NULL,
  `ehstats` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NULL,
  `ehstartip` varchar(72) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NULL DEFAULT NULL,
  `ehendip` varchar(72) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NULL DEFAULT NULL,
  `ehstartclient` varchar(72) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NULL DEFAULT NULL,
  `ehendclient` varchar(72) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NULL DEFAULT NULL,
  PRIMARY KEY (`ehid`) USING BTREE,
  INDEX `ehtype`(`ehtype` ASC, `ehbasicid` ASC, `ehtime` ASC) USING BTREE,
  INDEX `ehdecide`(`ehdecide` ASC) USING BTREE,
  INDEX `ehexamid`(`ehpaperid` ASC) USING BTREE,
  INDEX `ehneedresit`(`ehneedresit` ASC) USING BTREE,
  INDEX `ehispass`(`ehispass` ASC) USING BTREE,
  INDEX `ehplanid`(`ehplanid` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_520_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of x2_exam_history
-- ----------------------------

-- ----------------------------
-- Table structure for x2_exam_history_detail
-- ----------------------------
DROP TABLE IF EXISTS `x2_exam_history_detail`;
CREATE TABLE `x2_exam_history_detail`  (
  `ehdid` int NOT NULL AUTO_INCREMENT,
  `ehdehid` int NULL DEFAULT NULL,
  `ehdscores` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL,
  `ehdsetting` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL,
  `ehdquestion` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL,
  `ehdanswer` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL,
  `ehdfacelist` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL,
  `ehdtimes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL,
  PRIMARY KEY (`ehdid`) USING BTREE,
  UNIQUE INDEX `ehdehid`(`ehdehid` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of x2_exam_history_detail
-- ----------------------------

-- ----------------------------
-- Table structure for x2_exam_history_log
-- ----------------------------
DROP TABLE IF EXISTS `x2_exam_history_log`;
CREATE TABLE `x2_exam_history_log`  (
  `ehlid` int NOT NULL AUTO_INCREMENT,
  `ehlehid` int NULL DEFAULT NULL,
  `ehlusername` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NULL DEFAULT NULL,
  `ehltype` int NULL DEFAULT NULL,
  `ehlinfo` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NULL,
  `ehltime` int NULL DEFAULT NULL,
  PRIMARY KEY (`ehlid`) USING BTREE,
  INDEX `ehlusername`(`ehlusername` ASC) USING BTREE,
  INDEX `ehltype`(`ehltype` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_520_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of x2_exam_history_log
-- ----------------------------

-- ----------------------------
-- Table structure for x2_exam_member
-- ----------------------------
DROP TABLE IF EXISTS `x2_exam_member`;
CREATE TABLE `x2_exam_member`  (
  `emid` int NOT NULL AUTO_INCREMENT,
  `empassport` varchar(24) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NULL DEFAULT NULL,
  `embasicid` int NULL DEFAULT NULL,
  `emstarttime` int NULL DEFAULT NULL,
  `emendtime` int NULL DEFAULT NULL,
  PRIMARY KEY (`emid`) USING BTREE,
  INDEX `empassport`(`empassport` ASC) USING BTREE,
  INDEX `embasicid`(`embasicid` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_520_ci ROW_FORMAT = DYNAMIC;


-- ----------------------------
-- Table structure for x2_exam_paper
-- ----------------------------
DROP TABLE IF EXISTS `x2_exam_paper`;
CREATE TABLE `x2_exam_paper`  (
  `examid` int NOT NULL AUTO_INCREMENT,
  `examsubject` int NOT NULL DEFAULT 0,
  `exam` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `examtotalscore` int NULL DEFAULT NULL,
  `examtotaltime` int NULL DEFAULT NULL,
  `exampassmark` decimal(10, 2) NULL DEFAULT NULL,
  `examscalemodel` tinyint NULL DEFAULT NULL,
  `examsetting` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `examquestions` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `examscore` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `examstatus` int NOT NULL DEFAULT 0,
  `examtype` int NOT NULL DEFAULT 0,
  `examauthorid` int NOT NULL DEFAULT 0,
  `examauthor` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `examtime` int NOT NULL DEFAULT 0,
  `examkeyword` varchar(240) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `examdecide` int NOT NULL DEFAULT 0,
  PRIMARY KEY (`examid`) USING BTREE,
  INDEX `examstatus`(`examstatus`) USING BTREE,
  INDEX `examtype`(`examtype`, `examauthorid`) USING BTREE,
  INDEX `examtime`(`examtime`) USING BTREE,
  INDEX `examsubject`(`examsubject`) USING BTREE,
  INDEX `examdecide`(`examdecide`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_520_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of x2_exam_paper
-- ----------------------------

-- ----------------------------
-- Table structure for x2_exam_paper_session
-- ----------------------------
DROP TABLE IF EXISTS `x2_exam_paper_session`;
CREATE TABLE `x2_exam_paper_session`  (
  `esid` int NOT NULL AUTO_INCREMENT,
  `examsessionid` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `examsessionplanid` int NOT NULL,
  `examsessionpassport` varchar(48) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '0',
  `examsession` varchar(240) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `examsessionsetting` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `examsessionsign` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `examsessionbasicid` int NOT NULL DEFAULT 0,
  `examsessiontype` int NOT NULL,
  `examsessionpaperid` int(11) UNSIGNED ZEROFILL NOT NULL,
  `examsessionquestion` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `examsessionuseranswer` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `examsessionstarttime` int NOT NULL DEFAULT 0,
  `examsessiontime` int NOT NULL DEFAULT 0,
  `examsessiontimelist` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `examsessiontoken` varchar(48) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NULL DEFAULT NULL,
  `examsessionfacelist` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NULL,
  `examsessionip` varchar(72) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NULL DEFAULT NULL,
  `examsessionclient` varchar(72) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NULL DEFAULT NULL,
  PRIMARY KEY (`esid`) USING BTREE,
  UNIQUE INDEX `examsessionhandle`(`examsessionplanid` ASC, `examsessionpassport` ASC, `examsessionbasicid` ASC, `examsessiontype` ASC) USING BTREE,
  UNIQUE INDEX `examsessionid`(`examsessionid` ASC) USING BTREE,
  INDEX `examsessionstarttime`(`examsessionstarttime` ASC) USING BTREE,
  INDEX `examsessiontype`(`examsessiontype` ASC) USING BTREE,
  INDEX `examsessionkey`(`examsessionpaperid` ASC) USING BTREE,
  INDEX `examsessionbasic`(`examsessionbasicid` ASC) USING BTREE,
  INDEX `examsessionpassport`(`examsessionpassport` ASC) USING BTREE,
  INDEX `examsessiontoken`(`examsessiontoken` ASC) USING BTREE,
  INDEX `examsessionplanid`(`examsessionplanid` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_520_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of x2_exam_paper_session
-- ----------------------------

-- ----------------------------
-- Table structure for x2_exam_point
-- ----------------------------
DROP TABLE IF EXISTS `x2_exam_point`;
CREATE TABLE `x2_exam_point`  (
  `pointid` int NOT NULL AUTO_INCREMENT,
  `point` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `pointsectionid` int NOT NULL DEFAULT 0,
  `pointdescribe` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `pointstatus` int NOT NULL DEFAULT 1,
  `pointsequence` int NOT NULL,
  `pointnumber` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `pointquestions` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  PRIMARY KEY (`pointid`) USING BTREE,
  INDEX `pointstatus`(`pointstatus`) USING BTREE,
  INDEX `pointsequence`(`pointsequence`) USING BTREE,
  INDEX `pointsectionid`(`pointsectionid`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_520_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of x2_exam_point
-- ----------------------------

-- ----------------------------
-- Table structure for x2_exam_price
-- ----------------------------
DROP TABLE IF EXISTS `x2_exam_price`;
CREATE TABLE `x2_exam_price`  (
  `epid` int NOT NULL AUTO_INCREMENT,
  `epbasicid` int NULL DEFAULT NULL,
  `epdays` int NULL DEFAULT NULL,
  `epamount` int NULL DEFAULT NULL,
  PRIMARY KEY (`epid`) USING BTREE,
  INDEX `epbasicid`(`epbasicid` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_520_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of x2_exam_price
-- ----------------------------

-- ----------------------------
-- Table structure for x2_exam_question
-- ----------------------------
DROP TABLE IF EXISTS `x2_exam_question`;
CREATE TABLE `x2_exam_question`  (
  `questionid` int NOT NULL AUTO_INCREMENT,
  `questiontype` int NOT NULL DEFAULT 0,
  `question` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `questionuserid` int NOT NULL DEFAULT 0,
  `questionusername` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `questionlastmodifyuser` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `questionselect` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `questionselectnumber` tinyint NOT NULL DEFAULT 0,
  `questionselecttype` tinyint NULL DEFAULT 0,
  `questionanswer` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `questiondescribe` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `questioncreatetime` int NOT NULL DEFAULT 0,
  `questionhtml` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `questionparent` int NOT NULL DEFAULT 0,
  `questionisparent` int NULL DEFAULT NULL,
  `questionchildnumber` int NULL DEFAULT NULL,
  `questionchildren` varchar(240) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NULL DEFAULT NULL,
  `questionsequence` int NOT NULL DEFAULT 0,
  `questionlevel` int NOT NULL DEFAULT 0,
  PRIMARY KEY (`questionid`) USING BTREE,
  INDEX `questioncreatetime`(`questioncreatetime`) USING BTREE,
  INDEX `questiontype`(`questiontype`) USING BTREE,
  INDEX `questionuserid`(`questionuserid`) USING BTREE,
  INDEX `questionparent`(`questionparent`) USING BTREE,
  INDEX `questionlevel`(`questionlevel`) USING BTREE,
  INDEX `questionchildnumber`(`questionchildnumber`) USING BTREE,
  INDEX `questionisparent`(`questionisparent`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_520_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of x2_exam_question
-- ----------------------------

-- ----------------------------
-- Table structure for x2_exam_question_relation
-- ----------------------------
DROP TABLE IF EXISTS `x2_exam_question_relation`;
CREATE TABLE `x2_exam_question_relation`  (
  `qkid` int NOT NULL AUTO_INCREMENT,
  `qkquestionid` int NOT NULL DEFAULT 0,
  `qkpointid` int NOT NULL DEFAULT 0,
  `qkstatus` int NULL DEFAULT 1,
  PRIMARY KEY (`qkid`) USING BTREE,
  INDEX `qkpointid`(`qkpointid`) USING BTREE,
  INDEX `qkquestionid`(`qkquestionid`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_520_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of x2_exam_question_relation
-- ----------------------------

-- ----------------------------
-- Table structure for x2_exam_question_type
-- ----------------------------
DROP TABLE IF EXISTS `x2_exam_question_type`;
CREATE TABLE `x2_exam_question_type`  (
  `questid` int NOT NULL AUTO_INCREMENT,
  `questype` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `questsort` int NOT NULL DEFAULT 0,
  `questchoice` int NOT NULL DEFAULT 0,
  PRIMARY KEY (`questid`) USING BTREE,
  INDEX `questchoice`(`questchoice`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 8 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_520_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of x2_exam_question_type
-- ----------------------------
INSERT INTO `x2_exam_question_type` VALUES (1, '单选题', 0, 1);
INSERT INTO `x2_exam_question_type` VALUES (2, '多选题', 0, 2);
INSERT INTO `x2_exam_question_type` VALUES (3, '不定项题', 0, 3);
INSERT INTO `x2_exam_question_type` VALUES (4, '判断题', 0, 4);
INSERT INTO `x2_exam_question_type` VALUES (5, '定值填空', 0, 5);
INSERT INTO `x2_exam_question_type` VALUES (6, '填空题', 1, 101);
INSERT INTO `x2_exam_question_type` VALUES (7, '问答题', 1, 102);

-- ----------------------------
-- Table structure for x2_exam_section
-- ----------------------------
DROP TABLE IF EXISTS `x2_exam_section`;
CREATE TABLE `x2_exam_section`  (
  `sectionid` int NOT NULL AUTO_INCREMENT,
  `section` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `sectionsubjectid` int NOT NULL DEFAULT 0,
  `sectiondescribe` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `sectionsequence` int NOT NULL,
  PRIMARY KEY (`sectionid`) USING BTREE,
  INDEX `section`(`section`) USING BTREE,
  INDEX `sectionsubjectid`(`sectionsubjectid`) USING BTREE,
  INDEX `sectionsequence`(`sectionsequence`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_520_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of x2_exam_section
-- ----------------------------

-- ----------------------------
-- Table structure for x2_exam_session
-- ----------------------------
DROP TABLE IF EXISTS `x2_exam_session`;
CREATE TABLE `x2_exam_session`  (
  `esid` int NOT NULL AUTO_INCREMENT,
  `espassport` varchar(24) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NULL DEFAULT NULL,
  `esbasicid` int NULL DEFAULT NULL,
  `esfadtime` int NULL DEFAULT NULL,
  PRIMARY KEY (`esid`) USING BTREE,
  UNIQUE INDEX `espassport`(`espassport` ASC) USING BTREE,
  INDEX `esbasicid`(`esbasicid` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_520_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of x2_exam_session
-- ----------------------------

-- ----------------------------
-- Table structure for x2_exam_subject
-- ----------------------------
DROP TABLE IF EXISTS `x2_exam_subject`;
CREATE TABLE `x2_exam_subject`  (
  `subjectid` int NOT NULL AUTO_INCREMENT,
  `subject` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `subjectsetting` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  PRIMARY KEY (`subjectid`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_520_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of x2_exam_subject
-- ----------------------------

-- ----------------------------
-- Table structure for x2_member
-- ----------------------------
DROP TABLE IF EXISTS `x2_member`;
CREATE TABLE `x2_member`  (
  `mid` int NOT NULL AUTO_INCREMENT,
  `mname` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NULL DEFAULT NULL COMMENT '姓名',
  `mphoto` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NULL DEFAULT NULL COMMENT '照片',
  `mphone` varchar(24) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NULL DEFAULT NULL COMMENT '手机号',
  `maddress` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NULL DEFAULT NULL COMMENT '地址',
  `mpassport` varchar(24) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NULL DEFAULT NULL COMMENT '身份证号',
  `mpassportimg` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NULL DEFAULT NULL COMMENT '身份证照片A',
  `msex` varchar(6) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NULL DEFAULT NULL COMMENT '性别',
  `mbirthday` varchar(12) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NULL DEFAULT NULL COMMENT '生日',
  `mpolitic` varchar(48) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NULL DEFAULT NULL COMMENT '政治面貌',
  `medu` varchar(48) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NULL DEFAULT NULL COMMENT '学历',
  `munit` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NULL DEFAULT NULL COMMENT '单位全称',
  `mcompany` varchar(240) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NULL DEFAULT NULL COMMENT '公司名称',
  `mjobtime` varchar(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NULL DEFAULT NULL COMMENT '参加工作时间',
  `mjob` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NULL DEFAULT NULL COMMENT '职务',
  `mjobtitle` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NULL DEFAULT NULL COMMENT '职称或技能名称',
  `mteam` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NULL DEFAULT NULL COMMENT '小队名称',
  `mtext` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NULL COMMENT '其他有关情况陈述',
  `mresume` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NULL COMMENT '工作经历',
  `mtime` int NULL DEFAULT NULL COMMENT '添加时间',
  PRIMARY KEY (`mid`) USING BTREE,
  UNIQUE INDEX `mpassport`(`mpassport`) USING BTREE,
  INDEX `mname`(`mname`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_520_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Table structure for x2_session
-- ----------------------------
DROP TABLE IF EXISTS `x2_session`;
CREATE TABLE `x2_session`  (
  `serialid` int NOT NULL AUTO_INCREMENT,
  `sessionid` varchar(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NULL DEFAULT NULL,
  `sessionuserid` int NOT NULL DEFAULT 0,
  `sessionauthtoken` varchar(72) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `sessionsalt` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NULL DEFAULT NULL,
  `sessionip` varchar(24) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `sessioncurrent` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NULL DEFAULT '',
  `sessionlogintime` int NOT NULL DEFAULT 0,
  `sessionlasttime` int NOT NULL DEFAULT 0,
  PRIMARY KEY (`serialid`) USING BTREE,
  UNIQUE INDEX `sessionuserid`(`sessionuserid`) USING BTREE,
  UNIQUE INDEX `sessionid`(`sessionid`) USING BTREE,
  INDEX `sessionauthtoken`(`sessionauthtoken`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_520_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Table structure for x2_trade_order
-- ----------------------------
DROP TABLE IF EXISTS `x2_trade_order`;
CREATE TABLE `x2_trade_order`  (
  `orderid` int NOT NULL AUTO_INCREMENT,
  `ordersn` varchar(18) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `ordertitle` varchar(240) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `orderpassport` varchar(24) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `orderitems` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `orderprice` decimal(10, 2) NOT NULL,
  `orderstatus` int NOT NULL,
  `orderrawprice` decimal(10, 2) NOT NULL,
  `ordercreatetime` int NOT NULL,
  `orderpaytime` int NOT NULL,
  `orderpaytype` varchar(24) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NULL DEFAULT NULL,
  `orderfinishtime` int NOT NULL,
  `orderfaq` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `orderdescribe` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  PRIMARY KEY (`orderid`) USING BTREE,
  UNIQUE INDEX `ordersn`(`ordersn`) USING BTREE,
  INDEX `orderprice`(`orderprice`) USING BTREE,
  INDEX `orderpaytype`(`orderpaytype`) USING BTREE,
  INDEX `ordercreatetime`(`ordercreatetime`) USING BTREE,
  INDEX `orderpaytime`(`orderpaytime`) USING BTREE,
  INDEX `orderpassport`(`orderpassport`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_520_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Table structure for x2_user
-- ----------------------------
DROP TABLE IF EXISTS `x2_user`;
CREATE TABLE `x2_user`  (
  `userid` int NOT NULL AUTO_INCREMENT,
  `useropenid` varchar(48) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `userunionid` varchar(48) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NULL DEFAULT NULL,
  `username` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `userpassport` varchar(24) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NULL DEFAULT NULL,
  `usersalt` varchar(6) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NULL DEFAULT NULL,
  `useremail` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `userpassword` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `userphoto` varchar(240) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `usercoin` int NOT NULL DEFAULT 0,
  `userregip` varchar(24) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `userregtime` int NOT NULL DEFAULT 0,
  `userlogtime` int NOT NULL DEFAULT 0,
  `userverifytime` int NULL DEFAULT NULL,
  `usergroupid` int NOT NULL DEFAULT 0,
  `manager_apps` varchar(240) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `usertruename` varchar(24) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `teacher_subjects` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `userprofile` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `usergender` varchar(6) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `userphone` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `useraddress` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `userstatus` int NULL DEFAULT NULL,
  PRIMARY KEY (`userid`) USING BTREE,
  UNIQUE INDEX `useremail`(`useremail`) USING BTREE,
  INDEX `username`(`username`) USING BTREE,
  INDEX `userregtime`(`userregtime`) USING BTREE,
  INDEX `useropenid`(`useropenid`) USING BTREE,
  INDEX `userphone`(`userphone`) USING BTREE,
  INDEX `userunionid`(`userunionid`) USING BTREE,
  INDEX `userpassport`(`userpassport`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_520_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of x2_user
-- ----------------------------
INSERT INTO `x2_user` VALUES (1, '', NULL, 'peadmin', '410782198502140077', NULL, '958074@163.com', '$2y$10$tLoHXbCHFAdbFGojkPeghuWQLz9ZvDhBATMS3WA3vT3/A2qaydApW', 'storage/attach/public/20260630/ce9ce3121a4a8fcbead542e4704b65da.jpg', 259, '127.0.0.1', 1471795200, 0, 1782141035, 1, 'a:7:{i:0;s:4:\"user\";i:1;s:7:\"content\";i:2;s:4:\"exam\";i:3;s:8:\"document\";i:4;s:6:\"course\";i:5;s:4:\"bank\";i:6;s:8:\"autoform\";}', '火眼', '', '', '男', '', '信息部', 3);
INSERT INTO `x2_user` VALUES (2, '', '', 'redrangon', '00000d69ad4e72d9e9801269', NULL, 'redrangon@163.com', '$2y$10$d1ha4mcOmPIgfePVXIfXFeXoj4xRtKuMtkXc2GapU2E6eU0cbN4j2', 'storage/attach/public/20260618/f17ce7f87ee05c1fe25f4cfd08300c5f.jpg', 0, '127.0.0.1', 1781780670, 0, 1782141041, 8, '', '火眼', '', '', '男', '', '', 2);

-- ----------------------------
-- Table structure for x2_user_expense
-- ----------------------------
DROP TABLE IF EXISTS `x2_user_expense`;
CREATE TABLE `x2_user_expense`  (
  `ueid` int NOT NULL AUTO_INCREMENT,
  `uepassport` varchar(24) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NULL DEFAULT NULL,
  `ueuserid` int NULL DEFAULT NULL,
  `ueamount` int NULL DEFAULT NULL,
  `uetime` int NULL DEFAULT NULL,
  `uedescribe` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NULL DEFAULT NULL,
  PRIMARY KEY (`ueid`) USING BTREE,
  INDEX `uepassport`(`uepassport` ASC) USING BTREE,
  INDEX `ueuserid`(`ueuserid` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_520_ci ROW_FORMAT = Dynamic;


-- ----------------------------
-- Table structure for x2_user_group
-- ----------------------------
DROP TABLE IF EXISTS `x2_user_group`;
CREATE TABLE `x2_user_group`  (
  `groupid` int NOT NULL AUTO_INCREMENT,
  `groupname` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `groupmoduleid` tinyint NOT NULL DEFAULT 0,
  `groupdescribe` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `groupright` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `groupmoduledefault` int NOT NULL DEFAULT 0,
  `groupdefault` int NOT NULL DEFAULT 0,
  PRIMARY KEY (`groupid`) USING BTREE,
  INDEX `groupname`(`groupname`, `groupmoduleid`) USING BTREE,
  INDEX `groupmoduledefault`(`groupmoduledefault`) USING BTREE,
  INDEX `groupdefault`(`groupdefault`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 10 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_520_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of x2_user_group
-- ----------------------------
INSERT INTO `x2_user_group` VALUES (1, '管理员', 1, '管理员', '', 1, 0);
INSERT INTO `x2_user_group` VALUES (8, '普通用户', 9, '普通用户', '', 0, 1);
INSERT INTO `x2_user_group` VALUES (9, '教师', 12, '教师', '', 0, 0);

-- ----------------------------
-- Table structure for x2_user_log
-- ----------------------------
DROP TABLE IF EXISTS `x2_user_log`;
CREATE TABLE `x2_user_log`  (
  `ulid` int NOT NULL AUTO_INCREMENT,
  `uluserid` int NULL DEFAULT NULL,
  `ulip` varchar(24) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NULL DEFAULT NULL,
  `ulclient` varchar(12) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NULL DEFAULT NULL,
  `ultime` int NULL DEFAULT NULL,
  PRIMARY KEY (`ulid`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_520_ci ROW_FORMAT = DYNAMIC;


-- ----------------------------
-- Table structure for x2_user_money
-- ----------------------------
DROP TABLE IF EXISTS `x2_user_money`;
CREATE TABLE `x2_user_money`  (
  `umid` int NOT NULL AUTO_INCREMENT,
  `umpassport` varchar(24) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NULL DEFAULT NULL,
  `umamount` int UNSIGNED NULL DEFAULT NULL,
  PRIMARY KEY (`umid` DESC) USING BTREE,
  UNIQUE INDEX `umpassport`(`umpassport` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_520_ci ROW_FORMAT = Dynamic;


-- ----------------------------
-- Table structure for x2_user_rand_code
-- ----------------------------
DROP TABLE IF EXISTS `x2_user_rand_code`;
CREATE TABLE `x2_user_rand_code`  (
  `urcid` int NOT NULL AUTO_INCREMENT,
  `urctarget` varchar(72) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `urctype` varchar(24) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `urcsendtime` int NULL DEFAULT NULL,
  `urcstring` varchar(24) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  PRIMARY KEY (`urcid`) USING BTREE,
  INDEX `urctarget`(`urctarget` ASC) USING BTREE,
  INDEX `urctype`(`urctype` ASC) USING BTREE,
  INDEX `urcsendtime`(`urcsendtime` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of x2_user_rand_code
-- ----------------------------

-- ----------------------------
-- Table structure for x2_user_wxlogin
-- ----------------------------
DROP TABLE IF EXISTS `x2_user_wxlogin`;
CREATE TABLE `x2_user_wxlogin`  (
  `wxsid` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `wxinfo` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `wxtime` int NOT NULL,
  `wxtoken` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  PRIMARY KEY (`wxsid`) USING BTREE,
  INDEX `wxtime`(`wxtime`) USING BTREE,
  INDEX `wxtoken`(`wxtoken`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_520_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of x2_user_wxlogin
-- ----------------------------

SET FOREIGN_KEY_CHECKS = 1;
