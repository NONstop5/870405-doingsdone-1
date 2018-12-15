<?php

/**
 * includeTemplate - Шаблонизация страниц сайта
 *
 * @param  string $name Имя файла-шаблона страницы
 * @param  array $data Данные для вставки в файл шаблона страницы
 *
 * @return result Возвращает контент страницы с данными
 */
function includeTemplate($name, $data)
{
    $name = 'templates/' . $name;
    $result = '';

    if (!file_exists($name)) {
        return $result;
    }

    ob_start();
    extract($data);
    include_once $name;

    $result = ob_get_clean();

    return $result;
}

/**
 * connectDb - Подключния к БД
 *
 * @param  string $host Имя хоста
 * @param  string $userName Имя пользователя БД
 * @param  string $userPassw Пароль пользователя БД
 * @param  string $dbName Имя БД
 *
 * @return result Подключение к БД
 */
function connectDb($host, $userName, $userPassw, $dbName)
{
    $result = mysqli_connect($host, $userName, $userPassw, $dbName);
    if ($result ===  false) {
        print("Ошибка: Невозможно подключиться к MySQL " . mysqli_connect_error());
        exit();
    }
    mysqli_set_charset($result, "utf8");
    return $result;
}

/**
 * redirectGuest - Переадресации гостей сайта
 *
 * @return void
 */
function redirectGuest()
{
    header('Location: /logout.php');
    exit();
}

/**
 * showGuestPage - Проверка сессии пользователя
 *
 * @return void
 */
function showGuestPage()
{
    if (empty($_SESSION['userId'])) {
        redirectGuest();
    }
}

/**
 * endSession - Очистка сессии пользователя
 *
 * @return void
 */
function endSession()
{
    session_start();

    if (!empty($_SESSION['userId'])) {
        $_SESSION = [];
    }
}

/**
 * getCurrentUserData - Получает данные текущего пользователя по id
 *
 * @param  mysqli_connect $dbConn Ссылка на соединение с БД
 * @param  int $userId Id текущего пользователя
 *
 * @return array Массив с данными пользователя
 */
function getCurrentUserData($dbConn, $userId)
{
    $sql = 'SELECT user_id, user_name FROM users WHERE user_id = ' . $userId;
    $users = getAssocArrayFromSQL($dbConn, $sql);

    return $users;
}

/**
 * getTaskFilterQuery - Формирует строку SQL-запроса для фильтрации отображения задач
 *
 * @param  array $get_array Массив параметров GET
 *
 * @return string Строка SQL-запроса
 */
function getTaskFilterQuery($get_array)
{
    $taskFilterQuery = '';

    if (intval($get_array['task_filter']) === 0) {
        $taskFilterQuery = '';
    }
    if (intval($get_array['task_filter']) === 1) {
        $taskFilterQuery = ' AND DATE(task_deadline) = DATE(NOW())';
    }
    if (intval($get_array['task_filter']) === 2) {
        $taskFilterQuery = ' AND DATE(task_deadline) = DATE_ADD(DATE(NOW()), INTERVAL 1 DAY)';
    }
    if (intval($get_array['task_filter']) === 3) {
        $taskFilterQuery = ' AND task_complete_status = 0 AND task_deadline < NOW()';
    }

    return $taskFilterQuery;
}

/**
 * generateGetParamForUrl - Генерации GET параметров строки запроса
 *
 * @param  array $paramList Массив параметров, для формиирования строки GET запроса
 *
 * @return string Возвращаемая строка GET запроса
 */
function generateGetParamForUrl($paramList)
{
    if (empty($paramList)) {
        return '';
    }
    $url = '?';
    foreach($paramList as $name => $value) {
        $paramValue = trim($value);
        if ($paramValue === '') { continue; }
        if (gettype($value) === "integer" && $value >= 0) {
            $url .= $name . '=' . $value . '&';
        }
    }
    $url = substr($url, 0, strlen($url) - 1);

    return $url;
}

