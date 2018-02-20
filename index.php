<?php
    require_once('functions.php');
    require_once('data.php');
    $show_complete_tasks = rand(0, 1);

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
            die('Unable to connect');
        }
    }

    $page_content = include_template('templates/index.php', ['task_list' => $tasks_in_category, 'show_complete_tasks' => $show_complete_tasks]);
    $layout_content = include_template('templates/layout.php', ['content' => $page_content, 'title' => 'Дела в порядке', 'task_list' => $task_list, 'projects' => $projects]);

    print($layout_content);

?>
