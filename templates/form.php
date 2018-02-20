<?
$classname = '';
$err_message = '';
?>
<div class="modal">
    <button class="modal__close" type="button" name="button">Закрыть</button>

    <h2 class="modal__heading">Добавление задачи</h2>

    <form class="form"  action="index.php" method="post">
        <div class="form__row">

            <?php if (isset($errors['name'])) {
                $classname = 'form__input--error';
                $err_message = '<p class="form_message">Заполните это поле</p>';
            } ?>
            <?=$err_message;?>

            <label class="form__label" for="name">Название <sup>*</sup></label>

            <input class="form__input <?=$classname?>" type="text" name="name" id="name" value="<?=(isset($new_task['name'])) ? $new_task['name'] : '';?>" placeholder="Введите название">
        </div>

        <div class="form__row">
            <?php if (isset($errors['project'])) {
                $classname = 'form__input--error';
                $err_message = '<p class="form_message">Заполните это поле</p>';
            } ?>
            <?=$err_message;?>

            <label class="form__label" for="project">Проект <sup>*</sup></label>

            <select class="form__input form__input--select <?=$classname?>" name="project" id="project">
                <!--<option value="">Входящие</option>-->
                <?php foreach ($projects as $key): ?>
                    <?php if ($key !== 'Все'): ?>
                        <option value="<?=$key;?>">
                    <?php endif; ?>
                    <?=$key;?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form__row">
            <label class="form__label" for="date">Дата выполнения</label>

            <input class="form__input form__input--date" type="date" name="date" id="date" value="" placeholder="Введите дату в формате ДД.ММ.ГГГГ">
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
            <input class="button" type="submit" name="" value="Добавить">
        </div>
    </form>
</div>