/**
 * execSql - Обработка запроса к БД
 *
 * @param  mysql_connect $dbConn Ссылка на соединение с БД
 * @param  string $sql Строка SQL-запроса
 *
 * @return mysql_query Результат выполнения запроса к БД
 */
function execSql($dbConn, $sql)
{
    $result = mysqli_query($dbConn, $sql);
    if ($result === false) {
        print("Ошибка при выполнении запроса:" . mysqli_error($dbConn) . "<br>");
        print($sql);
        exit();
    }
    return $result;
}

/**
 * escapeSql - Экранирование значений для подстановки в SQL-запрос
 *
 * @param  mysql_connect $dbConn Ссылка на соединение с БД
 * @param  string $sqlStr Строка для экранирования
 *
 * @return string Экранированная строка
 */
function escapeSql($dbConn, $sqlStr) {
    return mysqli_real_escape_string($dbConn, $sqlStr);
}

/**
 * getAssocArrayFromSQL - Создание ассоциативного массива из запроса к БД
 *
 * @param  mysql_connect $dbConn Ссылка на соединение с БД
 * @param  string $sql Строка SQL-запроса
 *
 * @return array Ассоциативный массив
 */
function getAssocArrayFromSQL($dbConn, $sql)
{
    $result = mysqli_fetch_all(execSql($dbConn, $sql), MYSQLI_ASSOC);
    return $result;
}

/**
 * startSession - Старт сессии пользователя
 *
 * @param  string $userId Id пользователя
 *
 * @return void
 */
function startSession($userId)
{
    session_start();
    $_SESSION['userId'] = intval($userId);
}

/**
 * getNewUserInsertSql - Создает SQL запрос на добавление нового пользователя
 *
 * @param  string $userEmail Пользовательский e-mail
 * @param  string $userPassword Пароль пользователя
 * @param  string $userName Имя пользователя
 *
 * @return string Строка SQL-запроса
 */
function getNewUserInsertSql($userEmail, $userPassword, $userName)
{
    $sql = 'INSERT INTO users
            (
                user_name,
                user_email,
                user_password
            ) VALUES (
                \'' . $userName . '\',
                \'' . $userEmail . '\',
                \'' . password_hash($userPassword, PASSWORD_DEFAULT) . '\'
            )';
    return $sql;
}

/**
 * getProjectInsertSql - Создает SQL запрос на добавление новой задачи
 *
 * @param  int $currentUserId Id теекущего пользователя
 * @param  string $projectName Имя текущего пользователя
 *
 * @return string Строка SQL-запроса
 */
function getProjectInsertSql($currentUserId, $projectName)
{
    $sql = 'INSERT INTO projects
            (
                user_id,
                project_name
            ) VALUES (
                ' . $currentUserId . ',
                \'' . $projectName . '\'
            )';
    return $sql;
}

/**
 * getTaskInsertSql - Создает SQL запрос на добавление новой задачи
 *
 * @param  int $currentUserId Id текущего пользователя
 * @param  int $projectId Id проекта
 * @param  string $taskName Имя задачи
 * @param  timestamp $taskCompleteDate Дата выполнения задачи
 * @param  string $taskFile Файл вложения к задаче
 *
 * @return string Строка SQL-запроса
 */
function getTaskInsertSql($currentUserId, $projectId, $taskName, $taskCompleteDate, $taskFile)
{
    $quote = '\'';
    $taskCompleteDateSqlField = '';
    $taskCompleteDateSqlValue = '';
    $taskFileSqlField = '';
    $taskFileSqlValue = '';

    if (!empty($taskCompleteDate)) {
        $taskCompleteDateSqlField = ', task_deadline';
        $taskCompleteDateSqlValue = ', ' . $quote. convertDateToTimestampSqlFormat($taskCompleteDate) . $quote;
    }

    if (!empty($taskFile)) {
        $taskFileSqlField = ', task_file';
        $taskFileSqlValue = ', ' . $quote. $taskFile . $quote;
    }
    $sql = 'INSERT INTO tasks
            (
                user_id,
                project_id,
                task_name' .
                $taskCompleteDateSqlField .
                $taskFileSqlField . '
            ) VALUES (
                ' . $currentUserId . ',
                ' . $projectId . ',
                \'' . $taskName . '\'' .
                $taskCompleteDateSqlValue .
                $taskFileSqlValue . '
            )';
    return $sql;
}

