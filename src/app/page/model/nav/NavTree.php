<?php
namespace page\model\nav;

use n2n\l10n\N2nLocale;
use n2n\util\uri\Path;
use n2n\web\http\HttpContext;
use n2n\util\uri\Url;
use n2n\reflection\CastUtils;
use page\config\PageConfig;
use n2n\web\http\controller\ControllerContext;
use n2n\core\container\N2nContext;
use n2n\reflection\ArgUtils;

class NavTree {
	private $rootNavBranches = array();
	
	public function __construct() {
		
// 		foreach ($rootNavBranches as $rootNavBranch) {
// 			$rootNavBranch->setNavTree($this);
// 		}
	}
	
	public function addRootNavBranch(NavBranch $navBranch) {
		$this->rootNavBranches[] = $navBranch;
	}
	
	public function getRootNavBranches() {
		return $this->rootNavBranches;
	}
	
	public function createLeafContents(N2nContext $n2nContext, Path $cmdPath, Path $contextPath, 
			N2nLocale $n2nLocale, string $subsystemName = null, bool $homeOnly = false) {
		$resolver = new NavPathResolver($n2nContext, $n2nLocale, $subsystemName);
		if ($homeOnly || $cmdPath->isEmpty()) {
			$resolver->analyzeHome($this->rootNavBranches, $cmdPath->getPathParts(), $contextPath->getPathParts());
		} else if (!$resolver->analyzeLevel($this->rootNavBranches, $cmdPath->getPathParts(), $contextPath->getPathParts())) {
			$resolver->analyzeHome($this->rootNavBranches, $cmdPath->getPathParts(), $contextPath->getPathParts());
		}
		return $resolver->getLeafContents();
	}
	
	
	
	/**
	 * @param unknown $affiliatedObj
	 * @param array $tagNames
	 * @param array $hookKeys
	 * @return NavBranch or null if not found
	 */
	public function findRoot($affiliatedObj = null, array $tagNames = null, array $hookKeys = null) {
		$navFilter = new NavBranchFilter($affiliatedObj, $tagNames, $hookKeys);
		return $navFilter->find($this->rootNavBranches);
	}
	
	public function findHomeLeaf(N2nLocale $n2nLocale, string $subsystemName = null) {
		$leafFilter = new LeafFilter($n2nLocale, $subsystemName);
		return $leafFilter->findHome($this->rootNavBranches);
	}
	
	public function getHomeLeaf(N2nLocale $n2nLocale, string $subsystemName = null) {
		$leafFilter = new LeafFilter($n2nLocale, $subsystemName);
		if (null !== ($leaf = $leafFilter->findHome($this->rootNavBranches))) {
			return $leaf;
		}
		
		throw new UnavailableLeafException('No home Leaf found for locale: ' . $n2nLocale . 
				($subsystemName !== null? '; Subsystem: ' . $subsystemName : ''));
	}
	
	/**
	 * @param unknown $affiliatedObj
	 * @param array $tagNames
	 * @param array $hookKeys
	 * @return NavBranch or null if not found
	 */
	public function find($affiliatedObj = null, array $tagNames = null, array $hookKeys = null) {
		$navFilter = new NavBranchFilter($affiliatedObj, $tagNames, $hookKeys);
		return $navFilter->findR($this->rootNavBranches);
	}
	
	public function findClosest(NavBranch $navBranch, $affiliatedObj = null, array $tagNames = null, 
			array $hookKeys = null) {
		$navFilter = new NavBranchFilter($affiliatedObj, $tagNames, $hookKeys);
		return $navFilter->findClosest($navBranch);
	}
	
	/**
	 * @param unknown $affiliatedObj
	 * @param array $tagNames
	 * @param array $hookKeys
	 * @throws UnknownNavBranchException
	 * @return NavBranch
	 */
	public function get($affiliatedObj = null, array $tagNames = null, array $hookKeys = null) {
		if (null !== ($navBranch = $this->find($affiliatedObj, $tagNames, $hookKeys))) {
			return $navBranch;
		}
		
		throw $this->createException($affiliatedObj, $tagNames, $hookKeys);
	}
	
