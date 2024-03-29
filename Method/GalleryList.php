<?php
namespace GDO\Gallery\Method;

use GDO\Core\GDO;
use GDO\Core\GDT;
use GDO\Core\GDT_Response;
use GDO\DB\Query;
use GDO\Gallery\GDO_Gallery;
use GDO\Table\MethodQueryList;
use GDO\UI\GDT_Button;
use GDO\User\GDO_User;
use GDO\User\GDT_User;
use GDO\Util\Common;

final class GalleryList extends MethodQueryList
{

	public function gdoParameters(): array
	{
		return array_merge(parent::gdoParameters(), [
			GDT_User::make('user')->noFilter(),
		]);
	}

	/**
	 * @return GDO
	 */
	public function gdoTable(): GDO
	{
		return GDO_Gallery::table();
	}

	public function getQuery(): Query
	{
		$galleries = $this->gdoTable();
		$query = $galleries->select();
		if ($userId = (int)$this->gdoParameterVar('user'))
		{
			$query->where("gallery_creator=$userId");
		}
		else
		{
			# Prepare query for acl condition
			$galleries->aclColumn()->aclQuery(
				$query, GDO_User::current(), 'gallery_creator');
		}
		return $query;
	}

	public function execute(): GDT
	{
		$response = GDT_Response::make();

		# Own gallery allows you to add an image.
		if (Common::getRequestString('user') === GDO_User::current()->getID())
		{
			$link = GDT_Button::make('link_gallery_add')->icon('create')->href(href('Gallery', 'Crud'));
			$response->addField($link);
		}

		# Append the list
		return $response->addField(parent::execute());
	}

}
