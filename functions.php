<?php

function count_in_category($tasks, $category) {
    $task_in_category = 0;
    if($category == 'Все') {
        $task_in_category = count($tasks);
    }
    foreach ($tasks as $value){
        if ($value['category'] == $category){
            $task_in_category++;
        }
    }
    return $task_in_category;
};

function include_template($path, $data){

    if(file_exists($path)){
       ob_start();
       extract($data);
       $html_content = require_once($path);
       $html_content = ob_get_clean();
    } else {
        $html_content = '';
    }

    return $html_content;
};

function filterByStatus($data){
    $undone_tasks = [];
    foreach ($data as $key => $value){
        if($value['status'] == false){
            $undone_tasks[$key] = $value;
        }
    }
    return $undone_tasks;
};
?>
