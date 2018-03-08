<h2 class="content__main-heading">Список задач</h2>

<form class="search-form" action="index.php" method="get">
    <input class="search-form__input" type="text" name="search" value="" placeholder="Поиск по задачам">

    <input class="search-form__submit" type="submit" name="" value="Искать">
</form>

<div class="tasks-controls">
    <nav class="tasks-switch">
        <a href="index.php?<?=(isset($id)) ? 'id='.$id : 'all';?>&filter=all"
           class="tasks-switch__item <?=($filter == 'all') || (!$filter) ? 'tasks-switch__item--active' : '';?>">Все задачи</a>
        <a href="index.php?<?=(isset($id)) ? 'id='.$id : 'all';?>&filter=agenda"
           class="tasks-switch__item <?=($filter== 'agenda') ? 'tasks-switch__item--active': '';?>">Повестка дня</a>
        <a href="index.php?<?=(isset($id)) ? 'id='.$id : 'all';?>&filter=tomorrow"
           class="tasks-switch__item <?=($filter == 'tomorrow') ? 'tasks-switch__item--active': '';?>">Завтра</a>
        <a href="index.php?<?=(isset($id)) ? 'id='.$id : 'all';?>&filter=overdue"
           class="tasks-switch__item <?=($filter == 'overdue') ? 'tasks-switch__item--active': '';?>">Просроченные</a>
    </nav>

    <label class="checkbox">
        <a href="index.php?<?=(isset($id)) ? 'id='.$id.'&' : '&';?><?=(isset($filter)) ? 'filter='.$filter.'&' : 'filter=all&';?>show_completed">
            <input class="checkbox__input visually-hidden" type="checkbox" <?=$show_complete_tasks ? 'checked' : '' ;?>>
            <span class="checkbox__text">Показывать выполненные</span>
        </a>
    </label>
</div>

<table class="tasks">
    <?=$search_error;?>
    <? foreach ($task_list as $value): ?>
            <tr class="tasks__item task <? if($value['dt_done'] != null) { print("task--completed"); }  ?>
            <? if (abs(calcDays($value['dt_deadline'])) <= 1){ print("task--important"); } ?>">
                <td class="task__select">
                    <label class="checkbox task__checkbox">
                        <input class="checkbox__input visually-hidden" type="checkbox">
                        <a href="index.php?done=<?=$value['id']?>"><span class="checkbox__text">
                                <?=htmlspecialchars($value['name'])?></span>
                        </a>
                    </label>
                </td>
                <td class="task__date"><?=(strtotime($value['dt_deadline']) == $time_zone_stamp || $value['dt_deadline'] == null) ? 'Нет' : htmlspecialchars(date('d.m.Y', strtotime($value['dt_deadline'])))?></td>
            </tr>
    <? endforeach; ?>
</table>
