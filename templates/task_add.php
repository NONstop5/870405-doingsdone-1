      <main class="content__main">
        <h2 class="content__main-heading">Добавление задачи</h2>

        <form class="form" action="/task_add.php" method="post" enctype="multipart/form-data">
          <div class="form__row">
            <label class="form__label" for="name">Название <sup>*</sup></label>

            <input class="form__input<?= $fieldsValues['errors']['name']['errorClass'] ?>" type="text" name="name" id="name" value="<?= $fieldsValues['fieldValues']['name'] ?>" placeholder="Введите название" maxlength="50">
            <?= $fieldsValues['errors']['name']['errorMessage'] ?>
          </div>

          <div class="form__row">
            <label class="form__label" for="project">Проект <sup>*</sup></label>

            <select class="form__input form__input--select<?= $fieldsValues['errors']['project']['errorClass'] ?>" name="project" id="project">
            <?php
            foreach ($projects as $project) {
                $selected = '';
                if ($project['project_id'] == $activeProject['id']) {
                    $selected = ' selected';
                } ?>
              <option value="<?= $project['project_id'] ?>"<?= $selected ?>><?= $project['project_name'] ?></option>
            <?php
            }
            ?>
            </select>
            <?= $fieldsValues['errors']['project']['errorMessage'] ?>
          </div>

          <div class="form__row">
            <label class="form__label" for="date">Дата выполнения</label>

            <input class="form__input form__input--date<?= $fieldsValues['errors']['date']['errorClass'] ?>" type="date" name="date" id="date" value="<?= $fieldsValues['fieldValues']['date'] ?>" placeholder="Введите дату в формате ДД.ММ.ГГГГ" maxlength="10">
            <?= $fieldsValues['errors']['date']['errorMessage'] ?>
          </div>

          <div class="form__row">
            <label class="form__label" for="preview">Файл</label>

            <div class="form__input-file">
              <input class="visually-hidden" type="file" name="preview" id="preview" value="">

              <label class="button button--transparent" for="preview">
                <span>Выберите файл</span>
              </label>
            </div>
          </div>

          <div class="form__row form__row--controls">
            <?= $fieldsValues['errors']['errorGeneralMessage'] ?>
            <input class="button" type="submit" name="submit" value="Добавить">
          </div>
        </form>
      </main>
