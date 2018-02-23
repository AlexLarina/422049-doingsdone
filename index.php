<?php
    require_once('functions.php');
    require_once('data.php');
    //$show_complete_tasks = rand(0, 1);
    $show_complete_tasks = 0;
    $body_class = '';
    $form_content = null;

    $tasks_in_category = [];

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
        //$tasks_in_category = $task_list;
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
        $new_task = $_POST;
        $dict = ['name' => 'Название', 'project' => 'Проект'];
        $errors = [];

        if (empty($_POST['task'])) {
            $errors['task'] = 'Это поле надо заполнить';
        }

        if(!in_array($_POST['category'], $projects)) {
            $errors['category'] = 'Выберите из предложенного';
        }

        $current_date  = date('d.m.Y');
        if($current_date > date('d.m.Y', strtotime($new_task['date']))) {
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
            print_r($new_task['status']);
            array_unshift($tasks_in_category, $new_task);
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
        'form_content' => $form_content
    ]);

    print($layout_content);

?>
