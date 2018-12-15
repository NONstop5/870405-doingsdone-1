<?php

require_once 'constants.php';
require_once 'functions.php';

endSession();

$pageTitle = "Дела в порядке - Добро пожаловать";
$htmlData = includeTemplate('guest.php', ['pageTitle' => $pageTitle]);
print($htmlData);
