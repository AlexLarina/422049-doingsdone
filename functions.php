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

function renderTasks ($link, $sql_result) {
    $DB_tasks = mysqli_fetch_all($sql_result, MYSQLI_ASSOC);
    $tasks = [];
    foreach ($DB_tasks as $DBtask){
        $project_DB_name = mysqli_query($link, 'SELECT name FROM projects WHERE id = '.$DBtask['project_id']);
        $project_name = mysqli_fetch_assoc($project_DB_name);

        $date = '';
        $status = '';
        if($DBtask['dt_deadline'] == null) {
            $date = 'Нет';
        } else {
            $date = date('d.m.Y', strtotime($DBtask['dt_deadline']));
            $status = (strtotime($DBtask['dt_deadline']) < time()) ? true : false;
        }

        $task = [
            'task' => $DBtask['name'],
            'date' => $date,
            'category' => $project_name['name'],
            'status' => $status,
            'task_id' => $DBtask['id'],
            'done' => $DBtask['dt_done']
        ];
        array_push($tasks, $task);
    }

    return $tasks;
}
?>
