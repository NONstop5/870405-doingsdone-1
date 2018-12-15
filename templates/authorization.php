      <main class="content__main">
        <h2 class="content__main-heading">Вход на сайт</h2>

        <form class="form" action="authorization.php" method="post">
          <div class="form__row">
            <label class="form__label" for="email">E-mail <sup>*</sup></label>

            <input class="form__input<?= $fieldsValues['errors']['email']['errorClass'] ?>" type="text" name="email" id="email" value="<?= $fieldsValues['fieldValues']['email'] ?>" placeholder="Введите e-mail">
            <?= $fieldsValues['errors']['email']['errorMessage'] ?>
          </div>

          <div class="form__row">
            <label class="form__label" for="password">Пароль <sup>*</sup></label>

            <input class="form__input<?= $fieldsValues['errors']['password']['errorClass'] ?>" type="password" name="password" id="password" value="<?= $fieldsValues['fieldValues']['password'] ?>" placeholder="Введите пароль">
            <?= $fieldsValues['errors']['password']['errorMessage'] ?>
          </div>

          <div class="form__row form__row--controls">
          <?= $fieldsValues['errors']['errorGeneralMessage'] ?>
            <input class="button" type="submit" name="submit" value="Войти">
          </div>
        </form>

      </main>
