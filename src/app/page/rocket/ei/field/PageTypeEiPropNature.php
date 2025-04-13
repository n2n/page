<?php
namespace page\rocket\ei\field;

use rocket\op\ei\util\Eiu;
use n2n\util\type\CastUtils;
use page\bo\Page;
use rocket\op\ei\util\factory\EifGuiField;
use rocket\impl\ei\component\prop\adapter\DisplayableEiPropNatureAdapter;
use rocket\ui\gui\field\GuiField;
use rocket\ui\gui\field\BackableGuiField;
use rocket\ui\gui\field\impl\GuiFields;
use rocket\ui\si\content\impl\SiFields;
use rocket\ui\si\content\impl\meta\SiCrumb;
use rocket\op\spec\UnknownTypeException;
use page\bo\PageController;

class PageTypeEiPropNature extends DisplayableEiPropNatureAdapter {

	function __construct(string $label) {
		$this->setLabel($label);
	}

	/**
	 * @throws UnknownTypeException
	 */
	function buildOutGuiField(Eiu $eiu): ?BackableGuiField {
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
				$eiuMask = $eiu->context()->mask($page->getPageContent()?->getPageController()
						?? new \ReflectionClass(PageController::class));
				$iconType = $eiuMask->getIconType();
				$label = $eiuMask->getLabel();
		}
		
		if (null === $iconType) {
			return GuiFields::out(SiFields::stringOut($label));
		}
		
		return GuiFields::out(SiFields::crumbOut(SiCrumb::createIcon($iconType), SiCrumb::createLabel($label)));
	}


	function getGuiField(): ?GuiField {
		// TODO: Implement getGuiField() method.
	}
}