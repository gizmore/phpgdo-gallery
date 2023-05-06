<?php
declare(strict_types=1);
namespace GDO\Gallery;

use GDO\Core\GDO;
use GDO\Core\GDT;
use GDO\Core\GDT_AutoInc;
use GDO\Core\GDT_CreatedAt;
use GDO\Core\GDT_CreatedBy;
use GDO\Core\GDT_Template;
use GDO\DogShadowdogs\GDT_Action;
use GDO\File\GDT_ImageFiles;
use GDO\UI\GDT_Message;
use GDO\UI\GDT_Title;
use GDO\User\GDO_User;
use GDO\User\GDT_ACLRelation;

/**
 * A gallery is a collection of images with a title and description.
 *
 * @version 7.0.3
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

	public function canEdit(GDO_User $user): bool { return ($this->getCreatorID() === $user->getID()) || ($user->isStaff()); }

	public function getCreatorID(): string { return $this->gdoVar('gallery_creator'); }

	public function canView(GDO_User $user, &$reason): bool { return Module_Gallery::instance()->canSeeGallery($user, $this, $reason); }

	public function getCreator(): GDO_User { return $this->gdoValue('gallery_creator'); }

	public function getMessage(): ?string { return $this->gdoVar('gallery_description'); }

	public function displayDate(): ?string { return tt($this->getCreated()); }

	public function getCreated(): string { return $this->gdoVar('gallery_created'); }

	public function displayDescription(): string { return $this->gdoColumn('gallery_description')->render(); }

	public function renderTitle(): string { return html($this->getTitle()); }

	public function getTitle(): ?string { return $this->gdoVar('gallery_title'); }

	public function href_show(): string { return href('Gallery', 'Show', "&id={$this->getID()}"); }

	/**
	 * @return GDO_GalleryImage[]
	 */
	public function getImages(): array
	{
		$id = $this->getID();
		return $id ? GDO_GalleryImage::table()->select()->
		where("files_object={$id}")->
		exec()->fetchAllObjects() : GDT::EMPTY_ARRAY;
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
		$id = $this->getID();
		return $id ? GDO_GalleryImage::table()->countWhere("files_object={$this->getID()}") : 0;
	}

}
