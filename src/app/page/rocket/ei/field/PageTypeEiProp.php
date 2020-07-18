<?php
namespace page\rocket\ei\field;

use rocket\ei\util\Eiu;
use rocket\impl\ei\component\prop\adapter\DisplayableEiPropAdapter;
use n2n\util\type\CastUtils;
use page\bo\Page;
use rocket\si\content\SiField;
use rocket\si\content\impl\SiFields;
use rocket\si\content\impl\meta\SiCrumb;

class PageTypeEiProp extends DisplayableEiPropAdapter {
	
	protected function prepare() {
	}
	
	public function createOutSiField(Eiu $eiu): SiField {
		$page = $eiu->entry()->getEntityObj();
		CastUtils::assertTrue($page instanceof Page);
		
		$iconType = null;
		$label = null;
		
		switch ($page->getType()) {
			case Page::TYPE_INTERNAL:
				$iconType = 'fa fa-link';
				$label = $page->getInternalPage()->t($view->getN2nLocale())->getName();
				break;
			case Page::TYPE_EXTERNAL:
				$iconType = 'fa fa-link';
				$label = $view->getHtmlBuilder()->getLink($page->getExternalUrl(), null, array('target' => '_blank'));
				break;
			default:
				$eiuMask = $eiu->context()->mask($page->getPageContent()->getPageController());
				$iconType = $eiuMask->getIconType();
				$label = $eiuMask->getLabel();
		}
		
		return SiFields::crumbOut(SiCrumb::createIcon($iconType), SiCrumb::createLabel($label));
	}


	
}