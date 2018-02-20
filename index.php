<?php
    require_once('functions.php');
    require_once('data.php');
    $show_complete_tasks = rand(0, 1);
    $body_class = '';
    $form_content = null;

    if($show_complete_tasks) {
        $task_list = filterByStatus($task_list);
    }

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
    }
    if(isset($_GET['add'])) {
        $body_class = 'overlay';
        $form_content = include_template('templates/form.php', ['projects' => $projects]);
        //require_once('templates/form.php');
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $new_task = $_POST;

        $required = ['name', 'project'];
        $dict = ['name' => 'Название', 'project' => 'Проект'];
        $errors = [];
        foreach ($required as $key) {
            if (empty($_POST[$key])) {
                $errors[$key] = 'Это поле надо заполнить';
            }
        }

        if (isset($_FILES['preview']['name'])) {
            $tmp_name = $_FILES['preview']['tmp_name'];
            $path = $_FILES['preview']['name'];
            move_uploaded_file($tmp_name, '' . $path);
            $new_task['path'] = $path;
        }

        if (count($errors)) {
            $body_class = 'overlay';
            $form_content = include_template('templates/form.php', ['errors' => $errors, 'new_task' => $new_task, 'body_class' => $body_class,'projects' => $projects]);
            //require_once('templates/form.php');
            //$page_content = include_template('templates/form.php', ['new_task' => $new_task, 'errors' => $errors, 'dict' => $dict]);
        } else {
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
        'task_list' => $task_list,
        'projects' => $projects,
        'body_class' => $body_class,
        'form_content' => $form_content
    ]);

    print($layout_content);

?>
