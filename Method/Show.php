<?php
namespace GDO\Gallery\Method;

use GDO\Core\GDT;
use GDO\Core\GDT_Object;
use GDO\Core\Method;
use GDO\Gallery\GDO_Gallery;

final class Show extends Method
{

	public function isShownInSitemap(): bool { return false; }

	public function getMethodTitle(): string
	{
		$gallery = $this->getGallery();
		return t('mt_gallery_show', [$gallery->gdoDisplay('gallery_title')]);
	}

	public function getGallery(): GDO_Gallery
	{
		return $this->gdoParameterValue('id');
	}

	public function gdoParameters(): array
	{
		return [
			GDT_Object::make('id')->table(GDO_Gallery::table())->notNull(),
		];
	}

	public function execute(): GDT
	{
		return $this->templatePHP('gallery_page.php', [
			'gallery' => $this->getGallery(),
		]);
	}

}
