<?php
namespace GDO\Gallery;

use GDO\Core\GDO;
use GDO\Core\GDT_AutoInc;
use GDO\Core\GDT_CreatedAt;
use GDO\Core\GDT_CreatedBy;
use GDO\Core\GDT_Template;
use GDO\File\GDT_ImageFiles;
use GDO\UI\GDT_Message;
use GDO\UI\GDT_Title;
use GDO\User\GDO_User;
use GDO\User\GDT_ACLRelation;

/**
 * A gallery is a collection of images with a title and description.
 *
 * @version 7.0.1
 * @since 6.2.0
 * @author gizmore@wechall.net
 * @see GDT_ImageFiles
 */
final class GDO_Gallery extends GDO
{

	public function gdoCached(): bool { return false; }

	public function gdoColumns(): array
	{
		return [
			GDT_AutoInc::make('gallery_id'),
			GDT_Title::make('gallery_title')->label('title')->notNull()->initial(t('gallery_title_suggestion', [GDO_User::current()->renderUserName()])),
			GDT_Message::make('gallery_description')->label('description'),
			GDT_CreatedBy::make('gallery_creator'),
			GDT_CreatedAt::make('gallery_created'),
			GDT_ACLRelation::make('gallery_acl')->label('cfg_gallery_acl'),
			GDT_ImageFiles::make('gallery_files')->maxfiles(100)->
			scaledVersion('thumb', 320, 240)->
			fileTable(GDO_GalleryImage::table())->
			previewHREF(href('Gallery', 'Image', '&variant=thumb&id={id}')),
			# @TODO: You could put a virtual here for a subselect
		];
	}

	public function renderList(): string
	{
		return GDT_Template::php('Gallery', 'gallery_list.php', [
			'gallery' => $this]);
	}

	public function aclColumn(): GDT_ACLRelation { return $this->gdoColumn('gallery_acl'); }

	public function canEdit(GDO_User $user) { return ($this->getCreatorID() === $user->getID()) || ($user->isStaff()); }

	public function getCreatorID(): string { return $this->gdoVar('gallery_creator'); }

	public function canView(GDO_User $user, &$reason) { return Module_Gallery::instance()->canSeeGallery($user, $this, $reason); }

	public function getCreator(): GDO_User { return $this->gdoValue('gallery_creator'); }

	public function getMessage() { return $this->gdoVar('gallery_description'); }

	public function displayDate() { return tt($this->getCreated()); }

	public function getCreated() { return $this->gdoVar('gallery_created'); }

	public function displayDescription() { return $this->gdoColumn('gallery_description')->render(); }

	public function renderTitle(): string { return html($this->getTitle()); }

	public function getTitle() { return $this->gdoVar('gallery_title'); }

	public function href_show() { return href('Gallery', 'Show', "&id={$this->getID()}"); }

	/**
	 * @return GDO_GalleryImage[]
	 */
	public function getImages(): array
	{
		return GDO_GalleryImage::table()->select()->
		where("files_object={$this->getID()}")->
		exec()->fetchAllObjects();
	}

	public function getFiles(): array
	{
		return $this->gdoValue('gallery_images');
	}

	public function getImageCount(): int
	{
		return $this->queryImageCount();
	}

	public function queryImageCount(): int
	{
		return GDO_GalleryImage::table()->countWhere("files_object={$this->getID()}");
	}

}
