
<?php

function findActive($route)
{
    if (str_contains(\Leantime\Core\Frontcontroller::getCurrentRoute(), $route)) {
        return "active";
    }
    return "";
}

findActive("");
?>

<div class="maincontentinner tabs">
    <ul>
        <li class="<?=findActive('Kanban'); ?>">
            <a href="<?=BASE_URL ?>/tickets/showKanban<?=$tpl->get('searchParams') ?>">
                <?=$tpl->__("links.kanban") ?>
            </a>
        </li>
        <li class="<?=findActive('showAll'); ?>">
            <a href="<?=BASE_URL ?>/tickets/showAll<?=$tpl->get('searchParams') ?>">
                <?=$tpl->__("links.table") ?>
            </a>
        </li>
        <li class="<?=findActive('showList'); ?>">
            <a href="<?=BASE_URL ?>/tickets/showList<?=$tpl->get('searchParams') ?>">
                <?=$tpl->__("links.list") ?>
            </a>
        </li>
        <li class="<?=findActive('roadmap'); ?>">
            <a href="<?=BASE_URL ?>/tickets/roadmap<?=$tpl->get('searchParams') ?>">
                <?=$tpl->__("links.timeline") ?>
            </a>
        </li>
        <li class="<?=findActive('Calendar'); ?>">
            <a href="<?=BASE_URL ?>/tickets/showProjectCalendar<?=$tpl->get('searchParams') ?>">
                <?=$tpl->__("links.calendar") ?>
            </a>
        </li>
    </ul>
</div>
