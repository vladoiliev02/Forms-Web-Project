drop database if exists forms;
create database forms;

use forms;

create table `user` (
  `id` int primary key auto_increment,
  `username` varchar(255) unique,
  `email` varchar(255) unique,
  `password` varchar(255)
);

create table `form` (
  `id` int primary key auto_increment,
  `user_id` int,
  `title` varchar(255),
  unique(`user_id`, `title`),
  foreign key (`user_id`) references `user`(`id`)
);

create table `question` (
  `id` int primary key auto_increment,
  `form_id` int,
  `value` text,
  `type` varchar(255),
  `values` text,
  `min` varchar(255),
  `max` varchar(255),
  `step` varchar(255),
  foreign key (`form_id`) references `form`(`id`)
);

create table `answer` (
  `id` int primary key auto_increment,
  `question_id` int,
  `user_id` int,
  `value` text,
  foreign key (`question_id`) references `question`(`id`),
  foreign key (`user_id`) references `user`(`id`)
);