/**
 * convertDateToTimestampSqlFormat - Конвертирует дату в формат даты Timestamp mySQL
 *
 * @param  string $dateStr Строковое значение даты
 *
 * @return timestamp Возвращаемое значение даты в timestamp
 */
function convertDateToTimestampSqlFormat($dateStr)
{
    return date('Y-m-d H:i:s', strtotime($dateStr));
}

/**
 * checkDateFormat - Проверка корректности формата даты
 *
 * @param  string $dateStr Строковое значение даты
 *
 * @return boolean Логическое значение
 */
function checkDateFormat($dateStr)
{
    $result = false;
    $pattern1 = '/^([0-9]{2})\.([0-9]{2})\.([0-9]{4})$/';
    $pattern2 = '/^([0-9]{4})\-([0-9]{2})\-([0-9]{2})$/';
    if (preg_match($pattern1, $dateStr, $matches)) {
        $result = checkdate($matches[2], $matches[1], $matches[3]);
    }
    if (preg_match($pattern2, $dateStr, $matches)) {
        $result = checkdate($matches[2], $matches[3], $matches[1]);
    }

    return $result;
}

/**
 * clearUserInputStr - Удаления спецсимволов и пробелов
 *
 * @param  string $dataStr Строковое значение даты
 *
 * @return string Строка без спецсимволов и пробелов
 */
function clearUserInputStr($dataStr)
{
    return substr(htmlspecialchars(trim($dataStr), ENT_QUOTES), 0, 49);
}

/**
 * setErrorsValues - Заполнение массива ошибок значениями
 *
 * @param  array $fieldValuesArray Массив полей
 * @param  string $fieldName Имя поля
 * @param  string $errorMessage Текст ошибки
 *
 * @return array Массив ошибок со значениями
 */
function setErrorsValues($fieldValuesArray, $fieldName, $errorMessage)
{
    $result = $fieldValuesArray;
    $result['errors']['errorFlag'] = 1;
    $result['errors']['errorGeneralMessage'] = '<p class="error-message">Пожалуйста, исправьте ошибки в форме</p>';
    $result['errors'][$fieldName]['errorClass'] = ' form__input--error';
    $result['errors'][$fieldName]['errorMessage'] = '<p class="form__message"><span class="form__message error-message">' . $errorMessage . '</span></p>';

    return $result;
}

/**
 * createEmptyNewUserFieldValuesArray - Создание пустого массива значений полей нового пользователя
 *
 * @return array Массив значений полей нового пользователя
 */
function createEmptyNewUserFieldValuesArray()
{
    $errorClassAndMessage = [
        'errorClass' => '',
        'errorMessage' => ''
    ];

    $result = [
        'fieldValues' => [
            'email' => '',
            'password' => '',
            'name' => ''
        ],
        'errors' => [
            'errorFlag' => 0,
            'errorGeneralMessage' => '',
            'email' => $errorClassAndMessage,
            'password' => $errorClassAndMessage,
            'name' => $errorClassAndMessage
        ]
    ];
    return $result;
}

/**
 * createEmptyExistingUserFieldValuesArray - Создание пустого массива значений полей существующего пользователя
 *
 * @return array Массив значений полей существующего пользователя
 */
function createEmptyExistingUserFieldValuesArray()
{
    $errorClassAndMessage = [
        'errorClass' => '',
        'errorMessage' => ''
    ];

    $result = [
        'fieldValues' => [
            'email' => '',
            'password' => ''
        ],
        'errors' => [
            'errorFlag' => 0,
            'errorGeneralMessage' => '',
            'email' => $errorClassAndMessage,
            'password' => $errorClassAndMessage
        ]
    ];
    return $result;
}

/**
 * createEmptyProjectFieldValuesArray - Создание пустого массива значений полей проекта
 *
 * @return array Массив значений полей проекта
 */
