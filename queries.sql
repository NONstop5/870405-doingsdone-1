USE `doingsdone`;

/*Добавляем двух пользователей в таблицу users*/
INSERT INTO `users`
  (`user_name`, `user_email`, `user_password`, `user_contacts`, `register_date`)
VALUES
  ('NON$top', 'non_2002@mail.ru', '55555', NULL, '2018-09-23 10:44:51'),
  ('Keks', 'keks@mail.ru', '12345', NULL, '2018-09-23 10:44:51');

/*Добавляем проекты в таблицу projects*/
INSERT INTO `projects`
  (`user_id`, `project_name`)
VALUES
  (1,	'Входящие'),
  (1,	'Учеба'),
  (1,	'Работа'),
  (1,	'Домашние дела'),
  (1,	'Авто');

/*Добавляем задачи в таблицу tasks. Считаем, что все эти задачи созданы одним пользователем с user_id=1. Так же проставляем связи с проектами*/
INSERT INTO `tasks`
  (`project_id`, `user_id`, `task_name`, `task_create_date`, `task_complete_date`, `task_deadline`, `task_file`, `task_complete_status`)
VALUES
  (3, 1, 'Собеседование в IT <b>компании</b>', '2018-09-23 10:44:51', NULL, '2018-09-20 00:00:00', NULL, 0),
  (3, 1, 'Выполнить тестовое задание', '2018-09-23 10:44:51', NULL, '2018-09-23 00:00:00', NULL, 0),
  (2, 1, 'Сделать задание первого раздела', '2018-09-23 10:44:51', NULL, '2018-12-21 00:00:00', NULL, 1),
  (1, 1, 'Встреча с другом', '2018-09-23 10:44:51', NULL, '2018-09-24 00:00:00', NULL, 0),
  (4, 1, 'Купить корм для кота', '2018-09-23 10:44:51', NULL, NULL, NULL, 0),
  (4, 1, 'Заказать пиццу', '2018-09-23 10:44:51', NULL, NULL, NULL, 0);

/*получить список из всех проектов для одного пользователя*/
SELECT
	`project_name`
FROM
	`users`, `projects`
WHERE
	users.`user_id` = projects.`user_id`
AND
	users.`user_name` = 'NON$top';

/*получить список из всех задач для одного проекта*/
SELECT
	`task_name`
FROM
	`tasks`
WHERE
	`project_id` = 3;

/*пометить задачу как выполненную*/
UPDATE
	`tasks`
SET
	`task_complete_status` = 1
WHERE
	`task_id` = 3;

/*получить все задачи для завтрашнего дня*/
SELECT
	`task_name`
FROM
	`tasks`
WHERE
	`task_complete_status` = 0;

/*обновить название задачи по её идентификатору*/
UPDATE
	`tasks`
SET
	`task_name` = 'Сделать задание первого раздела (update)'
WHERE
	`task_id` = 3;
