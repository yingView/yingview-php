
create database if not exists yingview charset utf8;

show create database yingview;

drop database yingview;

use yingview;

update tablename set column=123,column2=234 where 条件;

/* 用户表 uid username password passcode sax email tel birthday photo level power status job activecode*/

CREATE TABLE `users` ( /*用户*/
	`userId` int PRIMARY KEY AUTO_INCREMENT,
	`userCode` varchar(64) NOT NULL,
	`userName` varchar(64) NOT NULL,
	`password` varchar(64) NOT NULL,
	`passCode` varchar(64) NOT NULL,
	`nickName` varchar(20) NOT NULL,
	`sax` tinyint,  /* 0 或者 1*/
	`email` varchar(32) NOT NULL,
	`tel` char(11) DEFAULT NULL,
	`bithday` int DEFAULT NULL,
	`userPhoto` varchar(72) DEFAULT NULL, /* md5(fileid).jpeg*/
	`userBanner` varchar(72) DEFAULT NULL, /* md5(fileid).jpeg*/
	`userLevel` tinyint DEFAULT NULL,
	`userPower` tinyint DEFAULT NULL,
	`userStatus` tinyint DEFAULT NULL,
	`userJob` varchar(16) DEFAULT NULL,
	`sign` varchar(64) DEFAULT NULL, /* 描述职业的一句话*/
	`description` varchar(100) DEFAULT NULL, /* 关于我 */
	`experience` varchar(1000) DEFAULT NULL, /* 个人履历 */
	`city` varchar(30) DEFAULT NULL,
	`activeCode` varchar(64) DEFAULT NULL,
	`userCreateTime` int  DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `users` ADD unique(`userName`, `nickName`);

alter table users add column experience varchar(1000) DEFAULT NULL after description;
/* 注意，上面这个命令的意思是说添加addr列到user1这一列后面。如果想添加到第一列的话，可以用：*/
alter table t1 add column addr varchar(20) not null first;

drop table users;

SELECT * FROM users;

delete from users;

insert into users values( null, 'd253c5f077b52bc4a569172b7ac8789e', '121', '212', '1534b76d325a8f591b52d302e7181331', '212', 1, '2122@qq.com', null, null, '1', 1, 1, 0, 'it', 'null', '8a1a1666d21a3d03918f55fdfff319d7', 1511691503 );
/* 导航表 */

CREATE TABLE `navs` ( /*导航*/
	`navId` int PRIMARY KEY AUTO_INCREMENT,
	`navIndex` tinyint NOT NULL,
	`navName` varchar(12) NOT NULL,
	`navUrl` varchar(32) NOT NULL,
	`parentId` int NOT NULL,
	`navTarget` tinyint DEFAULT NULL /* 0 新页面 ， 1 当前页*/
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `navs` ADD unique(`navName`);

insert into navs values( null, 1, '首页', '/', 0, 0);
insert into navs values( null, 1, '文章', '/', 0, 0);
insert into navs values( null, 1, '热门', '/', 0, 0);
insert into navs values( null, 1, '最新', '/', 0, 0);

drop table navs;

SELECT * FROM navs;

/* 附件 */

CREATE TABLE `files` ( /*附件*/
	`fileId` int PRIMARY KEY AUTO_INCREMENT,
	`fileCode` varchar(64) NOT NULL,
	`fileName` varchar(70) NOT NULL,
	`type` TINYINT, /* 0 代表头像 1，代表封面，2、代表文章， 3、代表系统图片*/
	`url` varchar(500) NOT NULL, /* 访问路径 */
	`userCode` varchar(64) NOT NULL,
	`subjectCode` varchar(64) DEFAULT NULL, /* 主体的Code   主体一般为文章*/
	`filesMime` varchar(16),
	`laseShowTime` int
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

drop table files;

/* 海报 */
CREATE TABLE `banners` ( /*附件*/
	`bannerId` int PRIMARY KEY AUTO_INCREMENT,
	`toUrl` varchar(220) NOT NULL, /* 访问路径 */
	`imgUrl` varchar(220)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

insert into banners values( null, '/', '/');

drop table banners;

CREATE TABLE `systems` ( /*系统设置*/
	`id` int PRIMARY KEY AUTO_INCREMENT,
	`name` VARCHAR(20) NOT NULL,
	`host` VARCHAR(100) NOT NULL,
	`desc` varchar(500) NOT NULL, /* 网站描述 */
	`mark` varchar(20) NOT NULL, /* 网站描述 */
	`logo` varchar(220) NOT NULL, /* 网站logo */
	`logo2`  varchar(220) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

drop table systems;

CREATE TABLE `articals` ( /* 文章 */
	`articalId` int PRIMARY KEY AUTO_INCREMENT,
	`articalCode` varchar(64) NOT NULL,
	`articalTitle` varchar(60) NOT NULL,
	`userCode` varchar(64) DEFAULT NULL,
	`categoryCode` varchar(64) DEFAULT NULL,
	`articalContent` varchar(18000) DEFAULT NULL,
	`articalPhoto` varchar(80) DEFAULT NULL, /* 封面图片 */
	`articalImages` varchar(1500) DEFAULT NULL, /* 图片地址 */
	`articalCreateDate` int DEFAULT 0, 
	`articalType` tinyint DEFAULT 0, /* 文章或是图片或是专栏文章 0 ，1 , 2 */
	`articalView` int DEFAULT 0,  /* 点击数 */
	`articalMark` int DEFAULT 0,  /* 点赞数 */
	`articalCommentNum` int DEFAULT 0, /* 评论数 */
	`articalStatus` tinyint DEFAULT 0, /* 0，保存，1，发布，状态为2则为精品*/
	`bookCode` varchar(64) DEFAULT NULL /* 专栏id 专栏文章photo和type取book得photo和type*/
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

insert into articals values(null,'2123131233213','测试', 1,1,13123123,'http://127.0.0.1:8080/files/23D31E0AAE3D4EC9BD46B66F71961A82.jpg',null,14212331343543,'artical', 143,4536,3321,1, 1);

insert into articals values ( null, '50551427efc6daf174f0afb494aff8af', '21', '543e8e89ce8485efe5c11746c7d7fc35', 1, '徐志飞测试徐志飞测试2', '201711281915225a1d454a1ee7d', '', 1511867857, 0, 0, 0, 0, 1);

drop table articals;

select count(*) from articals;

/* 联合查询 */
select articals.*, users.* from articals left join users on articals.userCode = users.userCode where articalStatus = 1 order by articalCreateDate desc limit 4;


CREATE TABLE `articalMarks` ( /*点赞列表*/
	`markId` int PRIMARY KEY AUTO_INCREMENT,
	`markCode` varchar(64) NOT NULL,
	`articalCode` varchar(64) NOT NULL,
	`userCode` varchar(64) DEFAULT NULL,
	`createDate` int DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


drop table articalMarks;

CREATE TABLE `articalViews` ( /*查看列表*/
	`viewId` int PRIMARY KEY AUTO_INCREMENT,
	`viewCode` varchar(64) NOT NULL,
	`articalCode` varchar(64) NOT NULL,
	`visitorIp` varchar(32) DEFAULT NULL,
	`createDate` int DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

select * from articalViews;

drop table articalViews;

CREATE TABLE `userFocus` ( /*关注列表*/
	`focusId` int PRIMARY KEY AUTO_INCREMENT,
	`focusCode` varchar(64) NOT NULL,
	`byFocusUserCode` varchar(64) DEFAULT NULL, /*被关注人*/
	`focusUserCode` varchar(64) DEFAULT NULL, /* 关注人*/
	`createDate` int DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

drop table userFocus;

CREATE TABLE `emails` ( /*站内信*/
	`emailId` int PRIMARY KEY AUTO_INCREMENT,
	`emailCode` varchar(64) NOT NULL,
	`sendUserCode` varchar(64) DEFAULT NULL, /*发件人人*/
	`receiveUserCode` varchar(64) DEFAULT NULL, /* 收件人 */
	`eamilTitle` varchar(40) DEFAULT NULL,
	`eamilContent` varchar(2000) DEFAULT NULL,
	`emailStatus` tinyint, /* 未读，已读, 草稿 0， 1， 2*/
	`emailCreateDate` int DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

drop table emails;

-- CREATE TABLE `experiences` ( /*工作经历*/
-- 	`experienceId` int PRIMARY KEY AUTO_INCREMENT,
-- 	`experienceCode` varchar(64) NOT NULL,
-- 	`userCode` varchar(64) DEFAULT NULL, /*发件人人*/
-- 	`experienceStartTime` int DEFAULT 0, /* 收件人 */
-- 	`experienceEndTime` int DEFAULT 0,
-- 	`experienceContent` varchar(400) DEFAULT NULL, /* 工作描述 */
-- 	`companyName` varchar(50) DEFAULT NULL,
-- 	`experienceCreateDate` int DEFAULT 0
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `books` (  /* 专栏 */
	`bookId` int PRIMARY KEY AUTO_INCREMENT,
	`bookCode` varchar(64) NOT NULL,
	`bookPhoto` varchar(80) DEFAULT NULL, /* 封面图片 */
	`bookName` varchar(30) NOT NULL,
	`categoryCode` int DEFAULT NULL,
	`userCode` varchar(64) NOT NULL,
	`bookView` int DEFAULT 0,  /* 点击数 */
	`bookMark` int DEFAULT 0,  /* 点赞数 */
	`bookCommentNum` int DEFAULT 0, /* 评论数 */
	`bookDesc` varchar(200) DEFAULT NULL, 
	`bookCreateDate` int DEFAULT 0,
	`bookStatus` tinyint /* 暂存，普通，新品，推荐，精品 0， 1， 2， 3, 4*/
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

insert into books values( null, 1 ,'首页', '/', 1, 0, 0, '_blank');

drop table books;

CREATE TABLE `categorys` (  /*分类*/
	`categoryId` int PRIMARY KEY AUTO_INCREMENT,
	`categoryCode` varchar(64) NOT NULL,
	`categoryName` varchar(40) NOT NULL,
	`parentCategoryId` int,
	`categoryStatus` tinyint
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

insert into categorys values( null, "1", '分类1', 0, 1);
insert into categorys values( null, "2", '分类2', 0, 1);
insert into categorys values( null, "3", '分类3', 0, 1);

drop table categorys;

CREATE TABLE `comments` ( /*评论*/
	`commentId` int PRIMARY KEY AUTO_INCREMENT,
	`commentCode` varchar(64) NOT NULL,
	`articalCode` varchar(64) NOT NULL,
	`userCode` varchar(64) NOT NULL,
	`bookCode` varchar(64) NOT NULL,
	`comContent` varchar(400), /*200字数*/
	`comCreateDate` int,
	`comParentType` tinyint, /* 主体是文章、评论, 专栏   0 ， 1,  2*/
	`comParentCode` varchar(64) NOT NULL, /* 主体 code */
	`comMark` int DEFAULT NULL /* 点赞数 */
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

drop table comments;

CREATE TABLE `commentMarks` ( /*评论点赞列表*/
	`commentMarkId` int PRIMARY KEY AUTO_INCREMENT,
	`commentMarkCode` varchar(64) NOT NULL,
	`commentCode` varchar(64) NOT NULL,
	`userCode` varchar(64) DEFAULT NULL,
	`createDate` int DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

drop table articalMarks;