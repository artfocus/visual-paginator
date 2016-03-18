<?php

/**
 * This file is part of visual-paginator.
 * Copyright © 2016 Jaroslav Hranička <hranicka@outlook.com>
 */

namespace Artfocus\VisualPaginator;

use Artfocus\VisualPaginator;

trait VisualPaginatorTrait
{

	/**
	 * @var VisualPaginator\VisualPaginatorFactory
	 * @inject
	 */
	public $visualPaginatorFactory;

	protected function createComponentPaginator()
	{
		return $this->visualPaginatorFactory->create();
	}

}
