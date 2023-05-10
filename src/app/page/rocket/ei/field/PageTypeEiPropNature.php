<?php
namespace page\rocket\ei\field;

use rocket\op\ei\util\Eiu;
use n2n\util\type\CastUtils;
use page\bo\Page;
use rocket\op\ei\util\factory\EifGuiField;
use rocket\si\content\impl\SiFields;
use rocket\si\content\impl\meta\SiCrumb;
use rocket\impl\ei\component\prop\adapter\DisplayableEiPropNatureAdapter;

class PageTypeEiPropNature extends DisplayableEiPropNatureAdapter {

	function __construct(string $label) {
		$this->setLabel($label);
	}
	
	function createOutEifGuiField(Eiu $eiu): EifGuiField {
		$page = $eiu->entry()->getEntityObj();
		CastUtils::assertTrue($page instanceof Page);
		
		$iconType = null;
		$label = null;
		
		switch ($page->getType()) {
			case Page::TYPE_INTERNAL:
				$iconType = 'fa fa-link';
				$label = $page->getInternalPage()->t($eiu->getN2nLocale())->getName();
				break;
			case Page::TYPE_EXTERNAL:
				$iconType = 'fa fa-link';
// 				$label = $view->getHtmlBuilder()->getLink($page->getExternalUrl(), null, array('target' => '_blank'));
				$label = $page->getExternalUrl();
				break;
			default:
				$eiuMask = $eiu->context()->mask($page->getPageContent()->getPageController());
				$iconType = $eiuMask->getIconType();
				$label = $eiuMask->getLabel();
		}
		
		if (null === $iconType) {
			return SiFields::stringOut($label);
		}
		
		return $eiu->factory()->newGuiField(SiFields::crumbOut(SiCrumb::createIcon($iconType),
				SiCrumb::createLabel($label)));
	}

	
}