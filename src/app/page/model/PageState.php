<?php
namespace page\model;

use n2n\model\RequestScoped;
use page\model\nav\NavTree;
use n2n\core\container\N2nContext;
use page\model\nav\Leaf;
use page\model\nav\NavBranch;
use page\model\nav\LeafContent;

/**
 * state
 *
 */
class PageState implements RequestScoped {
	private $pageDao;
	private $navTree;
	private $leafContent;
	
	private function _init(PageDao $pageDao, N2nContext $n2nContext) {
		$this->pageDao = $pageDao;
		$this->n2nContext = $n2nContext;
	}
	
	public function getNavTree(): NavTree {
		if ($this->navTree === null) {
			$this->navTree = $this->pageDao->getCachedNavTree();			
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
	public function getCurrentLeafContent(): LeafContent {
		if ($this->leafContent !== null) {
			return $this->leafContent;
		}
		
		throw new IllegalPageStateException('No current LeafContent assigned.');
	}
	
	/**
	 *  @return \page\model\nav\Leaf
	 */
	public function getCurrentLeaf() {
		return $this->getCurrentLeafContent()->getLeaf();
	}
	
	/**
	 * @return \page\model\nav\NavBranch
	 */
	public function getCurrentNavBranch(): NavBranch {
		return $this->getCurrentLeaf()->getNavBranch();
	}
}