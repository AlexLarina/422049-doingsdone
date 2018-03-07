<?php
    require_once('functions.php');
    require_once('init.php');
    require_once ('mysql_helper.php');
    require_once('vendor/autoload.php');

    $show_complete_tasks = 0;
    $cookie_value = 1;
    $time_zone_stamp = -10800;

    $form_content = null;
    $session = null;
    $auth_form = null;
    $project_form_content = null;
    $guest = null;

    $task_list = [];
    $projects = [];
    $project_names = [];
    $tasks_in_category = [];

    $body_class = '';
    $username = '';
    $id = 'all';
    $filter = '';
    $search_error = '';

    session_start();

    if (isset($_SESSION['user'])) {
        $session = $_SESSION['user'];
        $username = $session['name'];
        $user_id = $session['id'];

        $sql_projects_name = 'SELECT * FROM projects WHERE user_id = '.$user_id;

        $sql_projects_num = 'SELECT project_id, COUNT(project_id) FROM tasks 
                             WHERE user_id = '.$user_id.' GROUP BY project_id;';

        $sql_tasks = 'SELECT * FROM tasks WHERE tasks.user_id = '.$user_id;

        if(!$_COOKIE['showcompl']) {
            $sql_tasks = $sql_tasks.' AND dt_done is NULL';
            $sql_projects_num = 'SELECT project_id, COUNT(project_id) FROM tasks 
                                 WHERE dt_done is NULL AND user_id = '.$user_id.' 
                                 GROUP BY project_id;';
        }

        $sql_projects_name_result = mysqli_query($db_link, $sql_projects_name);
        $projects_name = mysqli_fetch_all($sql_projects_name_result, MYSQLI_ASSOC);

        $sql_projects_num_result = mysqli_query($db_link, $sql_projects_num);
        $projects_id_num = mysqli_fetch_all($sql_projects_num_result, MYSQLI_ASSOC);

        $projects = [];
        $total_task_number = 0;

        foreach ($projects_name as $key1 => $value1){
            $project = [
                'id' => $value1['id'],
                'name' => $value1['name'],
                'task_number' => 0,
            ];
            foreach ($projects_id_num as $key2 => $value2){
                if($value1['id'] == $value2['project_id']) {
                    $project['task_number'] = $value2['COUNT(project_id)'];
                    $total_task_number = $total_task_number + $value2['COUNT(project_id)'];
                }
            }
            if($value1['name'] != 'Все') {
                array_push($projects, $project);
            }
            array_push($project_names, $value1['name']);
        }
        array_unshift($projects, [
            'id' => null,
            'name' => 'Все',
            'task_number' => $total_task_number
        ]);

        if(isset($_GET['id'])) {
            $id = $_GET['id'];
            if($id != 'all') {
                $sql_tasks = $sql_tasks . ' AND project_id = ' . $projects[$id]['id'];
            }

        }

        if(isset($_GET['filter'])) {
            $filter = $_GET['filter'];

            switch ($_GET['filter']) {
                case 'all':
                    $sql_tasks = $sql_tasks;
                    break;
                case 'agenda':
                    $sql_tasks = $sql_tasks.' AND dt_deadline = CURDATE()';
                    break;
                case 'tomorrow':
                    $sql_tasks = $sql_tasks.' AND dt_deadline > nOW() AND dt_deadline <= DATE_ADD(NOW(), INTERVAL 1 DAY)';
                    break;
                case 'overdue':
                    $sql_tasks = $sql_tasks.' AND dt_deadline < CURDATE()';
                    break;
            }
        }

        $task_result = mysqli_query($db_link, $sql_tasks);
        $task_list = mysqli_fetch_all($task_result, MYSQLI_ASSOC);

        if (isset($_GET['search'])) {
            $search = $_GET['search'];
            if($search) {
                $sql_search_tasks = 'SELECT * FROM tasks WHERE MATCH(name) AGAINST(?) AND tasks.user_id = '.$user_id;
                $stmt = db_get_prepare_stmt($db_link, $sql_search_tasks, [$search]);
                mysqli_stmt_execute($stmt);
                $search_task_result = mysqli_stmt_get_result($stmt);
                $search_task_list = mysqli_fetch_all($search_task_result, MYSQLI_ASSOC);

                if($search_task_list) {
                    $task_list = $search_task_list;
                } else {
                    $task_list = [];
                    $search_error = 'По Вашему запросу ничего не найдено';
                }
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
        //print_r($new_project);

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
        if($new_task['date'] != ''){
            $date = date('Y-m-d', strtotime($new_task['date']));
            if ($current_date > date('d.m.Y', strtotime($new_task['date']))) {
                $errors['date'] = 'Выберите дату не позже сегодняшней';
            }
        } else {
            $date = date('Y-m-d', '0');
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
                $date,
                $_SESSION['user']['id'],
                $projects[$key]['id']
            ]);
            //date('Y-m-d', strtotime($new_task['date'])),
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
        'task_list' => $task_list,
        'id' => $id,
        'filter' => $filter,
        'search_error' => $search_error,
        'time_zone_stamp' => $time_zone_stamp,
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
        'username' => $username,
        'id' => $id
    ]);

    print($layout_content);

?>
