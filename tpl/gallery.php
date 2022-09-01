<?php
use GDO\Gallery\GDO_GalleryImage;
use GDO\Table\GDT_List;
use GDO\UI\GDT_Bar;
use GDO\UI\GDT_Button;
use GDO\User\GDO_User;
use GDO\Table\GDT_ListCard;

/** @var $gallery \GDO\Gallery\GDO_Gallery **/
$user = GDO_User::current();

$bar = GDT_Bar::make();
if ($gallery->canEdit($user))
{
	$button = GDT_Button::make()->icon('edit')->label('btn_edit')->href(href('Gallery', 'Crud', "&id={$gallery->getID()}"));
	$bar->addField($button);
}
echo $bar->renderHTML();

$images = GDO_GalleryImage::table();
$query = $images->select('*')->where("files_object={$gallery->getID()}")->joinObject('files_file');
$list = GDT_ListCard::make();
// $list->setupHeaders(false, true);
$list->query($query);
$list->countQuery($query->copy()->selectOnly('COUNT(*)'));
$list->paginateDefault();
$pagemenu = $list->pagemenu;
$pagemenu->paginateQuery($query);

echo $list->render();
