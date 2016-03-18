# Artfocus\VisualPaginator

## Installation

`$ composer require artfocus/visual-paginator`

## Setup

Register `Artfocus\VisualPaginator\VisualPaginatorFactory` as a Service in your DI.

```yaml
# app/config.neon

services:
	- Artfocus\VisualPaginator\VisualPaginatorFactory

```

## Example usage (with Doctrine)

```php

<?php

namespace App\Presenters;

use App\Facade\ArticleFacade;
use Artfocus\VisualPaginator;

class ArticlePresenter extends BasePresenter
{

	use VisualPaginator\VisualPaginatorTrait;

	/**
	 * @var ArticleFacade
	 * @inject
	 */
	public $articleFacade;

	public function renderDefault()
	{
		// Get instance of Doctrine\ORM\Tools\Pagination\Paginator
		$articles = $this->articleFacade->findByTag('new');
		
		// Apply pagination - 10 items per page.
		$this->template->articles = VisualPaginator\DoctrineHelper::apply($articles, $this->getComponent('paginator'), 10);
	}

}

```

```smarty
{* Article/default.latte *}

{block content}
	<ul n:inner-foreach="$articles as $article">
		<li>{$article->getTitle()}</li>
	</ul>

	{control paginator}
{/block}
```
