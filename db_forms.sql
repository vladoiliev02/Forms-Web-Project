create database forms;

use forms;

create table `user` (
  `id` int primary key,
  `username` varchar(255),
  `email` varchar(255),
  `password` varchar(255)
);

create table `form` (
  `id` int primary key,
  `user_id` int,
  `title` varchar(255),
  foreign key (`user_id`) references `user`(`id`)
);

create table `question` (
  `id` int primary key,
  `form_id` int,
  `value` text,
  foreign key (`form_id`) references `form`(`id`)
);

create table `answer` (
  `id` int primary key,
  `question_id` int,
  `user_id` int,
  `value` text,
  foreign key (`question_id`) references `question`(`id`),
  foreign key (`user_id`) references `user`(`id`)
);
