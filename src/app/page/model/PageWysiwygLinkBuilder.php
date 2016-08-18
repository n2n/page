<?php
namespace page\model;

use n2n\http\Request;
use n2n\core\container\N2nContext;
use page\model\ex\UrlBuildingException;
use n2n\reflection\ObjectAdapter;
use rocket\spec\ei\component\field\impl\string\wysiwyg\DynamicUrlBuilder;
use n2n\core\config\HttpConfig;
use n2n\http\HttpContext;

class PageWysiwygLinkBuilder extends ObjectAdapter implements DynamicUrlBuilder {

	const CHARACTERISTICS_KEY_ID = 'id';
	
	private $pageState;
	private $pageDao;
	private $n2nContext;

	private function _init(PageDao $pageDao, N2nContext $n2nContext, PageState $pageState) {
		$this->pageDao = $pageDao;
		$this->n2nContext = $n2nContext;
		$this->pageState = $pageState;
	}

	public function buildUrl(HttpContext $httpContext, $characteristics) {
		$page = $this->pageDao->getPageById($characteristics[self::CHARACTERISTICS_KEY_ID]);
		if (null === $page) return null;
		
		try {
			return $page->buildUrl($this->pageState->getNavTree(), $this->n2nContext, 
					array($httpContext->getLocale(), $this->n2nContext->getDefaultLocale(), $this->n2nContext->getFallbackLocale()));
		} catch (UrlBuildingException $e) {
			return null;
		}
	}
}