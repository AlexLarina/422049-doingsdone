<?php

/**
 * Creating template of html page
 * @param $path page path
 * @param $data page content
 * @return mixed|string
 */
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
/**
 * Calculating the number of days between current date and task's deadline
 * @param $task_date date of task
 * @return float
 */
function calcDays($task_date){
    $current_date_stamp = time();
    $task_date_stamp = strtotime($task_date);
    $seconds_in_day = 86400;
    $days = floor(($task_date_stamp - $current_date_stamp) / $seconds_in_day);

    return $days;
}

/**
 * Inverting value from 0 to 1 and conversely
 * @param $value 1 or 0
 * @return int
 */
function toggle_value ($value) {
    return $value = ($value ? 0 : 1);
}

/**
 * Searching in project database if registered email already exists in database.
 * If exists , returns user with appropriate email
 * @param $link database link
 * @param $email registered email
 * @return array|null
 */
function searchUserByEmailInDB($link, $email) {
    $sql = "SELECT * FROM users WHERE email = '".$email."'";
    $result = mysqli_query($link, $sql);
    $user = mysqli_fetch_assoc($result);

    return $user;
}

?>
