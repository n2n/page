<?php
namespace page\ui;

use page\model\PageState;
use n2n\web\ui\view\View;
use n2n\reflection\CastUtils;
use n2n\core\config\GeneralConfig;
use page\model\nav\murl\MurlPage;
use page\model\nav\NavBranch;
use page\model\nav\NavTree;

/**
 * PageHtmlBuilderMeta provides non-html meta information to your views. You can access it over 
 * {@link PageHtmlBuilder::meta()}. Like {@link PageHtmlBuilder} it looks up {@link \page\model\PageState} 
 * to determine the current page.
 */
class PageHtmlBuilderMeta {
	private $view;
	private $pageState;
	
	public function __construct(View $view) {
		$this->view = $view;
		$this->pageState = $view->lookup(PageState::class);
		CastUtils::assertTrue($this->pageState instanceof PageState);
	}
	
	/**
	 * @return \page\model\PageState
	 */
	public function getPageState() {
		return $this->pageState;
	}
	
	/**
	 * @return NavTree 
	 */
	public function getNavTree() {
		return $this->pageState->getNavTree();
	}
	
	/**
	 * Returns true if there is a current page.
	 * 
	 * @see PageState::hasCurrent()
	 * @return boolean
	 */
	public function hasCurrent() {
		return $this->pageState->hasCurrent();
	}
	
	/**
	 * Returns the title of the current page or the page name specified in app.ini if there is no 
	 * current page.
	 * 
	 * @return string
	 */
	public function getTitle() {
		if ($this->pageState->hasCurrent()) {
			return $this->pageState->getCurrentLeaf()->getTitle();
		}
		
		return $this->view->lookup(GeneralConfig::class)->getPageName();
	}
	
	
	/**
	 * Combines {@link self::applySeMeta()} and {@link self::applyN2nLocaleMeta()} 
	 * 
	 * @param string $titleSeparator See {@link self::applySeMeta()}
	 */
	public function applyMeta(string $titleSeparator = self::DEFAULT_TITLE_SEPARATOR) {
		$this->applySeMeta($titleSeparator);
		$this->applyN2nLocaleMeta();
	}
	
	const DEFAULT_TITLE_SEPARATOR = ' - ';
	
	/**
	 * <p>Applies meta information specified for the current page to the html header (e. g. <code>&lt;title&gt;</code> 
	 * or <code>&lt;meta name=&quot;description&quot; content=&quot;..&quot; /&gt;</code>) </p>
	 * 
	 * <p>If there is no current page nothing happens.</p>
	 * 
	 * @param string $titleSeparator The separator used to join the page title and the page name specified 
	 * in app.ini together for <code>&lt;title&gt;</code> element.
	 */
	public function applySeMeta(string $titleSeparator = self::DEFAULT_TITLE_SEPARATOR) {
		if (!$this->pageState->hasCurrent()) return;
		
		$leafContent = $this->pageState->getCurrentLeafContent();
		$htmlMeta = $this->view->getHtmlBuilder()->meta();
	
		$seTitle = $leafContent->getSeTitle();
		if ($seTitle === null) {
			$seTitle = $leafContent->getLeaf()->getName() . $titleSeparator 
					. $this->view->lookup(GeneralConfig::class)->getPageName();
		}
		$htmlMeta->setTitle($seTitle);
	
		if (null !== ($seDescription = $leafContent->getSeDescription())) {
			$htmlMeta->addMeta(array('name' => 'description', 'content' => $seDescription));
		}
	
		if (null !== ($seKeywords = $leafContent->getSeKeywords())) {
			$htmlMeta->addMeta(array('name' => 'keywords', 'content' => $seKeywords));
		}
	}
	
