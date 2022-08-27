<?php
namespace GDO\Gallery\Method;

use GDO\Core\Method;
use GDO\Gallery\GDO_GalleryImage;
use GDO\Util\Common;
use GDO\File\Method\GetFile;
use GDO\User\GDO_User;
use GDO\Core\GDT_Object;

/**
 * Download a gallery image.
 * Checks view permission.
 * 
 * @author gizmore@wechall.net
 * @version 7.0.1
 * @since 6.4.0
 */
final class Image extends Method
{
    public function isSavingLastUrl() : bool { return false; }
    
    public function gdoParameters() : array
    {
    	return [
    		GDT_Object::make('id')->table(GDO_GalleryImage::table())->notNull(),
    	];
    }
    
    public function getImage() : GDO_GalleryImage
    {
    	return $this->gdoParameterValue('id');
    }
    
    public function execute()
	{
    	$image = $this->getImage();
		$gallery = $image->getGallery();
		$reason = '';
		if (!$gallery->canView(GDO_User::current(), $reason))
		{
			return $this->error('err_not_allowed', [$reason]);
		}
		return GetFile::make()->executeWithId($image->getID(), Common::getRequestString('variant'));
	}
	
	public function getMethodTitle() : string
	{
		return t('mt_gallery_image', ['0']);
// 		if ($fileId = $this->gdoParameterVar('id'))
// 		{
// 			if ($image = GDO_GalleryImage::getBy('files_file', $fileId))
// 			{
// 				if ($descr = $image->displayDescription())
// 				{
// 					return $descr;
// 				}
// 				return t('mt_gallery_image', [$image->getID()]);
// 			}
// 		}
	}
	
}
