<?php
    require_once('functions.php');
    require_once('data.php');
    $show_complete_tasks = rand(0, 1);

    $displayed_array = [];
    if($show_complete_tasks == 0) {
        $displayed_array = $task_list;
    } else {
        $displayed_array = filterByStatus($task_list);
    }

    $page_content = include_template('templates/index.php', ['displayed_array' => $displayed_array, 'show_complete_tasks' => $show_complete_tasks]);
    $layout_content = include_template('templates/layout.php', ['content' => $page_content, 'title' => 'Дела в порядке', 'task_list' => $task_list, 'projects' => $projects]);

    print($layout_content);

?>
