<h2 class="content__main-heading">Список задач</h2>

<form class="search-form" action="index.html" method="post">
    <input class="search-form__input" type="text" name="" value="" placeholder="Поиск по задачам">

    <input class="search-form__submit" type="submit" name="" value="Искать">
</form>

<div class="tasks-controls">
    <nav class="tasks-switch">
        <a href="/" class="tasks-switch__item tasks-switch__item--active">Все задачи</a>
        <a href="index.php?agenda" class="tasks-switch__item">Повестка дня</a>
        <a href="index.php?tomorrow" class="tasks-switch__item">Завтра</a>
        <a href="index.php?overdue" class="tasks-switch__item">Просроченные</a>
    </nav>

    <label class="checkbox">
        <a href="index.php?show_completed">
            <!--добавить сюда аттрибут "checked", если переменная $show_complete_tasks равна единице-->
            <input class="checkbox__input visually-hidden" type="checkbox" <?=$show_complete_tasks ? 'checked' : '' ;?>>
            <span class="checkbox__text">Показывать выполненные</span>
        </a>
    </label>
</div>

<table class="tasks">
    <!--показывать следующий тег <tr/>, если переменная $show_complete_tasks равна единице-->
    <?php
    foreach ($task_list as $value): ?>
            <tr class="tasks__item task <? if($value['status']) { print("task--completed"); } ?>
            <? if(calcDays($value['date']) <= 1){ print("task--important"); } ?>">
                <td class="task__select">
                    <label class="checkbox task__checkbox">
                        <input class="checkbox__input visually-hidden" type="checkbox">
                        <a href="index.php?done=<?=$value['task_id']?>"><span class="checkbox__text"><?=htmlspecialchars($value['task'])?></span></a>
                    </label>
                </td>
                <td class="task__date"><?=htmlspecialchars($value['date'])?></td>
            </tr>
    <? endforeach; ?>
</table>
