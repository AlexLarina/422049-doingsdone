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

function filterByCategory($link, $project){
    $current_category_tasks = [];
    /*if($project == 'Все') {
        $current_category_tasks = $data;
    } else {
        foreach ($data as $key => $value) {
            if ($value['category'] == $project) {
                $current_category_tasks[$key] = $value;
            }
        }
    }*/
    /*$task_in_category_result = mysqli_query($link, 'SELECT * FROM tasks WHERE project_id = '.$projects[$id]['DB_id']);
    $task_in_category_list = mysqli_fetch_all($task_in_category_result, MYSQLI_ASSOC);

    $date = '';
    if($DBtask['dt_deadline'] == null) {
        $date = 'Нет';
    } else {
        $date = date('d.m.Y', strtotime($DBtask['dt_deadline']));
    }

    foreach ($task_in_category_list as $DBtask){
        $item = [
            'task' => $DBtask['name'],
            'date' => $date,
            'category' => $projects[$id]['name'],
            'status' => false,
        ];
        array_push($tasks_in_category, $item);
    }
    return $current_category_tasks;*/
};

function calcDays($task_date){
    $current_date_stamp = time();
    $task_date_stamp = strtotime($task_date);
    $seconds_in_day = 86400;
    $days = floor(($task_date_stamp - $current_date_stamp) / $seconds_in_day);

    return $days;
}
function toggle_value ($value) {
    return $value = ($value ? 0 : 1);
}
function searchUserByEmail($email, $users) {
    $result = null;
    foreach ($users as $user) {
        if ($user['email'] == $email) {
            $result = $user;
            break;
        }
    }

    return $result;
}

function searchUserByEmailInDB($link, $email) {
    $sql = "SELECT * FROM users WHERE email = '".$email."'";
    $result = mysqli_query($link, $sql);
    $user = mysqli_fetch_assoc($result);

    return $user;
}
?>
