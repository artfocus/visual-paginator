<?php

namespace Artfocus\VisualPaginator;

use Nette\DI;

class VisualPaginatorFactory
{

	/** @var DI\Container */
	private $container;

	/** @var VisualPaginator[] */
	private $paginatorStack = [];

	public function __construct(DI\Container $container)
	{
		$this->container = $container;
	}

	/**
	 * @return VisualPaginator
	 */
	public function create()
	{
		$paginator = new VisualPaginator();

		$this->container->callInjects($paginator);

		$this->paginatorStack[] = $paginator;

		return $paginator;
	}

	/**
	 * @return VisualPaginator|null
	 */
	public function getLastPaginator()
	{
		if ($this->paginatorStack) {
			return end($this->paginatorStack);
		}

		return NULL;
	}

}
