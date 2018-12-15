<?php

require_once 'constants.php';
require_once 'functions.php';

$dbConn = connectDb($host, $dbUserName, $dbUserPassw, $dbName);

$fieldsValues = createEmptyNewUserFieldValuesArray();

if (isset($_POST['submit'])) {
    $fieldsValues = checkNewUserFields($dbConn, $_POST);

    if (!$fieldsValues['errors']['errorFlag']) {
        $sql = getNewUserInsertSql(
            $fieldsValues['fieldValues']['email'],
            $fieldsValues['fieldValues']['password'],
            $fieldsValues['fieldValues']['name']
        );
        execSql($dbConn, $sql);
        header('Location: /');
    }
}

$pageTitle = 'Дела в порядке - Регистрация пользователя';
$content = includeTemplate('register_user.php', ['fieldsValues'=> $fieldsValues]);
$htmlData = includeTemplate('layout_guest.php', ['pageTitle' => $pageTitle, 'content' => $content]);
echo $htmlData;
