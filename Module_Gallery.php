<?php
namespace GDO\Gallery;

use GDO\Core\GDO;
use GDO\Core\GDO_Module;
use GDO\Core\GDT_Checkbox;
use GDO\UI\GDT_Link;
use GDO\UI\GDT_Page;
use GDO\User\GDO_User;
use GDO\User\GDT_ACLRelation;

/**
 * Image galleries.
 *
 * @version 7.0.1
 * @since 6.7.0
 * @author gizmore
 */
final class Module_Gallery extends GDO_Module
{

	##############
	### Module ###
	##############
	public function getDependencies(): array { return ['File']; }

	public function getClasses(): array { return [GDO_Gallery::class, GDO_GalleryImage::class]; }

	public function onLoadLanguage(): void { $this->loadLanguage('lang/gallery'); }

	##############
	### Config ###
	##############
	public function getConfig(): array
	{
		return [
			GDT_Checkbox::make('guest_galleries')->initial('1')->notNull(),
			GDT_Checkbox::make('hook_left_bar')->initial('1')->notNull(),
			GDT_Checkbox::make('hook_right_bar')->initial('1')->notNull(),
		];
	}

	public function getUserSettings(): array
	{
		return [
			GDT_ACLRelation::make('gallery_acl')->initial('acl_all')->label('cfg_gallery_acl')->noacl(),
		];
	}

	public function onInitSidebar(): void
	{
		if ($this->cfgHookLeftBar())
		{
			GDT_Page::instance()->leftBar()->addField(
				GDT_Link::make('link_gallery')->href(href('Gallery', 'GalleryList')));
		}
		if ($this->cfgHookRightBar())
		{
			$user = GDO_User::current();
			if ($user->isAuthenticated())
			{
				GDT_Page::$INSTANCE->rightBar()->addField(
					GDT_Link::make('link_your_gallery')->href(href('Gallery', 'GalleryList', '&user=' . $user->getID())));
			}
		}
	}

	public function cfgHookLeftBar() { return $this->getConfigValue('hook_left_bar'); }

	public function cfgHookRightBar() { return $this->getConfigValue('hook_right_bar'); }

	###########
	### ACL ###
	###########

	public function cfgUserACLObject(GDO_User $user): GDT_ACLRelation
	{
		return Module_Gallery::instance()->userSetting($user, 'gallery_acl');
	}

	public function canSeeGallery(GDO_User $user, GDO_Gallery $gallery, &$reason)
	{
		return $gallery->aclColumn()->hasAccess($user, $gallery->getCreator(), $reason);
	}

	public function canAddGallery(GDO_User $user)
	{
		if ($user->isMember())
		{
			return true;
		}
		return $this->cfgGuestGalleries();
	}

	#############
	### Hooks ###
	#############

	public function cfgGuestGalleries() { return $this->getConfigValue('guest_galleries'); }

	public function hookUserSettingChange(GDO_User $user, $key, $value)
	{
		if ($key === 'gallery_acl')
		{
			$this->changeAllGalleryACL($user, $value);
		}
	}

	/**
	 * Update all user galleries when main acl is changed.
	 *
	 * @param GDO_User $user
	 * @param string $value
	 */
	private function changeAllGalleryACL(GDO_User $user, $value)
	{
		GDO_Gallery::table()->update()->
		set('gallery_acl=' . GDO::quoteS($value))->
		where('gallery_creator=' . $user->getID())->
		exec();
	}

}
