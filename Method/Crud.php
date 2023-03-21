<?php
namespace GDO\Gallery\Method;

use GDO\Core\GDO;
use GDO\Form\MethodCrud;
use GDO\Gallery\GDO_Gallery;
use GDO\Gallery\Module_Gallery;
use GDO\User\GDO_User;

final class Crud extends MethodCrud
{

	public function isGuestAllowed(): bool { return Module_Gallery::instance()->cfgGuestGalleries(); }

	public function hrefList(): string
	{
		$uid = GDO_User::current()->getID();
		return href('Gallery', 'GalleryList', "&user={$uid}");
	}

	public function gdoTable(): GDO
	{
		return GDO_Gallery::table();
	}

	public function getGallery(): GDO_Gallery
	{
		return $this->gdo;
	}

}
