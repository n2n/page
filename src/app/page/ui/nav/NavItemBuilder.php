<?php
namespace page\ui\nav;

use n2n\ui\view\impl\html\HtmlElement;
use page\model\nav\Leaf;
use n2n\ui\view\impl\html\HtmlView;

/**
 * Used by {@link NavComposer} to build navigation html components.
 *
 */
interface NavItemBuilder {
	const INFO_CURRENT = 1;
	const INFO_OPEN = 2;
	
	/**
	 * @param HtmlView $view
	 * @param unknown $level
	 * @param array $attrs
	 * @return \n2n\ui\view\impl\html\HtmlElement
	 */
	public function buildRootUl(HtmlView $view, $level, array $attrs): HtmlElement;
	
	/**
	 * @param HtmlView $view
	 * @param Leaf $parentLeaf
	 * @param array $attrs
	 * @param int $parentInfos
	 * @return \n2n\ui\view\impl\html\HtmlElement
	 */
	public function buildUl(HtmlView $view, Leaf $parentLeaf, array $attrs, int $parentInfos): HtmlElement;
	
	/**
	 * @param HtmlView $view
	 * @param Leaf $leaf
	 * @param array $attrs
	 * @param int $infos
	 * @return \n2n\ui\view\impl\html\HtmlElement
	 */
	public function buildLi(HtmlView $view, Leaf $leaf, array $attrs, int $infos): HtmlElement;
}