<?
$classname = '';
$err_message = '';
?>
<div class="modal">
    <button class="modal__close" type="button" name="button">Закрыть</button>

    <h2 class="modal__heading">Добавление задачи</h2>
    <?//foreach ($projects as $key => $value):
        //if($new_task['category'] == $value):?>
    <form class="form" action="index.php" method="post">
        <?//endif; endforeach;?>
        <div class="form__row">
            <?php if (isset($errors['task'])) {
                $classname = 'form__input--error';
                $err_message = '<p class="form_message">'.$errors['task'].'</p>';
            } ?>
            <?=$err_message;?>

            <label class="form__label" for="name">Название <sup>*</sup></label>

            <input class="form__input <?=$classname?>" type="text" name="task" id="name" value="<?=(isset($new_task['task'])) ? $new_task['task'] : '';?>" placeholder="Введите название">
        </div>

        <div class="form__row">
            <?php if (isset($errors['category'])) {
                $classname = 'form__input--error';
                $err_message = '<p class="form_message">'.$errors['category'].'</p>';
            } ?>
            <?=$err_message;?>

            <label class="form__label" for="project">Проект <sup>*</sup></label>

            <select class="form__input form__input--select <?=$classname?>" name="category" id="project">
                <?php foreach ($projects as $key): ?>
                    <?php if ($key !== 'Все'): ?>
                        <option <?if ($key == 'Входящие') : ?><?='selected'?><?endif;?>
                        value="<?=$key;?>">
                    <?php endif; ?>
                    <?=$key;?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form__row">
            <?php if (isset($errors['date'])) {
                $classname = 'form__input--error';
                $err_message = '<p class="form_message">'.$errors['date'].'</p>';
            } ?>
            <?=$err_message;?>

            <label class="form__label" for="date">Дата выполнения</label>
            <input class="form__input form__input--date <?=$classname?>" type="date" name="date" id="date" value="
            <?if(!isset($new_task['date'])){
                $new_task['date'] = 'Нет';
            }?>
            <?=$new_task['date']?>" placeholder="Введите дату в формате ДД.ММ.ГГГГ">
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
