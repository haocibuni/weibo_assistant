-- phpMyAdmin SQL Dump
-- version 4.4.15.10
-- https://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: 2021-06-11 09:43:45
-- 服务器版本： 5.6.50-log
-- PHP Version: 7.1.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `weibo`
--

-- --------------------------------------------------------

--
-- 表的结构 `weibo_keyword`
--

CREATE TABLE IF NOT EXISTS `weibo_keyword` (
  `id` int(11) unsigned NOT NULL COMMENT '列表id',
  `user_id` int(11) NOT NULL COMMENT '用户id',
  `keyword` varchar(255) DEFAULT NULL COMMENT '关键字',
  `weibo_name` varchar(255) DEFAULT NULL COMMENT '发布者用户名',
  `content` varchar(255) DEFAULT NULL COMMENT '微博内容',
  `like_num` int(11) NOT NULL DEFAULT '0' COMMENT '点赞数',
  `forward_num` int(11) DEFAULT NULL COMMENT '转发数',
  `conment_num` int(11) DEFAULT NULL COMMENT '评论数',
  `keyword_value` varchar(255) DEFAULT NULL COMMENT '情感值',
  `search_time` int(11) NOT NULL COMMENT '搜索时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `weibo_keyword_record`
--

CREATE TABLE IF NOT EXISTS `weibo_keyword_record` (
  `id` int(11) NOT NULL COMMENT '列表id',
  `user_id` int(11) NOT NULL COMMENT '用户id',
  `keyword` varchar(255) NOT NULL COMMENT '关键词',
  `date` int(11) NOT NULL,
  `ave_value` varchar(255) NOT NULL COMMENT '情感均值'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `weibo_page_comment`
--

CREATE TABLE IF NOT EXISTS `weibo_page_comment` (
  `id` int(11) NOT NULL COMMENT '列表id',
  `weibopage_id` varchar(255) NOT NULL DEFAULT '' COMMENT '微博id',
  `user_id` int(11) NOT NULL COMMENT '用户id',
  `date` int(11) NOT NULL COMMENT '分析时间',
  `weibopage` varchar(255) DEFAULT NULL COMMENT '评论微博地址',
  `comment_name` varchar(255) DEFAULT NULL COMMENT '评论者昵称',
  `comment` varchar(255) DEFAULT NULL COMMENT '评论内容',
  `page_value` varchar(255) DEFAULT NULL COMMENT '情感值'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `weibo_page_information`
--

CREATE TABLE IF NOT EXISTS `weibo_page_information` (
  `weibopage_id` varchar(255) NOT NULL DEFAULT '' COMMENT '微博id',
  `nick_name` varchar(255) DEFAULT NULL COMMENT '微博昵称',
  `gender` varchar(255) DEFAULT NULL COMMENT '性别',
  `city` varchar(255) DEFAULT NULL COMMENT '城市',
  `follows_num` int(11) DEFAULT NULL COMMENT '关注数',
  `fans_num` int(11) DEFAULT NULL COMMENT '粉丝数',
  `tweets_num` int(11) DEFAULT NULL COMMENT '微博数'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `weibo_page_record`
--

CREATE TABLE IF NOT EXISTS `weibo_page_record` (
  `id` int(11) NOT NULL COMMENT '列表id',
  `user_id` int(11) NOT NULL COMMENT '用户id',
  `date` int(11) NOT NULL COMMENT '分析时间',
  `ave_value` varchar(255) NOT NULL COMMENT '情感均值'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `weibo_user`
--

CREATE TABLE IF NOT EXISTS `weibo_user` (
  `id` int(11) NOT NULL COMMENT '列表id',
  `open_id` varchar(50) NOT NULL COMMENT 'openID',
  `nick_name` varchar(100) NOT NULL COMMENT '昵称',
  `avatar_url` varchar(300) NOT NULL COMMENT '头像url',
  `sex` int(1) NOT NULL COMMENT '性别',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  `weibopage_id` varchar(255) DEFAULT 'rmrb' COMMENT '微博主页id',
  `kw_depth` int(11) NOT NULL DEFAULT '0' COMMENT '关键字搜索爬取深度',
  `pg_depth` int(11) NOT NULL DEFAULT '0' COMMENT '主页评论爬取深度'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `weibo_user_login_record`
--

CREATE TABLE IF NOT EXISTS `weibo_user_login_record` (
  `id` int(11) NOT NULL COMMENT '列表id',
  `user_id` int(11) NOT NULL COMMENT '用户ID',
  `session` varchar(300) NOT NULL COMMENT '第三方session',
  `session_key` varchar(300) NOT NULL COMMENT '微信官方session',
  `create_time` int(11) NOT NULL COMMENT '创建时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `weibo_keyword`
--
ALTER TABLE `weibo_keyword`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `weibo_keyword_record`
--
ALTER TABLE `weibo_keyword_record`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `weibo_page_comment`
--
ALTER TABLE `weibo_page_comment`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `weibo_page_information`
--
ALTER TABLE `weibo_page_information`
  ADD PRIMARY KEY (`weibopage_id`);

--
-- Indexes for table `weibo_page_record`
--
ALTER TABLE `weibo_page_record`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `weibo_user`
--
ALTER TABLE `weibo_user`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `weibo_user_login_record`
--
ALTER TABLE `weibo_user_login_record`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `weibo_keyword`
--
ALTER TABLE `weibo_keyword`
  MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '列表id';
--
-- AUTO_INCREMENT for table `weibo_keyword_record`
--
ALTER TABLE `weibo_keyword_record`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '列表id';
--
-- AUTO_INCREMENT for table `weibo_page_comment`
--
ALTER TABLE `weibo_page_comment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '列表id';
--
-- AUTO_INCREMENT for table `weibo_page_record`
--
ALTER TABLE `weibo_page_record`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '列表id';
--
-- AUTO_INCREMENT for table `weibo_user`
--
ALTER TABLE `weibo_user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '列表id';
--
-- AUTO_INCREMENT for table `weibo_user_login_record`
--
ALTER TABLE `weibo_user_login_record`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '列表id';
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
