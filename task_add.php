<?php

session_start();

require_once 'constants.php';
require_once 'functions.php';

showGuestPage();

$dbConn = connectDb($host, $dbUserName, $dbUserPassw, $dbName);

$currentUserId = $_SESSION['userId'];
$currentUserData = getCurrentUserData($dbConn, $currentUserId);
$currentUserName = $currentUserData[0]['user_name'];

$activeProject = ['id' => ''];
$fieldsValues = createEmptyTaskFieldValuesArray();

if (isset($_GET['project_id'])) {
    $activeProject['id'] = intval($_GET['project_id']);
    if ($activeProject['id']) {

        $activeProject['aloneGetStr'] = '?project_id=' . $activeProject['id'];
        $activeProject['additionGetStr'] = '&project_id=' . $activeProject['id'];
    }
}

if (isset($_POST['submit'])) {
    $activeProject['id'] = intval($_POST['project']);
    if ($activeProject['id']) {
        $activeProject['aloneGetStr'] = '?project_id=' . $activeProject['id'];
        $activeProject['additionGetStr'] = '&project_id=' . $activeProject['id'];
    }

    $fieldsValues = checkTaskFields($dbConn, $currentUserId, $_POST, $_FILES);
    if (!$fieldsValues['errors']['errorFlag']) {
        $sql = getTaskInsertSql($currentUserId, $activeProject['id'], $fieldsValues['fieldValues']['name'], $fieldsValues['fieldValues']['date'], $fieldsValues['fieldValues']['file']);
        execSql($dbConn, $sql);
        header('Location: /index.php');
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

$pageTitle = "Дела в порядке - Добавление задачи";
$content = includeTemplate('task_add.php', ['projects' => $projects, 'activeProject' => $activeProject, 'fieldsValues'=> $fieldsValues]);
$htmlData = includeTemplate('layout.php', ['pageTitle' => $pageTitle, 'content' => $content, 'projects' => $projects, 'activeProject' => $activeProject, 'currentUserName' => $currentUserName]);
print($htmlData);
?>

