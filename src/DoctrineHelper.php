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
	 * @param int $itemsPerPage
	 * @return array
	 */
	public static function apply(DoctrinePaginator $doctrinePaginator, VisualPaginator $visualPaginator, $itemsPerPage = 50):array
	{
		$visualPaginator->setItems($doctrinePaginator->count(), $itemsPerPage);

		$doctrinePaginator
			->getQuery()
			->setFirstResult($visualPaginator->getOffset())
			->setMaxResults($visualPaginator->getLimit());

		return iterator_to_array($doctrinePaginator);
	}

}
