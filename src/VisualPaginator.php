<?php

namespace Artfocus\VisualPaginator;

use Nette\Application;
use Nette\Http;
use Nette\Utils;

/**
 * Visual paginator control.
 *
 * @author David Grudl
 * @copyright Copyright (c) 2009 David Grudl
 * @copyright Copyright (c) 2013 Jaroslav Hranička
 */
class VisualPaginator extends Application\UI\Control
{

	/**
	 * @var int
	 * @persistent
	 */
	public $page = 1;

	/** @var Utils\Paginator */
	private $paginator;

	/** @var string */
	private $templateFile;

	/** @var int */
	private $around = 3;

	/** @var int */
	private $skipRadius = 4;

	/**
	 * @return Utils\Paginator
	 */
	public function getPaginator()
	{
		if (!$this->paginator) {
			$this->paginator = new Utils\Paginator;
		}
		return $this->paginator;
	}

	/**
	 * @return int
	 */
	public function getPage()
	{
		return $this->page;
	}

	/**
	 * @return int
	 */
	public function getLimit()
	{
		return $this->getPaginator()->getLength();
	}

	/**
	 * @return int
	 */
	public function getOffset()
	{
		return $this->getPaginator()->getOffset();
	}

	/**
	 * Loads state information.
	 * @param array $params
	 * @return $this
	 * @throws Application\BadRequestException
	 */
	public function loadState(array $params)
	{
		parent::loadState($params);
		$this->getPaginator()->setPage($this->page);

		return $this;
	}

	/**
	 * @param int $count
	 * @param int $itemsPerPage
	 * @return $this
	 */
	public function setItems($count, $itemsPerPage)
	{
		$paginator = $this->getPaginator();
		$paginator->setItemCount($count);
		$paginator->setItemsPerPage($itemsPerPage);

		return $this;
	}

	/**
	 * Redirects if given page is invalid (too big).
	 */
	public function redirectInvalidPage()
	{
		$paginator = $this->getPaginator();
		if ($this->page > $paginator->getLastPage()) {
			$this->redirect(Http\IResponse::S302_FOUND, 'this', [
				'page' => $paginator->getLastPage(),
			]);
		}
	}

	/**
	 * @param string $file
	 * @return $this
	 */
	public function setTemplateFile($file = NULL)
	{
		$this->templateFile = $file;
		return $this;
	}

	/**
	 * @param int $around
	 * @return $this
	 */
	public function setAround($around)
	{
		$this->around = $around;
		return $this;
	}

	/**
	 * @param int $skipRadius
	 * @return $this
	 */
	public function setSkipRadius($skipRadius)
	{
		$this->skipRadius = $skipRadius;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getFirstPageLink()
	{
		return $this->link('this', [
			'page' => 1,
		]);
	}

	/**
	 * @return string
	 */
	public function getLastPageLink()
	{
		return $this->link('this', [
			'page' => $this->getPaginator()->getLastPage(),
		]);
	}

	/**
	 * @return string
	 */
	public function getPreviousPageLink()
	{
		return $this->link('this', [
			'page' => max($this->getPaginator()->getPage() - 1, 1),
		]);
	}

	/**
	 * @return string
	 */
	public function getNextPageLink()
	{
		return $this->link('this', [
			'page' => min($this->getPaginator()->getPage() + 1, $this->getPaginator()->getPageCount()),
		]);
	}

	/**
	 * @return int|null
	 */
	public function getPreviousPage()
	{
		$paginator = $this->getPaginator();
		return ($paginator->getPage() - 1 >= 0) ?
			$paginator->getPage() - 1 :
			NULL;
	}

	/**
	 * @return int|null
	 */
	public function getNextPage()
	{
		$paginator = $this->getPaginator();
		return ($paginator->getPage() + 1 <= $paginator->getPageCount()) ?
			$paginator->getPage() + 1 :
			NULL;
	}

	/**
	 * Renders paginator.
	 * @return void
	 */
	public function render()
	{
		$paginator = $this->getPaginator();
		$page = $paginator->getPage();

		if ($paginator->getPageCount() < 2) {
			$steps = [$page];
		} else {
			$start = max($paginator->getFirstPage(), $page - $this->around);
			$end = min($paginator->getLastPage(), $page + $this->around);
			$arr = range($start, $end);
			$arr = $this->skipSteps($arr, $paginator);
			$arr = $this->fillOneSteps($arr);
			$steps = array_values($arr);
		}

		$this->template->firstPage = $this->getFirstPageLink();
		$this->template->lastPage = $this->getLastPageLink();
		$this->template->previousPage = $this->getPreviousPageLink();
		$this->template->nextPage = $this->getNextPageLink();

		$this->template->steps = $steps;
		$this->template->paginator = $paginator;

		$this->template->setFile($this->getTemplateFile());
		$this->template->render();
	}

	/**
	 * @param array $arr
	 * @param Utils\Paginator $paginator
	 * @return array
	 */
	private function skipSteps(array $arr, Utils\Paginator $paginator)
	{
		$radius = $this->skipRadius;

		if ($radius) {
			$quotient = ($paginator->getPageCount() - 1) / $radius;
			for ($i = 0; $i <= $radius; $i++) {
				$arr[] = (int)round($quotient * $i) + $paginator->getFirstPage();
			}

			$arr = array_unique($arr);
			sort($arr);
		}

		return $arr;
	}

	/**
	 * @param array $arr
	 * @return array
	 */
	private function fillOneSteps(array $arr)
	{
		$prev = 0;
		foreach ($arr as $k => $v) {
			if ($prev === $v - 2) {
				$arr[] = $v - 1;
			}

			$prev = $v;
		}

		sort($arr);
		return $arr;
	}

	/**
	 * @return string
	 */
	private function getTemplateFile()
	{
		if ($this->templateFile) {
			return $this->templateFile;
		} else {
			return __DIR__ . '/templates/visualPaginator.latte';
		}
	}

}
