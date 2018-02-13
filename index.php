<?php
    require_once('functions.php');
    require_once('data.php');

    $page_content = include_template('templates/index.php', ['task_list' => $task_list, 'show_complete_tasks' => $show_complete_tasks]);
    $layout_content = include_template('templates/layout.php', ['content' => $page_content, 'title' => 'Дела в порядке', 'task_list' => $task_list, 'projects' => $projects]);

    print($layout_content);
?>