	public function getClosest(NavBranch $navBranch, $affiliatedObj = null, array $tagNames = null, array $hookKeys = null) {
		if (null !== ($navBranch = $this->findClosest($navBranch, $affiliatedObj, $tagNames, $hookKeys))) {
			return $navBranch;
		}
	
		throw $this->createException($affiliatedObj, $tagNames, $hookKeys);
	}
	
	private function createException($affiliatedObj = null, array $tagNames = null, array $hookKeys = null) {
		$chrits = array();
		if ($affiliatedObj !== null) {
			$chrits[] = 'affiliated object: ' . get_class($affiliatedObj);
		}
		
		if (!empty($tagNames)) {
			$chrits[] = 'tag names: [' . implode(', ', $tagNames) . ']';
		}
		
		if (!empty($hookKeys)) {
			$chrits[] = 'hook keys: [' . implode(', ', $hookKeys) . ']';
		}
		
		throw new UnknownNavBranchException('No matching NavBranch found: ' . implode('; ', $chrits));
	}
	
	public function createUrlBuilder(HttpContext $httpContext, bool $fallbackBackAllowed) {
		$urlBuilder = new NavUrlBuilder($httpContext);
		$urlBuilder->setFallbackAllowed($fallbackBackAllowed);
		return $urlBuilder;
	}
	
	public function createSitemapItems(N2nContext $n2nContext, string $subsystemName = null) {
		$sitemapItemBuilder = new SitemapItemBuilder($n2nContext, $subsystemName);
		return $sitemapItemBuilder->analyzeLevel($this->rootNavBranches);
	}
}

class SitemapItemBuilder {
	
	public function __construct(N2nContext $n2nContext, string $subsystemName = null) {
		$this->n2nContext = $n2nContext;
		$this->subsystemName = $subsystemName;
	}
	
	public function analyzeLevel(array $navBranches): array {
		$sitemapItems = array();
		foreach ($navBranches as $navBranch) {
			$sitemapItems = array_merge($sitemapItems, $this->analyzeBranch($navBranch));
		}
		return $sitemapItems;
	}
	
	public function analyzeBranch(NavBranch $navBranch) {
		$sitemapItems = array();
		
		foreach ($navBranch->getLeafs() as $leaf) {
			if ($this->subsystemName !== $leaf->getSubsystemName() || !$leaf->isAccessible()) continue;
			
			$leafSitemapItems = $leaf->createSitemapItems($this->n2nContext);
			ArgUtils::valArrayReturn($leafSitemapItems, $leaf, 'createSitemapItems', SitemapItem::class);
			$sitemapItems = array_merge($sitemapItems, $leafSitemapItems);
		}
		
		return array_merge($sitemapItems, $this->analyzeLevel($navBranch->getChildren()));
	}
}

class NavPathResolver {
	private $n2nContext;
	private $n2nLocale;
	private $subsystemName;
	private $leafContents = array();
	
	public function __construct(N2nContext $n2nContext, N2nLocale $n2nLocale, string $subsystemName = null) {
		$this->n2nContext = $n2nContext;
		$this->n2nLocale = $n2nLocale;
		$this->subsystemName = $subsystemName;
	}
	
	public function getLeafContents() {
		return array_reverse($this->leafContents);
	}
	
	public function analyzeHome(array $navBranches, array $cmdPathParts, array $contextPathParts) {
		foreach ($navBranches as $navBranch) {
			if ($navBranch->containsLeafN2nLocale($this->n2nLocale)) {
				$leaf = $navBranch->getLeafByN2nLocale($this->n2nLocale);
				$subsystemName = $leaf->getSubsystemName();
				if ($leaf->isHome() && ($subsystemName === null || $this->subsystemName === $subsystemName)) {
					$this->leafContents[] = $leaf->createLeafContent($this->n2nContext, new Path($cmdPathParts), 
							new Path($contextPathParts));
					return true;
				}
			}
			
			if ($this->analyzeHome($navBranch->getChildren(), $cmdPathParts, $contextPathParts)) {
				return true;
			}
		}
		
		return false;
	}
	
