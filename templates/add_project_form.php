<div class="modal">
    <button class="modal__close" type="button" name="button">Закрыть</button>

    <h2 class="modal__heading">Добавление проекта</h2>

    <form class="form"  action="index.php" method="post">
        <div class="form__row">
            <? if (isset($errors['name'])) {
                $classname = 'form__input--error';
                $err_message = '<p class="form_message">'.$errors['name'].'</p>';
            } else {
                $classname = '';
                $err_message = '';
            }?>
            <?=$err_message;?>
            <label class="form__label" for="project_name">Название <sup>*</sup></label>

            <input class="form__input <?=$classname?>" type="text" name="name" id="project_name" value="<?=(isset($new_project['name'])) ? $new_project['name'] : '';?>" placeholder="Введите название проекта">
        </div>

        <div class="form__row form__row--controls">
            <input class="button" type="submit" name="add_project" value="Добавить">
        </div>
    </form>
</div>
