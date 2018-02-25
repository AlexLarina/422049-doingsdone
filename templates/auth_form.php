<div class="modal">
    <button class="modal__close" type="button" name="button">Закрыть</button>

    <h2 class="modal__heading">Вход на сайт</h2>

    <form class="form" action="index.php" method="post">
        <div class="form__row">
            <?php if (isset($errors['email'])) {
                $classname = 'form__input--error';
                $err_message = '<p class="form_message">'.$errors['email'].'</p>';
            } ?>
            <?=$err_message;?>
            <label class="form__label" for="email">E-mail <sup>*</sup></label>

            <input class="form__input <?=$classname;?>" type="text" name="email" id="email" value="<?=(isset($authorization['email'])) ? $authorization['email'] : '';?>" placeholder="Введите e-mail">

            <p class="form__message">E-mail введён некорректно</p>
        </div>

        <div class="form__row">
            <?php if (isset($errors['password'])) {
                $classname = 'form__input--error';
                $err_message = '<p class="form_message">'.$errors['password'].'</p>';
            } ?>
            <?=$err_message;?>
            <label class="form__label" for="password">Пароль <sup>*</sup></label>

            <input class="form__input <?=$classname;?>" type="password" name="password" id="password" value="<?=(isset($authorization['password'])) ? $authorization['password'] : '';?>" placeholder="Введите пароль">
        </div>

        <div class="form__row form__row--controls">
            <input class="button" type="submit" name="" value="Войти">
        </div>
    </form>
</div>
