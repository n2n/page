<?php
namespace page\ui\nav;

use n2n\ui\view\impl\html\HtmlElement;
use n2n\l10n\N2nLocale;
use page\model\nav\NavBranch;
use n2n\ui\view\impl\html\HtmlView;
use n2n\util\col\ArrayUtils;

class NavFactory {
	private $navItemBuilder;
	private $n2nLocale;
	private $numLevels;
	private $numOpenLevels = 0;
	private $currentNavBranch;
	private $rootUlAttrs;
	private $ulAttrs;
	private $liAttrs;
	
	public function __construct(NavItemBuilder $navItemBuilder, N2nLocale $n2nLocale) {
		$this->navItemBuilder = $navItemBuilder;
		$this->n2nLocale = $n2nLocale;
	}
	
	public function setNumLevels(int $numLevels = null) {
		$this->numLevels = $numLevels;
	}
	
	public function setNumOpenLevels(int $numOpenLevels = null) {
		$this->numOpenLevels = $numOpenLevels;
	}
	
	public function setCurrentNavBranch(NavBranch $currentNavBranch = null) {
		$this->currentNavBranch = $currentNavBranch;
	}
	
	public function setRootUlAttrs(array $rootUlAttrs) {
		$this->rootUlAttrs = $rootUlAttrs;
	}
	
	public function setUlAttrs(array $ulAttrs) {
		$this->ulAttrs = $ulAttrs;
	}
	
	public function setLiAttrs(array $liAttrs) {
		$this->liAttrs = $liAttrs;
	}
	
	public function create(HtmlView $view, array $baseNavBranches) {
		$ul = null;
		if (null !== ($navBranch = ArrayUtils::current($baseNavBranches))) {
			$ul = $this->navItemBuilder->buildRootUl($view, $navBranch->getLevel(), $this->rootUlAttrs);
		} else {
			return null;
		}
		
		$numProcessLevels = $this->numLevels;
		if ($numProcessLevels === null) {
			$numProcessLevels = PHP_INT_MAX;
		}
		$numProcessOpenLevels = $this->numOpenLevels;
		if ($numProcessOpenLevels === null) {
			$numProcessOpenLevels = PHP_INT_MAX;
		}

		foreach ($baseNavBranches as $childNavBranch) {
			$ul->appendContent($this->buildLi($view, $childNavBranch, $numProcessLevels, $numProcessOpenLevels));
		}
		
		return $ul;
	}
	
	private function buildInfos(NavBranch $navBranch): int {
		$infos = 0;
		
		if ($navBranch->equals($this->currentNavBranch)) {
			$infos |= NavItemBuilder::INFO_CURRENT;
		}
		
		if ($this->currentNavBranch !== null 
				&& $navBranch->containsDescendant($this->currentNavBranch)) {
			$infos |= NavItemBuilder::INFO_OPEN;
		}
		
		return $infos;
	}
	
	private function buildLi(HtmlView $view, NavBranch $navBranch, int $numProcessLevels, 
			int $numProcessOpenLevels) {
	    if (!$navBranch->containsLeafN2nLocale($this->n2nLocale)) {
			return null;
		}
		
		$leaf = $navBranch->getLeafByN2nLocale($this->n2nLocale);
		if (!$leaf->isInNavigation()) {
		    return null;
		}
		
		$infos = $this->buildInfos($navBranch);
		$li = $this->navItemBuilder->buildLi($view, $leaf, $this->liAttrs, $infos);
		
		$childNavBranches = $navBranch->getChildren();
		$numProcessLevels--;
		$numProcessOpenLevels--;
		if (empty($childNavBranches)
        		|| ($numProcessLevels <= 0 && !($infos & (NavItemBuilder::INFO_OPEN|NavItemBuilder::INFO_CURRENT)
                        && $numProcessOpenLevels >= 0))) {
			return $li;
		}

		$ul = $this->navItemBuilder->buildUl($view, $leaf, $this->ulAttrs, $infos);
		$li->appendContent($ul);
		
		foreach ($childNavBranches as $childNavBranch) {
			$ul->appendContent($this->buildLi($view, $childNavBranch, $numProcessLevels, $numProcessOpenLevels));
		}
		
		return $li;
	}
}

// class NavBuilder {
// 	private $view;
// 	private $pageHtml;
// 	/**
// 	 * @var PageConfig
// 	 */
// 	private $pageConfig;
	
// 	private $activeBranch;
// 	private $n2nLocale;
// 	private $navConfig;
// 	private $navLabeler;
	
// 	public function __construct(HtmlView $view, PageHtmlBuilder $pageHtml) {
// 		$this->view = $view;
// 		$this->pageHtml = $pageHtml;
// 		$this->pageConfig = $view->getN2nContext()->getModuleConfig('page');
// 	}
	
// 	public function build($baseTarget, $activeTarget = null, 
// 			NavConfig $navConfig = null, N2nLocale $n2nLocale = null) {
		
// 		$baseBranch = $this->pageHtml->meta()->determineBranch($baseTarget);
// 		$this->setup($activeTarget, $navConfig, $n2nLocale);
		
		
// 		$openLevels = $this->navConfig->getOpenLevels();
// 		$startLevel = $baseBranch->getLevel();
// 		$elemUl = new HtmlElement('ul', array('class' => $this->buildClassName('level-' . ($startLevel + 1))));