function createEmptyProjectFieldValuesArray()
{
    $errorClassAndMessage = [
        'errorClass' => '',
        'errorMessage' => ''
    ];

    $result = [
        'fieldValues' => [
            'name' => ''
        ],
        'errors' => [
            'errorFlag' => 0,
            'errorGeneralMessage' => '',
            'name' => $errorClassAndMessage,
        ]
    ];
    return $result;
}

/**
 * createEmptyTaskFieldValuesArray - Создание пустого массива значений полей задачи
 *
 * @return array Массив значений значений полей задачи
 */
function createEmptyTaskFieldValuesArray()
{
    $errorClassAndMessage = [
        'errorClass' => '',
        'errorMessage' => ''
    ];

    $result = [
        'fieldValues' => [
            'name' => '',
            'project' => 0,
            'date' => '',
            'file' => ''
        ],
        'errors' => [
            'errorFlag' => 0,
            'errorGeneralMessage' => '',
            'name' => $errorClassAndMessage,
            'project' => $errorClassAndMessage,
            'date' => $errorClassAndMessage
        ]
    ];
    return $result;
}

/**
 * checkNewUserFields - Проверка полей формы нового пользователя
 *
 * @param  mysqli_connect $dbConn Ссылка на соединение с БД
 * @param  array $postArray Массив значений полей формы
 *
 * @return array Массив значений пользователя
 */
function checkNewUserFields($dbConn, $postArray)
{
    $result = createEmptyNewUserFieldValuesArray();

    $postEmailStr = escapeSql($dbConn, clearUserInputStr($postArray['email']));
    $postPasswordStr = escapeSql($dbConn, clearUserInputStr($postArray['password']));
    $postNameStr = escapeSql($dbConn, clearUserInputStr($postArray['name']));

    $isCorrectEmail = filter_var($postEmailStr, FILTER_VALIDATE_EMAIL);

    $result['fieldValues']['email'] = $postEmailStr;
    $result['fieldValues']['password'] = $postPasswordStr;
    $result['fieldValues']['name'] = $postNameStr;

    if (empty($postEmailStr)) {
        $result = setErrorsValues($result, 'email', 'Введите корректное значение');
    } elseif (!$isCorrectEmail) {
        $result = setErrorsValues($result, 'email', 'E-mail введён некорректно');
    } else {
        $sql = 'SELECT user_id FROM users WHERE user_email = \'' . $postEmailStr . '\'';
        $users = execSql($dbConn, $sql);

        if (mysqli_num_rows($users)) {
            $result = setErrorsValues($result, 'email', 'Пользователь с таким e-mail уже существует');
        }
    }

    if (!empty($postPasswordStr)) {
        $result['fieldValues']['password'] = $postPasswordStr;
    } else {
        $result = setErrorsValues($result, 'password', 'Введите корректное значение');
    }

    if (!empty($postNameStr)) {
        $result['fieldValues']['name'] = $postNameStr;
    } else {
        $result = setErrorsValues($result, 'name', 'Введите корректное значение');
    }

    return $result;
}

/**
 * checkExistingUserFields - Проверка полей формы существующего пользователя
 *
 * @param  mysqli_connect $dbConn Ссылка на соединение с БД
 * @param  array $postArray Массив значений полей формы
 *
 * @return array Массив значений пользователя
 */
