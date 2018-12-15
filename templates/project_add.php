    <main class="content__main">
        <h2 class="content__main-heading">Добавление проекта</h2>

        <form class="form" action="project_add.php" method="post">
          <div class="form__row">
            <label class="form__label" for="project_name">Название <sup>*</sup></label>

            <input class="form__input<?= $fieldsValues['errors']['name']['errorClass'] ?>" type="text" name="name" id="project_name" value="<?= $fieldsValues['fieldValues']['name'] ?>" placeholder="Введите название проекта" maxlength="50">
            <?= $fieldsValues['errors']['name']['errorMessage'] ?>
          </div>

          <div class="form__row form__row--controls">
          <?= $fieldsValues['errors']['errorGeneralMessage'] ?>
            <input class="button" type="submit" name="submit" value="Добавить">
          </div>
        </form>
      </main>
