<?php
namespace page\model\leaf;

use page\model\nav\Leaf;
use n2n\core\container\N2nContext;
use n2n\persistence\orm\EntityManager;
use n2n\reflection\CastUtils;
use page\bo\Page;
use n2n\l10n\N2nLocale;
use n2n\util\uri\Path;
use page\model\nav\LeafContent;
use page\model\PageControllerAnalyzer;
use page\model\nav\impl\CommonLeafContent;
use page\model\PageMethod;
use n2n\util\ex\IllegalStateException;
use n2n\reflection\ReflectionUtils;
use page\model\nav\UnknownContentItemPanelException;
use page\model\IllegalPageStateException;
use page\model\nav\SitemapItem;
use page\model\nav\murl\MurlPage;
use page\model\PageDao;
use n2n\web\http\nav\UnavailableUrlException;

class ContentLeaf extends LeafAdapter {
	private $pageId;
	
	public function __construct(N2nLocale $n2nLocale, string $name, int $pageId) {
		parent::__construct($n2nLocale, $name);
		$this->pageId = $pageId;
	}
		
	/**
	 * {@inheritDoc}
	 * @see \page\model\nav\Leaf::createLeafContent($n2nContext, $cmdPath, $cmdContextPath)
	 */
	public function createLeafContent(N2nContext $n2nContext, Path $cmdPath, Path $cmdContextPath): LeafContent {
		$em = $n2nContext->lookup(EntityManager::class);
		CastUtils::assertTrue($em instanceof EntityManager);
		
		$page = $em->find(Page::getClass(), $this->pageId);
		if ($page === null) {
			$pageDao = $n2nContext->lookup(PageDao::class);
			CastUtils::assertTrue($pageDao instanceof PageDao);
			$pageDao->clearCache();
			throw new IllegalStateException('Old cache conflict. Try to solve with auto clean up. Try again.');
		}
		$pageContent = $page->getPageContent();
		$pageController = $pageContent->getPageController();
		
		$analyzer = new PageControllerAnalyzer(new \ReflectionClass($pageController));
		$leafContent = new PageLeafContent($this, $cmdPath, $cmdContextPath, $pageController);
		
		$pageMethod = $analyzer->analyzeMethode($pageController->getMethodName());
		if ($pageMethod === null) {
			throw new IllegalPageStateException('Page method '
					. ReflectionUtils::prettyMethName(get_class($pageController), $pageController->getMethodName())
					. ' does not exist. Used in: ' . get_class($pageController) . '#' . $pageController->getId());
		}
		$leafContent->setPageMethod($pageMethod);
		
		if (null !== ($pageContentT = $pageContent->t($this->getN2nLocale()))) {
			$leafContent->setSeTitle($pageContentT->getSeTitle());
			$leafContent->setSeDescription($pageContentT->getSeDescription());
			$leafContent->setSeKeywords($pageContentT->getSeKeywords());
		}
		
		if (null !== ($pageControllerT = $pageController->pageControllerT($this->getN2nLocale()))) {
			$leafContent->setContentItems($pageControllerT->getContentItems()->getArrayCopy());
		}
		
		return $leafContent;
	}
	
	public function createSitemapItems(N2nContext $n2nContext): array {
		try {
			return array(new SitemapItem(
					MurlPage::obj($this->navBranch)->locale($this->n2nLocale)->absolute()->toUrl($n2nContext)/*,
					null, $this->determineChangeFreq(), $this->determinePriority()*/));
		} catch (UnavailableUrlException $e) {
			return array();
		}
		
	}
	

// 	private function determinePriority() {
// 		$negativeScore = 0;
		
// 		if (!$this->isHome()) {
// 			$negativeScore++;
// 		}
		
// 		$negativeScore += $this->navBranch->getLevel();
// 		$negativeScore = ($negativeScore > 9) ? 9 : $negativeScore;
				 
// 		return 1 - ($negativeScore / 10);
// 	}
	
// 	private function determineChangeFreq() {
// 		$negativeScore = 1;
    	
//     	if (!$this->isHome()) {
//     		$negativeScore++;
//     	}
    	
//     	$negativeScore += $this->navBranch->getLevel();
    	
// 		switch (ceil($negativeScore)) {
//         	case 1:
//             	return SitemapItem::CHANGE_FREQ_HOURLY;
//         	case 2:
//             	return SitemapItem::CHANGE_FREQ_DAILY;
//         	case 3:
//             	return SitemapItem::CHANGE_FREQ_WEEKLY;
//         	case 4:
//             	return SitemapItem::CHANGE_FREQ_MONTHLY;
//         	default:
//             	return SitemapItem::CHANGE_FREQ_YEARLY;
//         }
//     }
	    
}

class PageLeafContent extends CommonLeafContent {
	private $pageMethod;
	private $contentItems = array();
	
	public function setPageMethod(PageMethod $pageMethod) {
		$this->pageMethod = $pageMethod;
	}
	
	public function getPageMethod() {
		IllegalStateException::assertTrue($this->pageMethod !== null);
		return $this->pageMethod;
	}
	
	public function setContentItems(array $contentItems) {
		$this->contentItems = $contentItems;
	}
	
	public function getContentItems() {
		return $this->contentItems;
	}
	
	public function containsContentItemPanelName(string $panelName): bool {
		return $this->getPageMethod()->containsCiPanelName($panelName);
	}
	
	public function getContentItemsByPanelName(string $panelName): array {
		if (!$this->containsContentItemPanelName($panelName)) {
			$pageController = $this->getControllerContext()->getController();
			throw new UnknownContentItemPanelException('Undefined ContentItem panel \'' . $panelName . '\' for ' 
					. ReflectionUtils::prettyMethName(get_class($pageController), 
							$pageController->getMethodName()));
		}
		
		$contentItems = array();
		foreach ($this->contentItems as $contentItem) {
			if ($contentItem->getPanel() === $panelName) {
				$contentItems[] = $contentItem;
			}
		}
		return $contentItems;
	}
	
}