	/**
	 * Applies a <code>&lt;link rel=&quot;alternate&quot; hreflang=&quot;de&quot; href=&quot;..&quot; /&gt;</code> 
	 * element to the html header for every translation available for current page.
	 */
	public function applyN2nLocaleMeta() {
		if (!$this->pageState->hasCurrent()) return;
		$navBranch = $this->pageState->getCurrentNavBranch();
		
		$leafs = $navBranch->getLeafs();
		if (count($leafs) <= 1) return;
		
		$htmlMeta = $this->view->getHtmlBuilder()->meta();
		
		foreach ($leafs as $leaf) {
			$n2nLocale = $leaf->getN2nLocale();
			
			if (null !== ($href = $this->view->buildUrl(MurlPage::obj($leaf), false))) {
				$htmlMeta->addLink(array('rel' => 'alternate', 
						'hreflang' => $this->view->getHttpContext()->n2nLocaleToHttpId($n2nLocale), 
						'href' => $href));
			}
		}
	}
	
	/**
	 * <p>Returns the Urls to every translation of the current page. If it is no translation available
	 * for a locale, a translation of its ancestors will be used. If they don't have a translation for this locale,
	 * the locale will be skiped.</p>
	 * 
	 * <p>If there is no current page, an empty array will be returned.</p>
	 * 
	 * <p>You can use this method to build a customized locale switch.
	 * 
	 * <pre>
	 * &lt;ul&gt;
	 * 	&lt;?php foreach ($pageHtml-&gt;meta()-&gt;getN2nLocaleSwitchUrls() as $n2nLocaleId =&gt; $url): ?&gt;
	 * 		&lt;?php $n2nLocale = N2nLocale::create($n2nLocaleId) ?&gt;
	 * 		&lt;li&lt;?php $view-&gt;out($n2nLocale-&gt;equals($view-&gt;getN2nLocale()) ? &#39; class=&quot;active&quot;&#39; : &#39;&#39;) ?&gt;&gt;
	 * 			&lt;?php $html-&gt;link($url, $n2nLocale-&gt;getName($view-&gt;getN2nLocale())) ?&gt;
	 * 		&lt;/li&gt;
	 * 	&lt;?php endforeach ?&gt;
	 * &lt;/ul&gt;
	 * </pre>
	 * </p>
	 * 
	 * @return \n2n\util\uri\Url[] The array key is the associated locale id. 
	 */
	public function getN2nLocaleSwitchUrls() {
		if (!$this->pageState->hasCurrent()) return array();
		
		$pageMurl = MurlPage::obj($this->pageState->getCurrentNavBranch())->fallback();
		
		$urls = array();
		foreach ($this->view->getHttpContext()->getContextN2nLocales() as $n2nLocale) {
			if (null !== ($url = $this->view->buildUrl($pageMurl->locale($n2nLocale), false))) {
				$urls[$n2nLocale->getId()] = $url; 
			}
		}
		return $urls;
	}
	
	/**
	 * Returns the {@link \rocket\spec\ei\component\field\impl\ci\model\ContentItem}s of to the current page
	 * which have been assigned to the panel with the passed name.
	 * 
	 * @param string $panelName
	 * @return \rocket\spec\ei\component\field\impl\ci\model\ContentItem[]
	 * @throws \page\model\nav\UnknownContentItemPanelException if there is no panel with passed name defined.   
	 */
	public function getContentItems(string $panelName) {
		return $this->pageState->getCurrentLeafContent()->getContentItemsByPanelName($panelName);
	}
	
	/**
	 * <p>Returns the {@link NavBranch}es of the current page and all its ancestors. You can use this method to build
	 * a customized breadcrumb navigation.
	 * 
	 * <pre>
	 * &lt;ul&gt;
	 * 	&lt;?php foreach ($pageHtml-&gt;meta()-&gt;getBreadcrumbNavBranches() as $navBranch): ?&gt;
	 * 		&lt;li&gt;&lt;?php $html-&gt;link(MurlPage::obj($navBranch)) ?&gt;
	 * 	&lt;?php endforeach ?&gt;
	 * &lt;/ul&gt;
	 * </pre>
	 * </p>
	 * 
	 * @return NavBranch[]
	 */
	public function getBreadcrumbNavBranches() {
		if (!$this->pageState->hasCurrent()) return array();
		
		$navBranches = array();
		$navBranch = $this->pageState->getCurrentNavBranch();
		do {
			$navBranches[] = $navBranch;
		} while (null !== ($navBranch = $navBranch->getParent()));
		return $navBranches;
	}
}