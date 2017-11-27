
create database if not exists yingview charset utf8;

show create database yingview;

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
	`photoImage` varchar(72) DEFAULT NULL, /* md5(fileid).jpeg*/
	`userLevel` tinyint DEFAULT NULL,
	`userPower` tinyint DEFAULT NULL,
	`userStatus` tinyint DEFAULT NULL,
	`userJob` varchar(16) DEFAULT NULL,
	`jobDesc` varchar(64) DEFAULT NULL, /* 描述职业的一句话*/
	`activeCode` varchar(64) DEFAULT NULL,
	`userCreateTime` int  DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `users` ADD unique(`userName`, `nickName`);

drop table users;

SELECT * FROM users;

delete from users;

insert into users values( null, 'd253c5f077b52bc4a569172b7ac8789e', '121', '212', '1534b76d325a8f591b52d302e7181331', '212', 1, '2122@qq.com', null, null, '1', 1, 1, 0, 'it', 'null', '8a1a1666d21a3d03918f55fdfff319d7', 1511691503 );
/* 导航表 */

CREATE TABLE `navs` ( /*导航*/
	`navId` int PRIMARY KEY AUTO_INCREMENT,
	`navIndex` smallint NOT NULL,
	`navName` varchar(12) NOT NULL,
	`navUrl` varchar(32) NOT NULL,
	`navStatus` tinyint NOT NULL,
	`navLevel` tinyint NOT NULL,
	`parentNavId` int NOT NULL,
	`navTarget` varchar(8) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
ALTER TABLE `navs` ADD unique(`navName`);

insert into navs values( null, 1, '首页', '/', 1, 0, 0, '_blank');
insert into navs values( null, 1, '文章', 'artical', 1, 0, 0, '_blank');
insert into navs values( null, 1, '热门', 'hot', 1, 0, 0, '_blank');
insert into navs values( null, 1, '最新', 'new', 1, 0, 0, '_blank');

drop table navs;

SELECT * FROM navs;

/* 附件 */

CREATE TABLE `files` ( /*附件*/
	`fileId` int PRIMARY KEY AUTO_INCREMENT,
	`filesCode` varchar(64) NOT NULL,
	`filesName` varchar(70) NOT NULL,
	`type` TINYINT, /* 0 代表头像 1，代表封面，2、代表文章*/
	`url` varchar(500) NOT NULL, /* 访问路径 */
	`userCode` varchar(64) NOT NULL,
	`filesMime` varchar(16)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

drop table files;

CREATE TABLE `articals` ( /*文章*/
	`articalId` int PRIMARY KEY AUTO_INCREMENT,
	`articalCode` varchar(64) NOT NULL,
	`articalTitle` varchar(60) NOT NULL,
	`userId` int DEFAULT NULL,
	`categoryId` int DEFAULT NULL,
	`articalContent` varchar(20000) DEFAULT NULL,
	`articalPhoto` varchar(80) DEFAULT NULL, /* 封面图片 */
	`articalImages` varchar(1600) DEFAULT NULL, /* 图片地址 */
	`articalCreateDate` int DEFAULT 0, 
	`articalType` tinyint DEFAULT 0, /* 文章或是图片 0 ，1 */
	`articalView` int DEFAULT 0,  /* 点击数 */
	`articalMark` int DEFAULT 0,  /* 查看数 */
	`articalCommentNum` int DEFAULT 0, /* 评论数 */
	`articalStatus` tinyint DEFAULT 0, /* 0，保存，1，发布，状态为2则为精品*/
	`bookId` int DEFAULT NULL /* 专栏id */
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

insert into articals values(null,'2123131233213','测试', 1,1,13123123,'http://127.0.0.1:8080/files/23D31E0AAE3D4EC9BD46B66F71961A82.jpg',null,14212331343543,'artical', 143,4536,3321,1);

select count(*) from articals;

/* 联合查询 */
select articals.*, users.* from articals left join users on articals.userId = users.userId;

CREATE TABLE `books` (  /* 专栏 */
	`bookId` int PRIMARY KEY AUTO_INCREMENT,
	`bookCode` varchar(64) NOT NULL,
	`bookName` varchar(30) NOT NULL,
	`userId` int,
	`bookStatus` tinyint
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


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

CREATE TABLE `comments` ( /*评论*/
	`commentId` int PRIMARY KEY AUTO_INCREMENT,
	`articalId` int,
	`userId` int,
	`comContent` varchar(400), /*200字数*/
	`comCreateDate` date DEFAULT NULL,
	`comParentType` tinyint, /* 主体是文章还是评论   0 ， 1 */
	`comParentId` int, /* 主体id */
	`comMark` int DEFAULT NULL, /* 点赞数 */
	`comCommentNum` int   /* 评论数 */
) ENGINE=InnoDB DEFAULT CHARSET=utf8;