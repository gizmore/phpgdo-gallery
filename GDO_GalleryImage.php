<?php
namespace GDO\Gallery;

use GDO\File\GDO_File;
use GDO\Core\GDT_Template;
use GDO\User\GDO_User;
use GDO\File\GDO_FileTable;
use GDO\UI\GDT_Title;

/**
 * A table that maps Files to Galleries.
 * Required by GDT_Files.
 *  
 * @author gizmore@wechall.net
 * @since 7.0.1
 * @version 6.8.0
 * @see GDO_FileTable
 * @see GDT_Files
 */
final class GDO_GalleryImage extends GDO_FileTable
{
	#################
	### FileTable ###
	#################
	public function gdoFileObjectTable() { return GDO_Gallery::table(); }

	###########
	### GDO ###
	###########
	public function gdoColumns() : array
	{
		return array_merge(parent::gdoColumns(), [
			GDT_Title::make('files_description'),
		]);
	}

	##############
	### Getter ###
	##############
	public function getFile() : GDO_File { return $this->gdoValue('files_file'); }
	public function getFileID() : string { return $this->gdoVar('files_file'); }
	public function getGallery() : GDO_Gallery { return $this->gdoValue('files_object'); }
	public function getGalleryID() : string { return $this->gdoVar('files_object'); }
	public function getCreator() : GDO_User { return $this->gdoValue('files_creator'); }
	public function getCreated() { return $this->gdoVar('files_created'); }
	public function getDescription() { return $this->gdoVar('files_description'); }
	public function hasDescription() { return !!$this->gdoVar('files_description'); }
	
	public function displayDate() { return tt($this->getCreated()); }
	public function displayDescription() { return $this->gdoColumn('files_description')->renderCell(); }
	
	##############
	### Render ###
	##############
	public function href_show() { return href('Gallery', 'Image', "&id={$this->getFileID()}&variant=thumb"); }
	public function href_full() { return href('Gallery', 'Image', "&id={$this->getFileID()}&nodisposition=1"); }
	
	public function renderCard() : string
	{
		return GDT_Template::php('Gallery', 'card/gallery_image.php', ['image' => $this]);
	}
	
}