function checkExistingUserFields($dbConn, $postArray)
{
    $result = createEmptyExistingUserFieldValuesArray();

    $postEmailStr = escapeSql($dbConn, clearUserInputStr($postArray['email']));
    $postPasswordStr = escapeSql($dbConn, clearUserInputStr($postArray['password']));

    $isCorrectEmail = filter_var($postEmailStr, FILTER_VALIDATE_EMAIL);

    $result['fieldValues']['email'] = $postEmailStr;
    $result['fieldValues']['password'] = $postPasswordStr;

    if (empty($postEmailStr)) {
        $result = setErrorsValues($result, 'email', 'Введите корректное значение');
    } elseif (!$isCorrectEmail) {
        $result = setErrorsValues($result, 'email', 'E-mail введён некорректно');
    }

    if (empty($postPasswordStr)) {
        $result = setErrorsValues($result, 'password', 'Введите корректное значение');
    } else {
        $sql = 'SELECT user_id, user_name, user_password FROM users WHERE user_email = \'' . $postEmailStr . '\'';
        $users = getAssocArrayFromSQL($dbConn, $sql);

        if (count($users)) {
            if (password_verify($postPasswordStr, $users[0]['user_password'])) {
                startSession($users[0]['user_id']);
            } else {
                $result['errors']['errorFlag'] = 1;
                $result['errors']['errorGeneralMessage'] = '<p class="error-message">Вы ввели неверный email/пароль</p>';
            }
        } else {
            $result['errors']['errorFlag'] = 1;
            $result['errors']['errorGeneralMessage'] = '<p class="error-message">Вы ввели неверный email/пароль</p>';
        }
    }

    return $result;
}

/**
 * checkProjectFields - Проверка полей формы нового проекта
 *
 * @param  mysqli_connect $dbConn Ссылка на соединение с БД
 * @param  int $currentUserId Id текущего пользователя
 * @param  array $postArray Массив значений полей формы
 *
 * @return array Массив значений проекта
 */
function checkProjectFields($dbConn, $currentUserId, $postArray)
{
    $result = createEmptyProjectFieldValuesArray();

    $postNameStr = escapeSql($dbConn, clearUserInputStr($postArray['name']));

    $result['fieldValues']['name'] = $postNameStr;

    if (empty($postNameStr)) {
        $result = setErrorsValues($result, 'name', 'Введите корректное значение');
    } else {
        $sql = 'SELECT project_id FROM projects WHERE user_id = ' . $currentUserId . ' AND project_name = \'' . $postNameStr . '\'';
        $projects = execSql($dbConn, $sql);

        if (mysqli_num_rows($projects)) {
            $result = setErrorsValues($result, 'name', 'Проект с таким именем уже существует');
        }
    }

    return $result;
}

/**
 * checkTaskFields - Проверки полей формы новой задачи
 *
 * @param  mysqli_connect $dbConn Ссылка на соединение с БД
 * @param  int $currentUserId Id текущего пользователя
 * @param  array $postArray Массив значений полей формы
 * @param  array $filesArray Массив файлов формы
 *
 * @return array Массив значений задачи
 */
function checkTaskFields($dbConn, $currentUserId, $postArray, $filesArray)
{
    $result = createEmptyTaskFieldValuesArray();

    $postNameStr = escapeSql($dbConn, clearUserInputStr($postArray['name']));
    $postProjectStr = escapeSql($dbConn, clearUserInputStr($postArray['project']));
    $postDateStr = escapeSql($dbConn, clearUserInputStr($postArray['date']));

    $result['fieldValues']['name'] = $postNameStr;
    $result['fieldValues']['date'] = $postDateStr;

    if (empty($postNameStr)) {
        $result = setErrorsValues($result, 'name', 'Введите корректное значение');
    }

    if (empty($postProjectStr)) {
        $result = setErrorsValues($result, 'project', 'Введите корректное значение');
    } else {
        $sql = 'SELECT project_id FROM projects WHERE user_id = ' . escapeSql($dbConn, $currentUserId) . ' AND project_id = ' . $postProjectStr;
        $projects = execSql($dbConn, $sql);

        if (mysqli_num_rows($projects)) {
            $result['fieldValues']['project'] = $postProjectStr;
        } else {
            $result = setErrorsValues($result, 'project', 'Введите корректное значение');
        }
    }

    if (!empty($postDateStr)) {
        if (!checkDateFormat($postDateStr)) {
            $result = setErrorsValues($result, 'date', 'Введите корректное значение даты');
        }
    }

    if (!empty($filesArray)) {
        if (!$filesArray['preview']['error']) {
            $filePath = '/' . basename($filesArray['preview']['name']);
            if (move_uploaded_file($filesArray['preview']['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . $filePath)) {
                $result['fieldValues']['file'] = $filePath;
            }
        }
    }

    return $result;
}
