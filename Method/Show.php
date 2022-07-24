<?php
namespace GDO\Gallery\Method;

use GDO\Core\Method;
use GDO\Gallery\GDO_Gallery;
use GDO\Util\Common;
use GDO\Core\GDT_Object;

final class Show extends Method
{
	public function showInSitemap() : bool { return false; }
	
	public function getMethodTitle() : string
	{
		$gallery = $this->getGallery();
		return t('mt_gallery_show', [$gallery->gdoDisplay('gallery_title')]);
	}
	
	public function gdoParameters() : array
	{
	    return [
	        GDT_Object::make('id')->table(GDO_Gallery::table())->notNull(),
	    ];
	}
	
	public function getGallery() : GDO_Gallery
	{
		return $this->gdoParameterValue('id');
	}
	
	public function execute()
	{
		$gallery = GDO_Gallery::findById(Common::getRequestString('id'));
		return $this->templatePHP('gallery.php', ['gallery' => $gallery]);
	}
	
}
