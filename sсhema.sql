CREATE DATABASE `doingsdone` COLLATE='utf8_general_ci';
USE `doingsdone`;

CREATE TABLE `projects` (
  `project_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `project_name` varchar(50) NOT NULL,
  PRIMARY KEY (`project_id`),
  KEY `user_id` (`user_id`)
);

CREATE TABLE `tasks` (
  `task_id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `task_name` varchar(50) NOT NULL,
  `task_create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `task_complete_date` timestamp NULL DEFAULT NULL,
  `task_deadline` timestamp NULL DEFAULT NULL,
  `task_file` varchar(100) DEFAULT NULL,
  `task_complete_status` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`task_id`),
  KEY `project_id` (`project_id`),
  KEY `user_id` (`user_id`),
  FULLTEXT KEY `task_name` (`task_name`)
);

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_name` varchar(50) NOT NULL,
  `user_email` varchar(50) NOT NULL,
  `user_password` varchar(60) NOT NULL,
  `user_contacts` varchar(100) DEFAULT NULL,
  `register_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `user_email` (`user_email`)
);

ALTER TABLE `projects` ADD CONSTRAINT `FK_projects_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);
ALTER TABLE `tasks` ADD CONSTRAINT `FK_tasks_projects` FOREIGN KEY (`project_id`) REFERENCES `projects` (`project_id`);
ALTER TABLE `tasks` ADD CONSTRAINT `FK_tasks_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);
