<?php

session_start();

require_once 'constants.php';
require_once 'functions.php';

showGuestPage();

$dbConn = connectDb($host, $dbUserName, $dbUserPassw, $dbName);

$currentUserId = $_SESSION['userId'];
$currentUserData = getCurrentUserData($dbConn, $currentUserId);
$currentUserName = $currentUserData[0]['user_name'];

$projectFilterQuery = '';
$taskFilterQuery = '';
$taskSearchQuery = '';
$activeProject = [
    'id' => '',
    'aloneGetStr' => '',
    'additionGetStr' => ''
];
$activeTaskFilter = ['', '', '', ''];

if (isset($_GET['project_id'])) {
    $activeProject['id'] = intval($_GET['project_id']);
    $projectFilterQuery = ' AND project_id = ' . escapeSql($dbConn, $activeProject['id']);

    $sql = 'SELECT project_id FROM projects WHERE user_id = ' . $currentUserId . ' AND project_id = ' . escapeSql($dbConn, $activeProject['id']);
    $projects = execSql($dbConn, $sql);

    if (!mysqli_num_rows($projects)) {
        header('Location: ' . $_SERVER['REQUEST_URI'], true, 404);
        print('Страница не обнаружена!');
        exit();
    }
}

if (isset($_GET['show_completed'])) {
    $showCompleteTasks = intval($_GET['show_completed']);
} else {
    $showCompleteTasks = 0;
}

if (isset($_GET['task_id']) && isset($_GET['check'])) {
    $sql = 'UPDATE tasks SET task_complete_status = ' . escapeSql($dbConn, intval($_GET['check'])) . ' WHERE task_id = ' . escapeSql($dbConn, intval($_GET['task_id']));
    execSql($dbConn, $sql);
}

if (isset($_GET['task_filter'])) {
    $activeTaskFilter[intval($_GET['task_filter'])] = ' tasks-switch__item--active';
    $taskFilterQuery = getTaskFilterQuery($_GET);
}

if (isset($_GET['submit']) && isset($_GET['search'])) {
    $taskSearchStr = clearUserInputStr($_GET['search']);

    if (!empty($taskSearchStr)) {
        $taskSearchQuery = ' AND MATCH(task_name) AGAINST(\'' . escapeSql($dbConn, $taskSearchStr) . '\' IN BOOLEAN MODE)';
    }
}

$sql = 'SELECT projects.project_id, projects.project_name, COUNT(tasks.task_id) AS task_count
        FROM projects
        LEFT JOIN tasks
        ON projects.project_id = tasks.project_id
        AND tasks.task_complete_status = 0
        WHERE projects.user_id = ' . $currentUserId . '
        GROUP BY projects.project_id';
$projects = getAssocArrayFromSQL($dbConn, $sql);

$sql = 'SELECT task_id, task_name, DATE_FORMAT(task_deadline, \'%d.%m.%Y\') as task_deadline, task_complete_status, task_file
        FROM tasks
        WHERE user_id = ' . $currentUserId . $projectFilterQuery . $taskFilterQuery . $taskSearchQuery . ' ORDER BY task_id DESC';
$tasks = getAssocArrayFromSQL($dbConn, $sql);

$pageTitle = "Дела в порядке";
$content = includeTemplate('index.php', ["tasks" => $tasks, "showCompleteTasks" => $showCompleteTasks, "activeProject" => $activeProject, 'activeTaskFilter' => $activeTaskFilter]);
$htmlData = includeTemplate('layout.php', ["pageTitle" => $pageTitle, "content" => $content, "projects" => $projects, "tasks" => $tasks, 'currentUserName' => $currentUserName, "activeProject" => $activeProject]);
print($htmlData);
