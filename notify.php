<?php

require_once('init.php');
require_once('vendor/autoload.php');

$sql_remind_users = 'SELECT users.name, users.email, users.id FROM users
                     JOIN tasks on users.id = tasks.user_id 
                     WHERE tasks.dt_done IS NULL AND dt_deadline > NOW() AND 
                     dt_deadline = DATE_ADD(NOW(), INTERVAL 1 HOUR) GROUP BY users.id';

$sql_remind_users_result = mysqli_query($db_link, $sql_remind_users);
$users_result = mysqli_fetch_all($sql_remind_users_result, MYSQLI_ASSOC);

if($users_result) {

    $transport = new Swift_SmtpTransport('smtp.mail.ru', 465, 'ssl');
    $transport->setUsername('doingsdone@mail.ru ');
    $transport->setPassword('rds7BgcL');
    $message = new Swift_Message('Уведомление от сервиса «Дела в порядке»');
    $message->setFrom(['doingsdone@mail.ru' => 'Дела в порядке']);
    $mailer = new Swift_Mailer($transport);

    foreach($users_result as $key => $value) {
        $sql_remind_tasks = mysqli_query($db_link,
            "SELECT name, dt_deadline FROM tasks 
            WHERE dt_done IS NULL AND dt_deadline > NOW() AND 
            dt_deadline = DATE_ADD(NOW(), INTERVAL 1 HOUR) AND user_id = " . $value['id']);

        $remind_tasks = mysqli_fetch_all($sql_remind_tasks, MYSQLI_ASSOC);
        $remind_list = [];

        foreach($remind_tasks as $key => $task_value) {
            if(!$task_value['dt_deadline']){
                array_push($remind_list, $task_value['name'].'.<br/>');
            } else {
                array_push($remind_list,
                            $task_value['name'].' на '.date('d.m.Y', strtotime($task_value['dt_deadline'])).'.<br/>');
            }
        }

        $message_text = '';

        if(count($remind_list) == 1) {
            $message_text = 'Уважаемый '.$value['name'].'. У Вас запланирована задача:<br/>'.$remind_list[0];
        } else {
            $message_text = 'Уважаемый '.$value['name'].'. У Вас запланированы задачи:<br/>';
            foreach ($remind_list as $value) {
                $message_text = $message_text.$value;
            }
        }

        $message->setTo([$value['email'] => $value['name']]);
        $message->setBody($message_text, 'text/plain');
        $mailer->send($message);
    }
}

?>
