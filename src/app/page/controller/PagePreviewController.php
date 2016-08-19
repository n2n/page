<?php
namespace page\controller;

use rocket\spec\ei\manage\preview\controller\PreviewControllerAdapter;
use n2n\reflection\CastUtils;
use page\bo\Page;
use rocket\spec\ei\manage\EiState;
use rocket\spec\ei\manage\EiSelection;
use n2n\l10n\N2nLocale;
use page\model\PageState;
use page\model\NavInitProcess;
use n2n\util\uri\Path;
use page\model\nav\UnavailableLeafException;
use n2n\web\http\PageNotFoundException;

class PagePreviewController extends PreviewControllerAdapter {
	
	public function getPreviewTypeOptions(EiState $eiState, EiSelection $eiSelection): array {
		$page = $eiSelection->getLiveObject();
		CastUtils::assertTrue($page instanceof Page);
		
		$pageContent = $page->getPageContent();
		if ($pageContent === null) return array();
		
		$options = array();
		foreach ($pageContent->getPageContentTs() as $pageControllerT) {
			$n2nLocale = $pageControllerT->getN2nLocale();
			$options[(string) $n2nLocale] = $n2nLocale->getName($eiState->getN2nLocale());
		}
		return $options;
	}
	
	public function index(PageState $pageState, Path $cmdPath, Path $cmdContextPath, array $params = null) {
		$page = $this->getPreviewModel()->getEntityObj();
		CastUtils::assertTrue($page instanceof Page);
		
		$pageContent = $page->getPageContent();
		if ($pageContent === null) {
			return;
		}
		
		$n2nLocale = N2nLocale::create($this->getPreviewType());
		$this->getN2nContext()->setN2nLocale($n2nLocale);
		
		$navInitProcess = new NavInitProcess($pageState->getNavTree());
		$navBranch = $page->createNavBranch($navInitProcess);
		
		$leafContent = null;
		try {
			$leafContent = $navBranch->getLeafByN2nLocale($n2nLocale)
					->createLeafContent($this->getN2nContext(), $cmdPath, $cmdContextPath);
		} catch (UnavailableLeafException $e) {
			throw new PageNotFoundException('Preview unavailable.', null, $e);	
		}
		
		$pageState->setCurrentLeafContent($leafContent);
		$this->delegateToControllerContext($leafContent->getControllerContext());
	}
}