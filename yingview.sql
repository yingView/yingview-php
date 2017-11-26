
create database if not exists yingview charset utf8;

show create database yingview;

use yingview;

update tablename set column=123,column2=234 where 条件;

/* 用户表 uid username password passcode sax email tel birthday photo level power status job activecode*/

CREATE TABLE `users` ( /*用户*/
	`userId` int PRIMARY KEY AUTO_INCREMENT,
	`usercode` varchar(64) NOT NULL,
	`username` varchar(64) NOT NULL,
	`password` varchar(64) NOT NULL,
	`passcode` varchar(64) NOT NULL,
	`nickname` varchar(20) NOT NULL,
	`sax` tinyint,  /* 0 或者 1*/
	`email` varchar(32) NOT NULL,
	`tel` char(11) DEFAULT NULL,
	`bithday` int DEFAULT NULL,
	`photoimage` varchar(72) DEFAULT NULL, /* md5(fileid).jpeg*/
	`userlevel` tinyint DEFAULT NULL,
	`userpower` tinyint DEFAULT NULL,
	`userstatus` tinyint DEFAULT NULL,
	`userjob` varchar(16) DEFAULT NULL,
	`jobdesc` varchar(64) DEFAULT NULL, /* 描述职业的一句话*/
	`activecode` varchar(64) DEFAULT NULL,
	`usercreatetime` int  DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `users` ADD unique(`username`, `nickname`);

drop table users;

SELECT * FROM users;

delete from users;

insert into users values( null, 'd253c5f077b52bc4a569172b7ac8789e', '121', '212', '1534b76d325a8f591b52d302e7181331', '212', 1, '2122@qq.com', null, null, '1', 1, 1, 0, 'it', 'null', '8a1a1666d21a3d03918f55fdfff319d7', 1511691503 );
/* 导航表 */

CREATE TABLE `navs` ( /*导航*/
	`navId` int PRIMARY KEY AUTO_INCREMENT,
	`navindex` smallint NOT NULL,
	`navname` varchar(12) NOT NULL,
	`navurl` varchar(32) NOT NULL,
	`navstatus` tinyint NOT NULL,
	`navlevel` tinyint NOT NULL,
	`navtarget` varchar(8) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
ALTER TABLE `navs` ADD unique(`navname`);

insert into navs values( null, 1, '首页', '/', 1, 0, '_blank');
insert into navs values( null, 1, '文章', 'artical', 1, 0, '_blank');
insert into navs values( null, 1, '热门', 'hot', 1, 0, '_blank');
insert into navs values( null, 1, '最新', 'new', 1, 0, '_blank');

drop table navs;

SELECT * FROM navs;

/* 附件 */

CREATE TABLE `files` ( /*附件*/
	`fileId` int PRIMARY KEY AUTO_INCREMENT,
	`filescode` varchar(64) NOT NULL,
	`filesname` varchar(80) NOT NULL,
	`userId` int,
	`filesmime` varchar(16),
	`articalId` int
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `articals` ( /*文章*/
	`articalId` int PRIMARY KEY AUTO_INCREMENT,
	`articalcode` varchar(64) NOT NULL,
	`articaltitle` varchar(40) NOT NULL,
	`userId` int DEFAULT NULL,
	`typeId` int DEFAULT NULL,
	`articalcontent` varchar(20000) DEFAULT NULL,
	`articalphoto` varchar(80) DEFAULT NULL,
	`articalimages` varchar(1600) DEFAULT NULL, /* 图片地址 */
	`articalcreateDate` int,
	`articaltype` varchar(8) DEFAULT NULL, /* 文章或是图片 */
	`articalview` int DEFAULT NULL,
	`articalmark` int DEFAULT NULL,
	`articalcommentNum` int DEFAULT NULL,
	`articalstatus` int /* 状态为2则为精品*/
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

insert into articals values(null,'2123131233213','测试', 1,1,13123123,'http://127.0.0.1:8080/files/23D31E0AAE3D4EC9BD46B66F71961A82.jpg',null,14212331343543,'artical', 143,4536,3321,1);

select count(*) from articals;

/* 联合查询 */
select articals.*, users.* from articals left join users on articals.userId = users.userId;

CREATE TABLE `types` (  /*分类*/
	`typeId` int PRIMARY KEY AUTO_INCREMENT,
	`typecode` varchar(64) NOT NULL,
	`typeName` varchar(40) NOT NULL,
	`typeparentId` int,
	`typestatus` int
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

insert into types values( null, "1", '分类1', 0, 1);
insert into types values( null, "2", '分类2', 0, 1);
insert into types values( null, "3", '分类3', 0, 1);

CREATE TABLE `comments` ( /*评论*/
	`commentId` int PRIMARY KEY AUTO_INCREMENT,
	`articalId` int,
	`userId` int,
	`comcontent` varchar(500),
	`comcreateDate` date DEFAULT NULL,
	`comparentType` varchar(10), /* 主体是文章还是评论 */
	`comparentId` int /* 主体id */
) ENGINE=InnoDB DEFAULT CHARSET=utf8;