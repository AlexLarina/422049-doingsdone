<?php
    require_once('functions.php');
    require_once('data.php');
    require_once('userdata.php');
    require_once('init.php');

    $show_complete_tasks = 0;
    $body_class = '';
    $form_content = null;
    $session = null;
    $auth_form = null;
    $reg_form = null;
    $username = '';
    $guest = null;

    $tasks_in_category = [];

    session_start();
    if (isset($_SESSION['user'])) {
        $session = $_SESSION['user'];
        $username = $_SESSION['user']['name'];
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

    /*if(isset($_POST['signup'])){
        $signup = $_POST;
        $required = ['email', 'password', 'name'];
        $errors = [];
        foreach ($required as $value) {
            if (empty($signup[$value])) {
                $errors[$value] = 'Это поле надо заполнить';
            }
        }
        print_r('welcome');
    }*/

    /*$sql = "SELECT email FROM users";
    $result = mysqli_query($db_link, $sql);
    $rows = mysqli_fetch_all($result);
    $user_email = [];
    foreach ($rows as $row){
        array_unshift($user_email, $row[0]);
        //print_r($row[0].', ');
    }
    //print_r($user_email);
    print_r(searchUserByEmail('ignfrfat.v@gmail.com', $user_email));*/
    if (isset($_POST['signin'])) {
        $authorization = $_POST;
        $required = ['email', 'password'];
        $errors = [];
        $user = searchUserByEmail($authorization['email'], $users);

        foreach ($required as $value) {
            if (empty($authorization[$value])) {
                $errors[$value] = 'Это поле надо заполнить';
            }
            if (!empty($authorization['email']) && !$user) {
                $errors['email'] = 'Пользователь не существует';
                $errors['password'] = '';
            }
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
                'errors' => [],
                'classname' => '',
                'err_message' => '',
                'authorization' => ''
            ]);
        }
    }

    if (isset($_GET['logout'])) {
        unset($_SESSION['user']);
        header('Location: /index.php');
    }

    if(isset($_GET['id'])) {
        $id = $_GET['id'];
        if(isset($projects[$id])) {
            $tasks_in_category = filterByCategory($task_list, $projects[$id]);
        }
        else {
            http_response_code(404);
            die();
        }
    } else{
        $tasks_in_category = filterByStatus($task_list);
    }

    if(isset($_GET['add'])) {
        $body_class = 'overlay';
        $form_content = include_template('templates/form.php', ['projects' => $projects]);
    }

    $cookie_value = 1;
    if (isset($_GET['show_completed'])) {
        if (isset($_COOKIE['showcompl'])) {
            $cookie_value = toggle_value($_COOKIE['showcompl']);
        }
        setcookie('showcompl', $cookie_value, strtotime("+30 days"), "/");
        header('Location: /');
    }
    if (isset($_COOKIE['showcompl'])) {
        $show_complete_tasks = $_COOKIE['showcompl'];
        $tasks_in_category = ($_COOKIE['showcompl'] ? $task_list : filterByStatus($task_list));
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if(isset($_POST['task'])) {
            $new_task = $_POST;
            $errors = [];

            if (empty($_POST['task'])) {
                $errors['task'] = 'Это поле надо заполнить';
            }

            if (!in_array($_POST['category'], $projects)) {
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
                $new_task['status'] = false;
                //print_r($new_task['status']);
                array_unshift($tasks_in_category, $new_task);
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
        'task_list' => $tasks_in_category,
        'projects' => $projects,
        'body_class' => $body_class,
        'form_content' => $form_content,
        'guest' => $guest,
        'session' => $session,
        'auth_form' => $auth_form,
        'username' => $username
    ]);

    print($layout_content);

?>
