<?php
namespace GDO\Gallery\Method;

use GDO\Core\GDO;
use GDO\Form\MethodCrud;
use GDO\Gallery\GDO_Gallery;
use GDO\User\GDO_User;
use GDO\Gallery\Module_Gallery;

final class Crud extends MethodCrud
{
	public function isGuestAllowed() : bool { return Module_Gallery::instance()->cfgGuestGalleries(); }
	
	public function hrefList() : string
	{
		return href('Gallery', 'GalleryList', '&user='.GDO_User::current()->getID());
	}

	public function gdoTable() : GDO
	{
		return GDO_Gallery::table();
	}

	public function getGallery() : GDO_Gallery
	{
		return $this->gdo;
	}
}
