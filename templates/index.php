            <main class="content__main">
                <h2 class="content__main-heading">Список задач</h2>

                <form class="search-form" action="index.php" method="get">
                    <input class="search-form__input" type="text" name="search" value="" placeholder="Поиск по задачам">

                    <input class="search-form__submit" type="submit" name="submit" value="Искать">
                </form>

                <div class="tasks-controls">
                    <nav class="tasks-switch">
                        <a href="/index.php<?= generateGetParamForUrl(['task_filter'=> 0, 'project_id' => $activeProject['id']]) ?>" class="tasks-switch__item<?= $activeTaskFilter[0] ?>">Все задачи</a>
                        <a href="/index.php<?= generateGetParamForUrl(['task_filter'=> 1, 'project_id' => $activeProject['id']]) ?>" class="tasks-switch__item<?= $activeTaskFilter[1] ?>">Повестка дня</a>
                        <a href="/index.php<?= generateGetParamForUrl(['task_filter'=> 2, 'project_id' => $activeProject['id']]) ?>" class="tasks-switch__item<?= $activeTaskFilter[2] ?>">Завтра</a>
                        <a href="/index.php<?= generateGetParamForUrl(['task_filter'=> 3, 'project_id' => $activeProject['id']]) ?>" class="tasks-switch__item<?= $activeTaskFilter[3] ?>">Просроченные</a>
                    </nav>

                    <label class="checkbox">
                        <input class="checkbox__input visually-hidden show_completed" type="checkbox" <?php if ($showCompleteTasks) { print("checked"); } ?>>
                        <span class="checkbox__text">Показывать выполненные</span>
                    </label>
                </div>

                <table class="tasks">
                    <?php
                    date_default_timezone_set("Europe/Moscow");
                    foreach ($tasks as $task) {
                        $taskCompletedClass = "";
                        $taskImportantClass = "";
                        $taskCompleteStatus = "";

                        if (!is_null($task['task_deadline'])) {
                            $taskDate = strtotime($task['task_deadline']);
                            $timeToOver = floor(($taskDate - time()) / 3600);

                            if ($timeToOver <= 24) {
                                $taskImportantClass = " task--important";
                            }
                        }
                        if ($task['task_complete_status']) {
                            if (!$showCompleteTasks) {
                                continue;
                            }
                            $taskCompletedClass = " task--completed";
                            $taskImportantClass = "";
                            $taskCompleteStatus = "checked";
                        } ?>
                    <tr class="tasks__item task<?= $taskCompletedClass . $taskImportantClass ?>">
                        <td class="task__select">
                            <label class="checkbox task__checkbox">
                                <input class="checkbox__input visually-hidden task__checkbox" type="checkbox" name="task_complete" value="<?= $task['task_id'] ?>" <?= $taskCompleteStatus ?>>
                                <span class="checkbox__text"><?= $task['task_name'] ?></span>
                            </label>
                        </td>
                        <?php
                        if (empty($task['task_file'])) {
                        ?>
                        <td>&nbsp;</td>
                        <?php
                        } else {
                        ?>
                        <td class="task__file">
                            <a class="download-link" href="<?= $task['task_file'] ?>"><?= basename($task['task_file']) ?></a>
                        </td>
                        <?php
                        }
                        ?>
                        <td class="task__date"><?= $task['task_deadline'] ?></td>
                    </tr>
                    <?php
                    }
                    ?>
                </table>
            </main>
