-- filling projects table

INSERT INTO projects SET name = 'Все', user_id = 1;
INSERT INTO projects SET name = 'Все', user_id = 2;
INSERT INTO projects SET name = 'Все', user_id = 3;

INSERT INTO projects SET name = 'Входящие', user_id = 1;
INSERT INTO projects SET name = 'Входящие', user_id = 2;
INSERT INTO projects SET name = 'Входящие', user_id = 3;

INSERT INTO projects SET name = 'Учеба', user_id = 1;
INSERT INTO projects SET name = 'Работа', user_id = 1;

INSERT INTO projects SET name = 'Учеба', user_id = 2;
INSERT INTO projects SET name = 'Домашние дела', user_id = 2;

INSERT INTO projects SET name = 'Работа', user_id = 3;
INSERT INTO projects SET name = 'Авто', user_id = 3;

-- filling users table

INSERT INTO users SET email = 'ignat.v@gmail.com', name = 'Игнат', password = '$2y$10$OqvsKHQwr0Wk6FMZDoHo1uHoXd4UdxJG/5UDtUiie00XaxMHrW8ka';
INSERT INTO users SET email = 'kitty_93@li.ru', name = 'Леночка', password = '$2y$10$bWtSjUhwgggtxrnJ7rxmIe63ABubHQs0AS0hgnOo41IEdMHkYoSVa';
INSERT INTO users SET email = 'warrior07@mail.ru', name = 'Руслан', password = '$2y$10$2OxpEH7narYpkOT1H5cApezuzh10tZEEQ2axgFOaKW.55LxIJBgWW';

-- filling tasks table

INSERT INTO tasks SET name = 'Собеседование в IT компании', dt_deadline = '01.06.2018', user_id = 1, project_id = 8;
INSERT INTO tasks SET name = 'Выполнить тестовое задание', dt_deadline = '25.05.2018', user_id = 3, project_id = 11;
INSERT INTO tasks SET name = 'Сделать задание первого раздела', dt_deadline = '21.04.2018', user_id = 2, project_id = 9;

INSERT INTO tasks SET name = 'Встреча с другом', dt_deadline = '22.04.2018', user_id = 1, project_id = 4;
INSERT INTO tasks SET name = 'Встреча с другом', dt_deadline = '22.04.2018', user_id = 2, project_id = 5;
INSERT INTO tasks SET name = 'Встреча с другом', dt_deadline = '22.04.2018', user_id = 3, project_id = 6;

INSERT INTO tasks SET name = 'Купить корм для кота', dt_deadline = NULL, user_id = 2, project_id = 10;
INSERT INTO tasks SET name = 'Заказать пиццу', dt_deadline = NULL, user_id = 2, project_id = 10;

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


