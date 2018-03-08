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
 * @return int
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
    $email = mysqli_real_escape_string($link, $email);

    $sql = "SELECT * FROM users WHERE email = '".$email."'";
    $result = mysqli_query($link, $sql);
    $user = mysqli_fetch_assoc($result);

    return $user;
}

/**
 * Returns array of user's projects from database
 * @param $user_id  id of current user
 * @param $cookie
 * @param $link database link
 * @return array
 */
function get_projects ($user_id, $link) {

    $sql_projects_name = 'SELECT * FROM projects WHERE user_id = '.$user_id;

    $sql_projects_num = 'SELECT project_id, COUNT(project_id) FROM tasks
                                 WHERE dt_done is NULL AND user_id = '.$user_id.'
                                 GROUP BY project_id;';

    $sql_projects_name_result = mysqli_query($link, $sql_projects_name);
    $projects_name = mysqli_fetch_all($sql_projects_name_result, MYSQLI_ASSOC);

    $sql_projects_num_result = mysqli_query($link, $sql_projects_num);
    $projects_id_num = mysqli_fetch_all($sql_projects_num_result, MYSQLI_ASSOC);

    $projects = [];
    $project_names = [];
    $total_task_number = 0;

    foreach ($projects_name as $key1 => $value1){
        $project = [
            'id' => $value1['id'],
            'name' => $value1['name'],
            'task_number' => 0,
        ];
        foreach ($projects_id_num as $key2 => $value2){
            if($value1['id'] == $value2['project_id']) {
                $project['task_number'] = $value2['COUNT(project_id)'];
                $total_task_number = $total_task_number + $value2['COUNT(project_id)'];
            }
        }
        if($value1['name'] != 'Все') {
            array_push($projects, $project);
        }
        array_push($project_names, $value1['name']);
    }
    array_unshift($projects, [
        'id' => null,
        'name' => 'Все',
        'task_number' => $total_task_number
    ]);

    return $projects;
}

/**
 * Returns array of projects' names necessary for new task form validaion
 * @param $projects array of user's projects
 * @return array
 */
function get_projects_names ($projects) {
    $project_names = [];
    foreach ($projects as $key => $value){
        array_push($project_names, $value['name']);
    }
    return $project_names;
}
/**
 * Returns array of user's tasks depending on category and filter status
 * @param $link database link
 * @param $user_id
 * @param $cookie
 * @param $projects array of user's projects
 * @param $id project id
 * @param $filter
 * @return array
 */
function get_tasks ($link, $user_id, $cookie, $projects, $id, $filter) {

    $sql_tasks = 'SELECT * FROM tasks WHERE tasks.user_id = '.$user_id;

    if(!$cookie) {
        $sql_tasks = $sql_tasks.' AND dt_done is NULL';
    }

    if(isset($id)) {
        if($id != 'all') {
            $sql_tasks = $sql_tasks . ' AND project_id = ' . $projects[$id]['id'];
        }
    }

    if(isset($filter)) {
        switch ($filter) {
            case 'all':
                $sql_tasks = $sql_tasks;
                break;
            case 'agenda':
                $sql_tasks = $sql_tasks . ' AND dt_deadline = CURDATE()';
                break;
            case 'tomorrow':
                $sql_tasks = $sql_tasks . ' AND dt_deadline > NOW() AND dt_deadline <= DATE_ADD(NOW(), INTERVAL 1 DAY)';
                break;
            case 'overdue':
                $sql_tasks = $sql_tasks . ' AND dt_deadline < CURDATE()';
                break;
        }
    }

    $task_result = mysqli_query($link, $sql_tasks);

    $task_list = mysqli_fetch_all($task_result, MYSQLI_ASSOC);

    return $task_list;
}
/**
 * Returns result of search query to database via fulltext search
 * @param $link database link
 * @param $search search query
 * @param $user_id
 * @return array
 */
function search_tasks ($link, $search, $user_id) {
    $sql_search_tasks = 'SELECT * FROM tasks WHERE MATCH(name) AGAINST(?) AND tasks.user_id = '.$user_id;
    $stmt = db_get_prepare_stmt($link, $sql_search_tasks, [$search]);
    mysqli_stmt_execute($stmt);
    $search_task_result = mysqli_stmt_get_result($stmt);
    return $search_task_list = mysqli_fetch_all($search_task_result, MYSQLI_ASSOC);
}

/**
 * Inserting new user's task from adding form to database
 * @param $link database link
 * @param $new_task new task's parameters from $_GET array
 * @param $date task's deadline date
 * @param $projects array of user's projects
 * @param $key key of choosen project in projects array
 * @return bool
 */
function insert_task ($link, $new_task, $date, $projects, $key) {

    $sql = 'INSERT INTO tasks (dt_add, name, file_path, dt_deadline, user_id, project_id)
                    VALUES(NOW(), ?, ?, ?, ?, ?)';

    $stmt = db_get_prepare_stmt($link, $sql, [
        $new_task['task'],
        $new_task['preview'],
        $date,
        $_SESSION['user']['id'],
        $projects[$key]['id']
    ]);

    $result = mysqli_stmt_execute($stmt);

    return $result;
}

/**
 * Updates task's status from 'undone' to 'done'
 * @param $link database link
 * @param $task_id task's id in database
 * @return bool
 */
function update_task_status($link, $task_id) {
    $sql_set_done = "UPDATE tasks SET dt_done = NOW() WHERE id = ?";
    $stmt = db_get_prepare_stmt($link, $sql_set_done, [$task_id]);
    $result = mysqli_stmt_execute($stmt);

    return $result;
}
?>
