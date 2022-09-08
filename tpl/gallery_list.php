<?php
use GDO\Gallery\GDO_Gallery;
use GDO\UI\GDT_EditButton;
use GDO\Table\GDT_ListItem;
use GDO\UI\GDT_Button;
use GDO\User\GDO_User;
use GDO\UI\GDT_Label;
/**
 * @var $gallery GDO_Gallery
 */
$gallery instanceof GDO_Gallery;

$li = GDT_ListItem::make()->gdo($gallery);

$li->creatorHeader();

$li->titleRaw($gallery->renderTitle());
$li->subtitle('gallery_li2', [
	$gallery->getImageCount(),
	$gallery->getCreator()->renderUserName(),
	$gallery->displayDate()]);
$li->content($gallery->gdoColumn('gallery_description'));

$actions = $li->actions();
if ($gallery->canEdit(GDO_User::current()))
{
	$actions->addField(GDT_EditButton::make()->href(href('Gallery', 'Crud', "&id={$gallery->getID()}")));
}
$actions->addField(GDT_Button::make()->href($gallery->href_show())->icon('view')->label('btn_view'));
echo $li->render();
