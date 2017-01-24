<?php
namespace page\model;

use n2n\context\RequestScoped;
use page\model\nav\NavTree;
use n2n\core\container\N2nContext;
use page\model\nav\LeafContent;
use n2n\core\N2N;

/**
 * state
 *
 */
class PageState implements RequestScoped {
	private $pageDao;
	private $n2nContext;
	private $navTree;
	private $leafContent;
	
	private function _init(PageDao $pageDao, N2nContext $n2nContext) {
		$this->pageDao = $pageDao;
		$this->n2nContext = $n2nContext;
	}
	
	/**
	 * @return NavTree
	 */
	public function getNavTree(): NavTree {
		if ($this->navTree === null) {
			if (!N2N::isDevelopmentModeOn()) {
				$this->navTree = $this->pageDao->getCachedNavTree();
			} else {
				$this->navTree = $this->pageDao->lookupNavTree();
			}
						
		}
		
		return $this->navTree;
	}	
	
	public function hasCurrent() {
		return $this->leafContent !== null;
	}
	
	public function setCurrentLeafContent(LeafContent $leafContent = null) {
		$this->leafContent = $leafContent;
	}
	
	/**
	 * @throws IllegalPageStateException
	 * @return \page\model\nav\LeafContent
	 */
	public function getCurrentLeafContent() {
		if ($this->leafContent !== null) {
			return $this->leafContent;
		}
		
		throw new IllegalPageStateException('No current LeafContent assigned.');
	}
	
	/**
	 * @param bool $required
	 * @return \page\model\nav\Leaf
	 */
	public function getCurrentLeaf(bool $required = true) {
		if (!$required && !$this->hasCurrent()) {
			return null;
		}
	
		return $this->getCurrentLeafContent()->getLeaf();
	}
	
	/**
	 * @return \page\model\nav\NavBranch
	 */
	public function getCurrentNavBranch() {
		return $this->getCurrentLeaf()->getNavBranch();
	}
}