	public function analyzeLevel(array $navBranches, array $cmdPathParts, array $contextPathParts): bool {
		foreach ($navBranches as $navBranch) {
			if ($this->analyzeBranch($navBranch, $cmdPathParts, $contextPathParts)) return true;
		}
		
		return false;
	}
	
	public function analyzeBranch(NavBranch $navBranch, array $cmdPathParts, array $contextPathParts): bool {
		if (empty($cmdPathParts)) {
			return false;
		}
		
		if (!$navBranch->containsLeafN2nLocale($this->n2nLocale)) {
			if ($navBranch->isInPath()) return false;
			
			return $this->analyzeLevel($navBranch->getChildren(), $cmdPathParts, $contextPathParts);
		}
		
		$leaf = $navBranch->getLeafByN2nLocale($this->n2nLocale);
		
		if (($leaf->getSubsystemName() !== null && $this->subsystemName !== $leaf->getSubsystemName()) || $leaf->isHome()) {
			return $this->analyzeLevel($navBranch->getChildren(), $cmdPathParts, $contextPathParts);
		}
		
		$pathPart = reset($cmdPathParts);
		
		if ($navBranch->isInPath()) {
			if ($leaf->getPathPart() !== $pathPart || !$leaf->isAccessible()) return false;
			
			$contextPathParts[] = array_shift($cmdPathParts);
			$this->leafContents[] = $leaf->createLeafContent($this->n2nContext, new Path($cmdPathParts), 
						new Path($contextPathParts));
			$this->analyzeLevel($navBranch->getChildren(), $cmdPathParts, $contextPathParts);
			return true;
		}
		
		if ($leaf->getPathPart() === $pathPart) {
			if (!$leaf->isAccessible()) return false;
			
			$contextPathParts[] = array_shift($cmdPathParts);
			$this->leafContents[] = $leaf->createLeafContent($this->n2nContext, new Path($cmdPathParts), 
						new Path($contextPathParts));
			return true;
		}
			
		return $this->analyzeLevel($navBranch->getChildren(), $cmdPathParts, $contextPathParts);
	}
}

class LeafResult {
	private $leaf;
	private $controllerContext;
	
	public function __construct(Leaf $leaf, ControllerContext $controllerContext) {
		$this->leaf = $leaf;
		$this->controllerContext = $controllerContext;
	}
	
	public function getLeaf() {
		return $this->leaf;
	}
	
	public function getControllerContext() {
		return $this->controllerContext;
	}
}

class NavUrlBuilder {
	private $httpContext;
	private $pageConfig;
	private $fallbackBackAllowed = false;
	private $absolute = false;
	private $accessiblesOnly = true;
	private $pathExt;
	
	public function __construct(HttpContext $httpContext) {
		$this->httpContext = $httpContext;
		$this->pageConfig = $httpContext->getN2nContext()->getModuleConfig('page');
		CastUtils::assertTrue($this->pageConfig instanceof PageConfig);
	}
	
	public function setFallbackAllowed(bool $fallbackBackAllowed) {
		$this->fallbackBackAllowed = $fallbackBackAllowed;
	}
	
	public function setAbsolute(bool $absolute) {
		$this->absolute = $absolute;
	}
	
	public function setAccessiblesOnly(bool $accessiblesOnly) {
		$this->accessiblesOnly = $accessiblesOnly;
	}
	
	public function setPathExt(Path $pathExt = null) {
		$this->pathExt = $pathExt;
	}
	
