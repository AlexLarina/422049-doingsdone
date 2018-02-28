<?
$classname = '';
$err_message = '';
?>
<h2 class="content__main-heading">Регистрация аккаунта</h2>

<form class="form" action="register.php" method="post">
    <div class="form__row">
        <?php if (isset($errors['email'])) {
            $classname = 'form__input--error';
            $err_message = '<p class="form_message">'.$errors['email'].'</p>';
        } else {
            $classname = '';
            $err_message = '';
        } ?>
        <?=$err_message;?>
        <label class="form__label" for="email">E-mail <sup>*</sup></label>

        <input class="form__input <?=$classname;?>" type="text" name="email" id="email" value="<?=(isset($sign_up['email'])) ? $sign_up['email'] : '';?>" placeholder="Введите e-mail">

        <p class="form__message">E-mail введён некорректно</p>
    </div>

    <div class="form__row">
        <?php if (isset($errors['password'])) {
            $classname = 'form__input--error';
            $err_message = '<p class="form_message">'.$errors['password'].'</p>';
        } else {
            $classname = '';
            $err_message = '';
        } ?>
        <?=$err_message;?>
        <label class="form__label" for="password">Пароль <sup>*</sup></label>

        <input class="form__input <?=$classname;?>" type="password" name="password" id="password" value="<?=(isset($sign_up['password'])) ? $sign_up['password'] : '';?>" placeholder="Введите пароль">
    </div>

    <div class="form__row">
        <?php if (isset($errors['name'])) {
            $classname = 'form__input--error';
            $err_message = '<p class="form_message">'.$errors['name'].'</p>';
        } else {
            $classname = '';
            $err_message = '';
        } ?>
        <?=$err_message;?>
        <label class="form__label" for="name">Имя <sup>*</sup></label>

        <input class="form__input <?=$classname;?>" type="password" name="name" id="name" value="<?=(isset($sign_up['name'])) ? $sign_up['name'] : '';?>" placeholder="Введите пароль">
    </div>

    <div class="form__row form__row--controls">
        <p class="error-message">Пожалуйста, исправьте ошибки в форме</p>

        <input class="button" type="submit" name="signup" value="Зарегистрироваться">
    </div>
</form>
