<?php
namespace page\model;

use n2n\core\container\N2nContext;
use page\model\PageState;
use n2n\reflection\CastUtils;
use n2n\reflection\ArgUtils;
use page\model\nav\Leaf;
use page\model\nav\NavBranch;
use n2n\l10n\N2nLocale;
use page\model\nav\UnavailableLeafException;
use page\model\nav\UnknownNavBranchException;

class NavBranchCriteria {
	const NAMED_ROOT = 'root';
	const NAMED_CURRENT = 'current';
	const NAMED_HOME = 'home';
	const NAMED_SUBHOME = 'subhome';
	
	protected $name;
	protected $affiliatedObj;
	protected $tagNames;
	protected $hookKeys;
	protected $subsystemName;
	
	public static function createNamed(string $name) {
		ArgUtils::valEnum($name, array(self::NAMED_ROOT, self::NAMED_CURRENT, self::NAMED_HOME), null, true);
		$navBranchCriteria = new NavBranchCriteria();
		$navBranchCriteria->name = $name;
		return $navBranchCriteria;
	}
	
	public static function createSubHome(string $subsystemName = null) {
		$navBranchCriteria = new NavBranchCriteria();
		$navBranchCriteria->name = self::NAMED_SUBHOME;
		$navBranchCriteria->subsystemName = $subsystemName;
		return $navBranchCriteria;
	}
	
	public static function create($affiliatedObj = null, array $tagNames = null, array $hookKeys = null) {
		ArgUtils::valObject($affiliatedObj, true);
		$navBranchCriteria = new NavBranchCriteria();
		$navBranchCriteria->affiliatedObj = $affiliatedObj;
		$navBranchCriteria->tagNames = $tagNames;
		$navBranchCriteria->hookKeys = $hookKeys;
		return $navBranchCriteria;
	}
	
	/**
	 * @param N2nContext $n2nContext
	 * @return \page\model\nav\NavBranch
	 * @throws UnknownNavBranchException
	 */
	public function determine(PageState $pageState, N2nLocale &$n2nLocale, N2nContext $n2nContext) {
		if ($this->name === null) {
			if ($this->affiliatedObj instanceof NavBranch) {
				return $this->affiliatedObj;
			} else if ($this->affiliatedObj instanceof Leaf) {
				$n2nLocale = $this->affiliatedObj->getN2nLocale();
				return $this->affiliatedObj->getNavBranch();
			} else if ($pageState->hasCurrent()) {
				return $pageState->getNavTree()->getClosest($pageState->getCurrentNavBranch(), 
						$this->affiliatedObj, $this->tagNames, $this->hookKeys);
			} else {
				return $pageState->getNavTree()->get($this->affiliatedObj, $this->tagNames, $this->hookKeys);
			}
		}
		
		try {
			switch ($this->name) {
				case self::NAMED_CURRENT:
					return $pageState->getCurrentNavBranch();
				case self::NAMED_HOME:
					$subsystemName = null;
					if (null !== ($subsystem = $n2nContext->getHttpContext()->getRequest()->getSubsystem())) {
						$subsystemName = $subsystem->getName();
					}
					return $pageState->getNavTree()->getHomeLeaf($n2nLocale, $subsystemName)->getNavBranch();
				case self::NAMED_SUBHOME:
					return $pageState->getNavTree()->getHomeLeaf($n2nLocale, $this->subsystemName)->getNavBranch();
			}
		} catch (UnavailableLeafException $e) {
			throw new UnknownNavBranchException(null, 0, $e);
		}
		
		if (!$pageState->hasCurrent()) {
			return $pageState->getNavTree()->get();
		}
		
		return $pageState->getCurrentNavBranch()->getRoot();	
	}
}