<?php
namespace page\ui\nav\impl;

use page\ui\nav\NavItemBuilder;
use n2n\impl\web\ui\view\html\HtmlElement;
use n2n\impl\web\ui\view\html\HtmlUtils;
use page\model\nav\Leaf;
use n2n\impl\web\ui\view\html\HtmlView;
use n2n\web\ui\UiComponent;
use page\model\nav\murl\MurlPage;

abstract class NavItemBuilderAdapter implements NavItemBuilder {
	protected $classPrefix = '';
	
	public function buildRootUl(HtmlView $view, $level, array $attrs): HtmlElement {
		$attrs = HtmlUtils::mergeAttrs(array('class' => $this->classPrefix . 'level-' . $level), $attrs);
		return new HtmlElement('ul', $attrs, '');
	}
	
	public function buildUl(HtmlView $view, Leaf $parentLeaf, array $attrs, int $infos): HtmlElement {
		$attrs = HtmlUtils::mergeAttrs(array('class' => $this->classPrefix . 'level-' . ($parentLeaf->getNavBranch()->getLevel() + 1)), $attrs);
		return new HtmlElement('ul', $attrs, '');
	}
	
	public function buildLi(HtmlView $view, Leaf $leaf, array $attrs, array $aAttrs, int $infos): HtmlElement {
		if ($leaf->isTargetNewWindow() && !($infos & self::INFO_OPEN || $infos & self::INFO_CURRENT)) {
			$aAttrs = HtmlUtils::mergeAttrs(array('target' => '_blank'), $aAttrs);
		}
		
		return new HtmlElement('li', $this->buildLiAttrs($view, $leaf, $attrs, $infos), 
				$view->getHtmlBuilder()->getLink(MurlPage::obj($leaf), 
						$this->buildLiLabel($view, $leaf, $attrs, $infos),
						$aAttrs));
	}
	
	protected function buildLiAttrs(HtmlView $view, Leaf $leaf, array $attrs, int $infos): array {
		$attrs = HtmlUtils::mergeAttrs($this->buildAdditionalAttrs($view, $leaf, $attrs, $infos), $attrs);
		
		$classNames = array($this->classPrefix . 'level-' . $leaf->getNavBranch()->getLevel());
		
		if ($leaf->getNavBranch()->hasChildren()) {
			$classNames[] = $this->classPrefix . 'has-children';
		}
		
		if ($infos & self::INFO_CURRENT) {
			$classNames[] = $this->classPrefix . 'active';
		}
		
		if ($infos & self::INFO_OPEN) {
			$classNames[] = $this->classPrefix . 'open';
		}
		
		return HtmlUtils::mergeAttrs(array('class' => implode(' ', $classNames)), $attrs);
	}
	
	protected function buildAdditionalAttrs(HtmlView $view, Leaf $leaf, array $attrs, int $infos): array {
		return array();
	}
	
	/**
	 * @param HtmlView $view
	 * @param Leaf $leaf
	 * @param array $attrs
	 * @param int $infos
	 * @return UiComponent|string
	 */
	protected function buildLiLabel(HtmlView $view, Leaf $leaf, array $attrs, int $infos) {
		return $leaf->getName();
	}
}