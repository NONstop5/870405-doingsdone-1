<?php

require_once 'constants.php';
require_once 'functions.php';

$dbConn = connectDb($host, $dbUserName, $dbUserPassw, $dbName);

$fieldsValues = createEmptyExistingUserFieldValuesArray();

if (isset($_POST['submit'])) {
    $fieldsValues = checkExistingUserFields($dbConn, $_POST);

    if (!$fieldsValues['errors']['errorFlag']) {
        header('Location: /');
    }
}

$pageTitle = 'Дела в порядке - Авторизация';
$content = includeTemplate('authorization.php', ['fieldsValues'=> $fieldsValues]);
$htmlData = includeTemplate('layout_guest.php', ['pageTitle' => $pageTitle, 'content' => $content]);
echo $htmlData;
