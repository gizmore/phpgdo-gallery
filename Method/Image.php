<?php
namespace GDO\Gallery\Method;

use GDO\Core\Method;
use GDO\Gallery\GDO_GalleryImage;
use GDO\Util\Common;
use GDO\File\Method\GetFile;
use GDO\User\GDO_User;
use GDO\Core\GDT_String;

/**
 * Download a gallery image.
 * @author gizmore@wechall.net
 * @version 6.08
 * @since 6.04
 */
final class Image extends Method
{
    public function isSavingLastUrl() : bool { return false; }
    
    public function gdoParameters() : array
    {
    	return [
    		GDT_String::make('id')->notNull(),
    	];
    }
    
    public function execute()
	{
		$fileId = $this->gdoParameterVar('id');
		$image = GDO_GalleryImage::findBy('files_file', $fileId);
		$gallery = $image->getGallery();
		$reason = '';
		if (!$gallery->canView(GDO_User::current(), $reason))
		{
			return $this->error('err_not_allowed', [$reason]);
		}
		return GetFile::make()->executeWithId($fileId, Common::getRequestString('variant'));
	}
	
	public function getMethodTitle() : string
	{
		if ($fileId = $this->gdoParameterVar('id'))
		{
			if ($image = GDO_GalleryImage::getBy('files_file', $fileId))
			{
				if ($descr = $image->displayDescription())
				{
					return $descr;
				}
				return t('mt_gallery_image', [$image->getID()]);
			}
		}
		return t('mt_gallery_image', ['0']);
	}
	
}