// 		$elemUlBaseBranch = null;
// 		$elemLiBaseBranch = null;
// 		if ($this->navConfig->isIncludeBasePage()) {
// 			$elemUlBaseBranch = new HtmlElement('ul', array('class' => $this->buildClassName('level-' . $baseBranch->getLevel())));
// 			$elemLiBaseBranch = new HtmlElement('li', $this->buildLiAttrs($baseBranch, $baseBranch->getLevel()), 
// 					$this->buildLiLink($baseBranch));
// 		}
		
// 		foreach ($baseBranch->getChildren() as $child) {
// 			$processLevels = $openLevels;
// 			if (($child->isChild($this->activeBranch) || $child->isActive($this->activeBranch)) 
// 					&& !(($childLevels = $this->navConfig->getChildLevels()) == 0)) {
// 				$childLevels = $this->activeBranch->getLevel() + $this->navConfig->getChildLevels();
// 				$processLevels = ($childLevels > $processLevels) ? $childLevels : $processLevels;
// 			}
			
// 			$elemUl->appendContent($this->createLiForTreeItem($child, $processLevels - 1));
// 		}
		
// 		if (null === $elemUlBaseBranch) return $elemUl;
		
// 		$elemLiBaseBranch->appendContent($elemUl);
// 		$elemUlBaseBranch->appendContent($elemLiBaseBranch);
		
// 		return $elemUlBaseBranch;
// 	}
	
// 	private function setup($activeTarget = null, 
// 			NavConfig $navConfig = null, N2nLocale $n2nLocale = null) {
// 		$this->activeBranch = $this->determineActiveBranch($activeTarget);
// 		$this->navConfig = (null !== $navConfig) ? $navConfig : NavConfig::create();
		
// 		$this->navLabeler = $this->navConfig->getNavLabeler();
// 		if (null === $this->navLabeler) {
// 			$this->navLabeler = $this->pageConfig->createNavLabeler();
// 		}
// 		if (null !== $this->navLabeler) {
// 			$this->navLabeler->setup($this->view);
// 		}
		
// 		$this->n2nLocale = (null !== $n2nLocale) ? $n2nLocale : $this->view->getN2nContext()->getN2nLocale();
// 	}
	
// 	private function determineActiveBranch($activeTarget = null) {
// 		if (null !== $activeTarget) {
// 			return $this->pageHtml->meta()->determineBranch($activeTarget);
// 		}
		
// 		return $this->pageHtml->meta()->getCurrentBranch();
// 	}
	
// 	private function createLiForTreeItem(NavBranch $branch, $processLevels) {
// 		if (!$branch->isAccessible($this->n2nLocale)) return null;

// 		$leaf = $branch->getLeaf();
		
// 		$elemLi = new HtmlElement('li');
// 		$elemLi->appendContent($this->buildLiLink($branch));
		
// 		$elemLi->setAttrs($this->buildLiAttrs($branch));

// 		if (($processLevels === 0 && !($this->navConfig->getOpenLevels() === 0)) 
// 				|| !$branch->hasVisibleChildren()) return $elemLi;
		
// 		$elemUl = new HtmlElement('ul', array('class' => $this->buildClassName('level-' . ($branch->getLevel() + 1))));
		
// 		foreach ($branch->getChildren() as $child) {
// 			if (!$child->getLeaf()->isVisible()) continue;
			
// 			$elemUl->appendContent($this->createLiForTreeItem($child, $processLevels - 1));
// 		}
		
// 		$elemLi->appendContent($elemUl);
		
// 		return $elemLi;
// 	}
	
// 	private function buildLiLink(NavBranch $branch) {
// 		return $this->pageHtml->getLink($branch, 
// 				$this->navLabeler->createLabel($this->n2nLocale, $branch),
// 				$this->navLabeler->getLinkAttrs($this->n2nLocale, $branch));
// 	}
	
// 	private function buildLiAttrs(NavBranch $branch) {
// 		$attrs = array('class' => $this->buildClassName('level-' . $branch->getLevel()));
		
// 		if (null !== ($characteristicsKey = $branch->getLeaf()->getCharacterisitcKey())) {
// 			$attrs['class'] .= ' ' . $this->buildClassName($characteristicsKey);
// 		}
		
// 		if ($branch->isActive($this->activeBranch)) {
// 			$attrs['class'] .= ' ' . $this->buildClassName('active');
				
// 		} elseif ($branch->isChild($this->activeBranch)) {
// 			$attrs['class'] .= ' ' . $this->buildClassName('open');
// 		}
		
// 		if ($branch->hasVisibleChildren()) {
// 			$attrs['class'] .= ' ' . $this->buildClassName('has-children');
// 		}
		
// 		return HtmlUtils::mergeAttrs($attrs, 
// 				(array) $this->navConfig->getLiAttrs());
// 	}
	
// 	private function buildClassName($className) {
// 		return $this->pageHtml->meta()->buildClassName($className);
// 	}
// }