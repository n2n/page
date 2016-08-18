<?php
namespace page\model\nav\murl;

use n2n\http\nav\Murlable;
use n2n\l10n\N2nLocale;
use n2n\core\container\N2nContext;
use n2n\http\controller\ControllerContext;
use page\model\nav\NavBranch;
use page\model\nav\UnknownNavBranchException;
use n2n\http\nav\UnavailableMurlException;
use page\model\nav\NavUrlBuilder;
use page\model\nav\BranchUrlBuildException;
use n2n\reflection\CastUtils;
use page\model\PageState;
use n2n\util\uri\Url;
use page\model\NavBranchCriteria;

/**
 * A PageMurlComposer is created by {@link PageMurl} and can be used like a 
 * {@link \n2n\http\nav\MurlComposer} to build urls to pages in a fluid way.
 */
class PageMurlComposer implements Murlable {
	private $navBranchCriteria;
	
	private $fallbackAllowed = false;
	private $n2nLocale;
	private $pathExts = array();
	private $queryExt;
	private $fragment;
	private $ssl;
	private $subsystem;
	private $absolute = false;
	private $accessiblesOnly = true;

	/**
	 * Use {@link PageMurl} to create a PageMurlComposer. Don't call this constructor manually.
	 * 
	 * @param NavBranchCriteria $navBranchCriteria
	 */
	public function __construct(NavBranchCriteria $navBranchCriteria) {
		$this->navBranchCriteria = $navBranchCriteria;
	}

	/**
	 * Specifies to which translation of target page the url will be build.
	 * 
	 * @param mixed $n2nLocale N2nLocale or locale id as string of the desired translation. Resetable with null.
	 * @return \page\model\nav\murl\PageMurlComposer
	 */
	public function locale($n2nLocale) {
		$this->n2nLocale = N2nLocale::build($n2nLocale);
		return $this;
	}
	
	/**
	 * <p>If true and the target page is not availablethe one of its ancestor pages will be used as fallback.</p>
	 * 
	 * <p>Default is false.</p>
	 * 
	 * @param bool $fallbackAllowed
	 * @return \page\model\nav\murl\PageMurlComposer
	 */
	public function fallback(bool $fallbackAllowed = true) {
		$this->fallbackAllowed = $fallbackAllowed;
		return $this;
	}

	/**
	 * Extends the url to the target page with passed paths. This method behaves like 
	 * {@link \n2n\util\uri\Path::ext()}.
	 * 
	 * @param mixed $pathExts
	 * @return \page\model\nav\murl\PageMurlComposer
	 */
	public function pathExt(...$pathPartExts): PageMurlComposer {
		$this->pathExts[] = $pathPartExts;
		return $this;
	}
	
	/**
	 * Extends the url to the target page with passed paths. This method behaves like
	 * {@link \n2n\util\uri\Path::extEnc()}.
	 *
	 * @param mixed $pathExts
	 * @return \page\model\nav\murl\PageMurlComposer
	 */
	public function pathExtEnc(...$pathExts) {
		$this->pathExts[] = array_merge($this->pathExts, $pathExts);
	}
	
	/**
	 * Extends the url to the target page with passed query. This method behaves like 
	 * {@link \n2n\util\uri\Query::ext()}.
	 * 
	 * @param mixed $queryExt
	 * @return \page\model\nav\murl\PageMurlComposer
	 */
	public function queryExt($queryExt): PageMurlComposer {
		$this->queryExt = $queryExt;
		return $this;
	}

	/**
	 * Defines the fragment of the url to the target page.
	 * 
	 * @param string $fragment
	 * @return \page\model\nav\murl\PageMurlComposer
	 */
	public function fragment(string $fragment): PageMurlComposer {
		$this->fragment = $fragment;
		return $this;
	}

	/**
	 * <p>If true and the url will be absolute.</p>
	 * 
	 * <p>Default is false.</p>
	 *
	 * @param string $absolute
	 * @return \page\model\nav\murl\PageMurlComposer
	 */
	public function absolute(bool $absolute = true): PageMurlComposer {
		$this->absolute = $absolute;
		return $this;
	}
	
	public function inaccessibles(bool $includeInaccessibles = true) {
		$this->accessiblesOnly = !$includeInaccessibles;
		return $this;
	}
	
	/**
	 * {@inheritDoc}
	 * @see \n2n\http\nav\Murlable::toUrl($n2nContext, $controllerContext)
	 */
	public function toUrl(N2nContext $n2nContext, ControllerContext $controllerContext = null, 
			string &$suggestedLabel = null): Url {
		$pageState = $n2nContext->lookup(PageState::class);
		CastUtils::assertTrue($pageState instanceof PageState);

		$n2nLocale = $this->n2nLocale;
		if ($n2nLocale === null){
			$n2nLocale = $n2nContext->getN2nLocale();
		}
		$navBranch = null;
		try {
			$navBranch = $this->navBranchCriteria->determine($pageState, $n2nLocale, $n2nContext);
		} catch (UnknownNavBranchException $e) {
			throw new UnavailableMurlException(false, null, null, $e);
		}

		$navUrlBuilder = new NavUrlBuilder($n2nContext->getHttpContext());
		$navUrlBuilder->setFallbackAllowed($this->fallbackAllowed);
		$navUrlBuilder->setAbsolute($this->absolute);
		$navUrlBuilder->setAccessiblesOnly($this->accessiblesOnly);
		$url = null;
		try {
			$url = $navUrlBuilder->build($navBranch, $n2nLocale, true);
			$suggestedLabel = $navBranch->getLeafByN2nLocale($n2nLocale)->getName();
		} catch (BranchUrlBuildException $e) {
			throw new UnavailableMurlException(false, 'NavBranch not available for locale: ' . $n2nLocale, 0, $e);
		}

		return $url->pathExtEnc(...$this->pathExts)->queryExt($this->queryExt)->chFragment($this->fragment);
	}
}