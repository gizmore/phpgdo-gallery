<?php
namespace GDO\Gallery\Method;

use GDO\Core\Method;
use GDO\Gallery\GDO_Gallery;
use GDO\Util\Common;
use GDO\Core\GDT_Object;

final class Show extends Method
{
	public function gdoParameters() : array
	{
	    return array_merge(parent::gdoParameters(), [
	        GDT_Object::make('id')->table(GDO_Gallery::table())->notNull(),
	    ]);
	}
	
	public function execute()
	{
		$gallery = GDO_Gallery::findById(Common::getRequestString('id'));
		return $this->templatePHP('gallery.php', ['gallery' => $gallery]);
	}
	
}
