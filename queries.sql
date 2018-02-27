-- filling projects table
INSERT INTO projects (name, user_id) VALUES ('Все', 1), ('Все', 2), ('Все', 3), ('Входящие', 1), ('Входящие', 2),
  ('Входящие', 3), ('Учеба', 1), ('Работа', 1), ('Учеба', 2), ('Домашние дела', 2), ('Работа', 3), ('Авто', 3);

-- filling users table

INSERT INTO users (email, name, password) VALUES
  ('ignat.v@gmail.com', 'Игнат', '$2y$10$OqvsKHQwr0Wk6FMZDoHo1uHoXd4UdxJG/5UDtUiie00XaxMHrW8ka'),
  ('kitty_93@li.ru', 'Леночка', '$2y$10$bWtSjUhwgggtxrnJ7rxmIe63ABubHQs0AS0hgnOo41IEdMHkYoSVa'),
  ('warrior07@mail.ru', 'Руслан', '$2y$10$2OxpEH7narYpkOT1H5cApezuzh10tZEEQ2axgFOaKW.55LxIJBgWW');

-- filling tasks table

INSERT INTO tasks (name, dt_deadline, user_id, project_id) VALUES
  ('Собеседование в IT компании', '2018.06.01', 1, 8),
  ('Выполнить тестовое задание', '2018.05.25', 3, 11),
  ('Сделать задание первого раздела', '2018.04.21', 2, 9),
  ('Встреча с другом', '2018.04.22', 1, 4),
  ('Встреча с другом', '2018.04.22', 2, 5),
  ('Встреча с другом', '2018.04.22', 3, 6),
  ('Купить корм для кота', NULL, 2, 10),
  ('Заказать пиццу', NULL, 2, 10);

-- select

-- получить список из всех проектов для одного пользователя;
SELECT name FROM projects WHERE user_id = 1;

-- получить список из всех задач для одного проекта;

SELECT name FROM tasks WHERE project_id = 10;

-- пометить задачу как выполненную;

UPDATE tasks SET dt_done = NOW() WHERE id = 3;

-- получить все задачи для завтрашнего дня;

SELECT name FROM tasks WHERE dt_deadline = DATE_ADD(NOW(), INTERVAL 1 DAY);

-- обновить название задачи по её идентификатору
UPDATE tasks SET name = 'Встреча с корешами' WHERE id = 4;


