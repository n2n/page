<?php
namespace page\rocket\ei\field;

use rocket\ei\util\Eiu;
use rocket\impl\ei\component\prop\adapter\DisplayableEiPropAdapter;
use n2n\util\type\CastUtils;
use page\bo\PageT;
use page\model\PageState;
use page\model\nav\NavUrlBuilder;
use n2n\impl\web\ui\view\html\HtmlElement;
use n2n\util\StringUtils;
use page\bo\Page;
use page\model\nav\UnavailableLeafException;
use rocket\si\content\SiField;
use rocket\si\content\impl\SiFields;
use rocket\si\content\impl\meta\SiCrumb;
use rocket\ei\util\factory\EifGuiField;

class PagePathEiProp extends DisplayableEiPropAdapter {
	
	protected function prepare() {
	}
	
	
	function createOutEifGuiField(Eiu $eiu): EifGuiField {
		$pageT = $eiu->entry()->getEntityObj();
		CastUtils::assertTrue($pageT instanceof PageT);
		
		$pageState = $eiu->lookup(PageState::class);
		CastUtils::assertTrue($pageState instanceof PageState);
		
		$navBranch = $pageState->getNavTree()->find($pageT);
		if ($navBranch === null) return null;
		
		$navUrlBuilder = new NavUrlBuilder($eiu->getN2nContext()->getHttpContext());
		$navUrlBuilder->setAccessiblesOnly(false);
		$navUrlBuilder->setFallbackAllowed(false);
		
		$pathStr = null;
		try {
			$pathStr = (string) $navUrlBuilder->buildPath($navBranch, $pageT->getN2nLocale())->chLeadingDelimiter(true);
		} catch (UnavailableLeafException $e) {
			return SiFields::crumbOut(SiCrumb::createLabel($eiu->dtc('page')->t('unreachable_err'))
					->setSeverity(SiCrumb::SEVERITY_INACTIVE));
		}
		
		$siCurmb = null;
		if (mb_strlen($pathStr) <= 30) {
			$siCurmb = SiCrumb::createLabel($pathStr);
		} else {
			$siCurmb = SiCrumb::createLabel(StringUtils::reduceFront($pathStr, 30, '...'))->setTitle($pathStr);
		}
		
		if (!$pageT->isActive() || !$pageT->getPage()->isOnline()
				|| $pageT->getPage()->getType() != Page::TYPE_CONTENT) {
			$siCurmb->setSeverity(SiCrumb::SEVERITY_INACTIVE);
		}
		
		return SiFields::crumbOut();
	}
	
	
}

