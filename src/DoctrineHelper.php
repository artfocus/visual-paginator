<?php

/**
 * This file is part of visual-paginator.
 * Copyright © 2016 Jaroslav Hranička <hranicka@outlook.com>
 */

namespace Artfocus\VisualPaginator;

use Doctrine\ORM\Tools\Pagination\Paginator as DoctrinePaginator;

class DoctrineHelper
{

	/**
	 * @param DoctrinePaginator $doctrinePaginator
	 * @param VisualPaginator $visualPaginator
	 * @return DoctrinePaginator
	 */
	public static function setup(DoctrinePaginator $doctrinePaginator, VisualPaginator $visualPaginator)
	{
		$doctrinePaginator
			->getQuery()
			->setFirstResult($visualPaginator->getOffset())
			->setMaxResults($visualPaginator->getLimit());

		return $doctrinePaginator;
	}

}
