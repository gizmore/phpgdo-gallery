<?php
namespace GDO\Gallery;

use GDO\Core\GDO_Module;
use GDO\UI\GDT_Link;
use GDO\User\GDO_User;
use GDO\Core\GDT_Checkbox;
use GDO\Friends\GDT_ACL;
use GDO\Core\GDO;
use GDO\UI\GDT_Page;

/**
 * Image galleries.
 * 
 * @author gizmore
 * @version 6.11.0
 * @since 6.7.0
 */
final class Module_Gallery extends GDO_Module
{
	##############
	### Module ###
	##############
	public function getDependencies() : array { return ['File']; }
	public function getClasses() : array { return [GDO_Gallery::class, GDO_GalleryImage::class]; }
	public function onLoadLanguage() : void { $this->loadLanguage('lang/gallery'); }
	
	##############
	### Config ###
	##############
	public function getConfig() : array
	{
		return [
		    GDT_Checkbox::make('guest_galleries')->initial('1')->notNull(),
		    GDT_Checkbox::make('hook_left_bar')->initial('1')->notNull(),
		    GDT_Checkbox::make('hook_right_bar')->initial('1')->notNull(),
		];
	}
	public function cfgGuestGalleries() { return $this->getConfigValue('guest_galleries'); }
	public function cfgHookLeftBar() { return $this->getConfigValue('hook_left_bar'); }
	public function cfgHookRightBar() { return $this->getConfigValue('hook_right_bar'); }
	
	public function getUserSettings()
	{
		return [
			GDT_ACL::make('gallery_acl')->initial('acl_all'),
		];
	}
	
	###########
	### ACL ###
	###########
	/**
	 * @param GDO_User $user
	 * @return GDT_ACL
	 */
	public function cfgUserACL(GDO_User $user)
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
	public function onInitSidebar() : void
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
	                GDT_Link::make('link_your_gallery')->href(href('Gallery', 'GalleryList', '&user='.$user->getID())));
	        }
	    }
	}

	public function hookUserSettingChange(GDO_User $user, $key, $value)
	{
		if ($key === 'gallery_acl')
		{
			$this->changeAllGalleryACL($user, $value);
		}
	}
	
	/**
	 * Update all user galleries when main acl is changed.
	 * @param GDO_User $user
	 * @param string $value
	 */
	private function changeAllGalleryACL(GDO_User $user, $value)
	{
		GDO_Gallery::table()->update()->
			set('gallery_acl='.GDO::quoteS($value))->
			where('gallery_creator='.$user->getID())->
			exec();
	}

}
