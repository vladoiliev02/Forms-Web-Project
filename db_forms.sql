create database forms;

use forms;

CREATE TABLE `User` (
  `id` integer PRIMARY KEY,
  `username` varchar(255),
  `email` varchar(255),
  `password` int
);

CREATE TABLE `Form` (
  `id` integer PRIMARY KEY,
  `title` varchar(255),
  `user_id` integer
);

CREATE TABLE `Question` (
  `id` integer PRIMARY KEY,
  `form_id` integer,
  `value` text
);

CREATE TABLE `Answer` (
  `id` integer PRIMARY KEY,
  `question_id` integer,
  `value` text,
  `user_id` int
);

ALTER TABLE `Form` ADD FOREIGN KEY (`id`) REFERENCES `User` (`id`);

ALTER TABLE `Question` ADD FOREIGN KEY (`id`) REFERENCES `Form` (`id`);

ALTER TABLE `Answer` ADD FOREIGN KEY (`id`) REFERENCES `Question` (`id`);

ALTER TABLE `Answer` ADD FOREIGN KEY (`user_id`) REFERENCES `User` (`id`);
