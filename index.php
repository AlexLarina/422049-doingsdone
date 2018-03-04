<?php
    require_once('functions.php');
    require_once('init.php');
    require_once ('mysql_helper.php');

    $show_complete_tasks = 0;
    $body_class = '';
    $form_content = null;
    $session = null;
    $auth_form = null;
    $project_form_content = null;
    $username = '';
    $guest = null;
    $cookie_value = 1;
    $task_list = [];
    $projects = [];
    $project_names = [];
    $tasks_in_category = [];

    session_start();

    if (isset($_SESSION['user'])) {
        $session = $_SESSION['user'];
        $username = $session['name'];
        $user_id = $session['id'];

        $project_result = mysqli_query($db_link, 'SELECT * FROM projects WHERE user_id = '.$user_id);
        $projects_list = mysqli_fetch_all($project_result, MYSQLI_ASSOC);

        foreach ($projects_list as $proj){
            $projects_item = [
                'DB_id' => $proj['id'],
                'name' => $proj['name']
            ];
            array_push($projects, $projects_item);
            array_push($project_names, $proj['name']);
        }

        if($_COOKIE['showcompl']) {
            $task_result_all = mysqli_query($db_link, 'SELECT * FROM tasks WHERE user_id = '.$user_id);
        } else {
            $task_result_all = mysqli_query($db_link, 'SELECT * FROM tasks WHERE user_id = '.$user_id.' AND dt_done IS NULL');
        }
        $task_list  = renderTasks($db_link, $task_result_all);

        if(isset($_GET['id'])) {
            $id = $_GET['id'];
            if($_COOKIE['showcompl']) {
                $task_result = mysqli_query($db_link, 'SELECT * FROM tasks WHERE user_id = '.$user_id.' AND project_id = '.$projects[$id]['DB_id']);
            } else {
                $task_result = mysqli_query($db_link, 'SELECT * FROM tasks WHERE user_id = '.$user_id.' AND project_id = '.$projects[$id]['DB_id'].' AND dt_done IS NULL');
            }
        } else {
            if($_COOKIE['showcompl']) {
                $task_result = mysqli_query($db_link, 'SELECT * FROM tasks WHERE user_id = '.$user_id);
            } else {
                $task_result = mysqli_query($db_link, 'SELECT * FROM tasks WHERE user_id = '.$user_id.' AND dt_done IS NULL');
            }

            //print_r(date('Y-m-d'));
            if(isset($_GET['agenda'])) {
                $task_result = mysqli_query($db_link, 'SELECT * FROM tasks WHERE user_id = '.$user_id.' AND dt_done IS NULL'
                                            .' AND dt_deadline = CURDATE()');
            }

            if(isset($_GET['tomorrow'])) {
                $task_result = mysqli_query($db_link, 'SELECT * FROM tasks WHERE user_id = '.$user_id.' AND dt_done IS NULL'
                    .' AND dt_deadline = DATE_ADD(NOW(), INTERVAL 1 DAY)');
            }

            if(isset($_GET['overdue'])) {
                $task_result = mysqli_query($db_link, 'SELECT * FROM tasks WHERE user_id = '.$user_id.' AND dt_done IS NULL'
                    .' AND dt_deadline < CURDATE()');
            }
        }

        $tasks_in_category = renderTasks($db_link, $task_result);
        if(isset($_GET['done'])) {
            $task_done = $_GET['done'];
            $sql = 'UPDATE tasks SET dt_done = now() WHERE id = '.$task_done;
            $stmt = db_get_prepare_stmt($db_link, $sql);
            $result = mysqli_stmt_execute($stmt);
            if($result){
                header('Location: index.php');
            } else {
                echo mysqli_error($db_link);
                exit();
            }
        }

    } else {
        $guest = include_template('templates/guest.php', []);
        if (isset($_GET['login'])) {
            $body_class = 'overlay';
            $auth_form = include_template('templates/auth_form.php', [
                'errors' => [],
                'classname' => '',
                'err_message' => '',
                'authorization' => ''
            ]);
        }
    }
    if (isset($_GET['show_completed'])) {
        if (isset($_COOKIE['showcompl'])) {
            $cookie_value = toggle_value($_COOKIE['showcompl']);
        }
        setcookie('showcompl', $cookie_value, strtotime("+30 days"), "/");
        header('Location: /');
    }
    if (isset($_COOKIE['showcompl'])) {
        $show_complete_tasks = $_COOKIE['showcompl'];
    }
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['signin'])) {
        $authorization = $_POST;

        $required = ['email', 'password'];
        $errors = [];
        $classname = '';
        $err_message = '';

        $user = searchUserByEmailInDB($db_link, $authorization['email']);

        foreach ($required as $value) {
            if (empty($authorization[$value])) {
                $errors[$value] = 'Это поле надо заполнить';
            }
        }

        if (!empty($authorization['email']) && !$user) {
                $errors['email'] = 'Пользователь не существует';
                $errors['password'] = '';
        }

        if (!count($errors) && $user) {
            if (password_verify($authorization['password'], $user['password'])) {
                $_SESSION['user'] = $user;
                header('Location: /index.php');
            } else {
                $errors['password'] = 'Неправильный пароль';
            }
        }
        if (count($errors)) {
            $body_class = 'overlay';
            $auth_form = include_template('templates/auth_form.php', [
                'errors' => $errors,
                'classname' => $classname,
                'err_message' => $err_message,
                'authorization' => $authorization
            ]);
        }
    }

    if (isset($_GET['logout'])) {
        unset($_SESSION['user']);
        header('Location: /index.php');
    }

    if(isset($_GET['add'])) {
        $body_class = 'overlay';
        $form_content = include_template('templates/form.php', ['projects' => $projects]);
    }

    if(isset($_GET['add_project'])) {
        $body_class = 'overlay';
        $project_form_content = include_template('templates/add_project_form.php', []);
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_project'])) {
        $new_project = $_POST;
        $errors = [];

        if (empty($_POST['name'])) {
            $errors['name'] = 'Это поле надо заполнить';
        }

        if (count($errors)) {
            $body_class = 'overlay';
            $project_form_content = include_template('templates/add_project_form.php', [
                'errors' => $errors,
                'new_project' => $new_project
                ]);
        } else {
            $sql = 'INSERT INTO projects (name, user_id) VALUES(?, ?)';
            $stmt = db_get_prepare_stmt($db_link, $sql, [$new_project['name'], $_SESSION['user']['id']]);
            $result = mysqli_stmt_execute($stmt);
            if($result){
                header('Location: index.php');
            } else {
                echo mysqli_error($db_link);
                exit();
            }
        }
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['task'])) {
        $new_task = $_POST;
        $errors = [];

        if (empty($_POST['task'])) {
            $errors['task'] = 'Это поле надо заполнить';
        }

        if (!in_array($_POST['category'], $project_names)) {
            $errors['category'] = 'Выберите из предложенного';
        }

        $current_date = date('d.m.Y');
        if ($current_date > date('d.m.Y', strtotime($new_task['date']))) {
            $errors['date'] = 'Выберите дату не позже сегодняшней';
        }

        if (isset($_FILES['preview']['name'])) {
            $tmp_name = $_FILES['preview']['tmp_name'];
            $path = $_FILES['preview']['name'];
            move_uploaded_file($tmp_name, '' . $path);
            $new_task['path'] = $path;
        }

        if (count($errors)) {
            $body_class = 'overlay';
            $form_content = include_template('templates/form.php', [
                'errors' => $errors,
                'new_task' => $new_task,
                'body_class' => $body_class,
                'projects' => $projects]);
        } else {
            $key = array_search($new_task['category'], $project_names);
            $sql = 'INSERT INTO tasks (dt_add, name, file_path, dt_deadline, user_id, project_id)
                    VALUES(NOW(), ?, ?, ?, ?, ?)';

            $stmt = db_get_prepare_stmt($db_link, $sql, [
                $new_task['task'],
                $new_task['preview'],
                date('Y-m-d', strtotime($new_task['date'])),
                $_SESSION['user']['id'],
                $projects[$key]['DB_id']
            ]);
            $result = mysqli_stmt_execute($stmt);
            if($result){
                header('Location: index.php');
            } else {
                echo mysqli_error($db_link);
                exit();
            }

        }
    }

    $page_content = include_template('templates/index.php', [
        'task_list' => $tasks_in_category,
        'show_complete_tasks' => $show_complete_tasks
    ]);

    $layout_content = include_template('templates/layout.php', [
        'content' => $page_content,
        'title' => 'Дела в порядке',
        'task_list' => $task_list,
        'projects' => $projects,
        'body_class' => $body_class,
        'form_content' => $form_content,
        'project_form_content' => $project_form_content,
        'guest' => $guest,
        'session' => $session,
        'auth_form' => $auth_form,
        'username' => $username
    ]);

    print($layout_content);

?>
