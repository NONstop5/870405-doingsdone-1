        <main class="content__main">
          <h2 class="content__main-heading">Регистрация аккаунта</h2>

          <form class="form" action="/register.php" method="post">
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

            <div class="form__row">
              <label class="form__label" for="name">Имя <sup>*</sup></label>

              <input class="form__input<?= $fieldsValues['errors']['name']['errorClass'] ?>" type="text" name="name" id="name" value="<?= $fieldsValues['fieldValues']['name'] ?>" placeholder="Введите имя">
              <?= $fieldsValues['errors']['name']['errorMessage'] ?>
            </div>

            <div class="form__row form__row--controls">
                <?= $fieldsValues['errors']['errorGeneralMessage'] ?>
              <input class="button" type="submit" name="submit" value="Зарегистрироваться">
            </div>
          </form>
        </main>
