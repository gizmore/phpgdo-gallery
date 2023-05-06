<?php
declare(strict_types=1);
namespace GDO\Gallery;

use GDO\Core\GDO;
use GDO\Core\GDT_Template;
use GDO\File\GDO_File;
use GDO\File\GDO_FileTable;
use GDO\UI\GDT_Title;
use GDO\User\GDO_User;

/**
 * A table that maps Files to Galleries.
 * Required by GDT_Files.
 *
 * @version 7.0.3
 * @since 6.2.1
 * @author gizmore@wechall.net
 * @see GDO_FileTable
 * @see GDT_Files
 */
final class GDO_GalleryImage extends GDO_FileTable
{

	#################
	### FileTable ###
	#################
	public function gdoFileObjectTable(): ?GDO { return GDO_Gallery::table(); }

	###########
	### GDO ###
	###########

	public function isTestable(): bool
	{
		return false;
	}

	public function gdoColumns(): array
	{
		return array_merge(parent::gdoColumns(), [
			GDT_Title::make('files_description'),
		]);
	}

	##############
	### Getter ###
	##############
	public function getFile(): ?GDO_File { return $this->gdoValue('files_file'); }

	public function getCreator(): GDO_User { return $this->gdoValue('files_creator'); }

	public function renderCard(): string
	{
		return GDT_Template::php('Gallery', 'gallery_image_card.php', ['image' => $this]);
	}

	public function getGallery(): ?GDO_Gallery
	{
		return $this->gdoValue('files_object');
	}

	public function getGalleryID(): ?string { return $this->gdoVar('files_object'); }

	public function getDescription(): string { return $this->gdoVar('files_description'); }

	public function hasDescription(): bool { return !!$this->gdoVar('files_description'); }

	public function displayDate(): string { return tt($this->getCreated()); }

	public function getCreated(): string { return $this->gdoVar('files_created'); }

	public function displayDescription(): string { return $this->gdoColumn('files_description')->render(); }

	##############
	### Render ###
	##############
	public function href_show(): string { return href('Gallery', 'Image', "&id={$this->getFileID()}&variant=thumb"); }

	public function getFileID(): string { return $this->gdoVar('files_file'); }

	public function href_full(): string { return href('Gallery', 'Image', "&id={$this->getFileID()}&nodisposition=1"); }

}
