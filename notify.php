<?php

require_once 'vendor/autoload.php';
require_once 'constants.php';
require_once 'functions.php';

$dbConn = connectDb($host, $dbUserName, $dbUserPassw, $dbName);

$sql = 'SELECT user_name, user_email, task_name, task_deadline
        FROM users, tasks
        WHERE users.user_id = tasks.user_id
        AND task_complete_status = 0
        AND TIMEDIFF(task_deadline, NOW()) BETWEEN \'00:00:00\' AND \'01:00:00\'
        ORDER BY task_id DESC';
$tasksForNotify = getAssocArrayFromSQL($dbConn, $sql);

$smtpTransport = (new Swift_SmtpTransport('smtp.phpdemo.ru', 25))
    ->setUsername('keks@phpdemo.ru')
    ->setPassword('htmlacademy')
;
$mailer = new Swift_Mailer($smtpTransport);

foreach ($tasksForNotify as $task) {
    $messageText = 'Уважаемый, ' . $task['user_name'] . '. У вас запланирована задача ' . $task['task_name'] . ' на ' . $task['task_deadline'];

    $message = (new Swift_Message())
        ->setContentType('text/plain')
        ->setSubject('Уведомление от сервиса «Дела в порядке»')
        ->setFrom(['keks@phpdemo.ru' => 'Keks'])
        ->setTo([$task['user_email'] => $task['user_name']])
        ->setBody($messageText)
    ;

    $result = $mailer->send($message);
}

?>