	/**
	 * @param NavBranch $navBranch
	 * @param N2nLocale $n2nLocale
	 * @param bool $required
	 * @throws BranchUrlBuildException
	 * @return \n2n\util\uri\Url
	 */
	public function build(NavBranch $navBranch, N2nLocale $n2nLocale, bool $required = false, NavBranch &$curNavBranch = null) {
		$curNavBranch = $navBranch;
		while (true) {
			try {
				return $this->buildUrl($this->buildUrlBuildTask($curNavBranch, $n2nLocale));
			} catch(UnavailableLeafException $e) {
				if ($this->fallbackBackAllowed && null !== ($curNavBranch = $curNavBranch->getParent())) {
					continue;
				}
				
				if (!$required) return null;
				
				throw new BranchUrlBuildException('Failed to build url of branch ' . $navBranch . ' for locale \'' 
						. $n2nLocale . '\'.', 0, $e);
			}
		};
	}
	
	private function buildUrlBuildTask(NavBranch $navBranch, N2nLocale $n2nLocale)  {
		$task = new UrlBuildTask($navBranch, $n2nLocale, $this->httpContext);
		
		do {
			$navBranch = $task->getNavBranch();
			$leaf = $navBranch->getLeafByN2nLocale($n2nLocale);
			if ($this->accessiblesOnly && !$leaf->isAccessible()) {
				throw new UnavailableLeafException($leaf . ' not accessible.');
			}
			$leaf->prepareUrl($task);
		} while ($task->getUrl() === null && $navBranch !== $task->getNavBranch());
	
		return $task;
	}
	
	private function buildUrl(UrlBuildTask $task) {
		if (null !== ($url = $task->getUrl())) {
			return $url;
		}

		$navBranch = $task->getNavBranch();
		$n2nLocale = $task->getN2nLocale();
		$leaf = $navBranch->getLeafByN2nLocale($n2nLocale);
		
		$subsystemName = $leaf->getSubsystemName();
		$ssl = null;
		if ($this->pageConfig->isSslSelectable()) {
			$ssl = $leaf->isSsl();
		}
		
		$pathParts = array();
		
		if (!$leaf->isHome()) {
			$pathParts[] = $leaf->getPathPart();
			
			$aNavBranch = $navBranch;
			while (null !== ($aNavBranch = $aNavBranch->getParent())) {
				if (!$aNavBranch->isInPath()) continue;
	
				$aLeaf = $aNavBranch->getLeafByN2nLocale($n2nLocale);
	
				if ($aLeaf->isHome()) continue;
				
				$pathParts[] = $aLeaf->getPathPart();
			}
		}
		
		if ($this->pageConfig->areN2nLocaleUrlsActive() 
				&& !($leaf->isHome() && $n2nLocale->equals($this->httpContext->getMainN2nLocale())
						&& ($this->pathExt === null || $this->pathExt->isEmpty()))) {
			$pathParts[] = $this->httpContext->n2nLocaleToHttpId($n2nLocale);
		}
		
		return $this->httpContext->buildContextUrl($ssl, $subsystemName, $this->absolute)
				->pathExt(array_reverse($pathParts), $this->pathExt);
	}
	
	
}

class UrlBuildTask {
	private $navBranches;
	private $n2nLocale;
	
	private $url;
	
	public function __construct(NavBranch $navBranch, N2nLocale $n2nLocale) {
		$this->navBranches[spl_object_hash($navBranch)] = $navBranch;
		$this->n2nLocale = $n2nLocale;
	}
	
	public function overwriteNavBranch(NavBranch $navBranch) {
		$this->navBranches[spl_object_hash($navBranch)] = $navBranch;
	}
	
	public function overwriteUrl(Url $url) {
		$this->url = $url;		
	}
	
	/**
	 * @return \n2n\util\uri\Url
	 */
	public function getUrl() {
		return $this->url;
	}
	
	/**
	 * @return NavBranch
	 */
	public function getNavBranch(): NavBranch {
		return end($this->navBranches);
	}
	
	public function getN2nLocale() {
		return $this->n2nLocale;
	}
	
}