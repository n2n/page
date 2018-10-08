<?php
namespace page\rocket\ei\modificator;

use rocket\impl\ei\component\modificator\adapter\IndependentEiModificatorAdapter;
use rocket\ei\util\Eiu;
use n2n\core\config\WebConfig;
use page\model\PageDao;
use n2n\reflection\CastUtils;
use page\bo\PageT;
use rocket\ei\EiPropPath;
use n2n\l10n\MessageCode;
use n2n\l10n\Message;

class PageTEiModificator extends IndependentEiModificatorAdapter  {

	public function setupGuiDefinition(Eiu $eiu) { 
		if (1 >= count($eiu->lookup(WebConfig::class)->getAllN2nLocales())) {
			$eiu->engine()->removeGuiProp('active');
		}	
	}
	
	public function setupEiEntry(Eiu $eiu) {
		$eiuEntry = $eiu->entry();
		
		$eiuEntry->onValidate(function (PageDao $pageDao) use ($eiuEntry) {
			if (!$eiuEntry->getValue('home')) return;
			
			$pageT = $eiuEntry->getEntityObj();
			CastUtils::assertTrue($pageT instanceof PageT);
			$n2nLocale = $eiuEntry->getValue('n2nLocale');
			$subsystemName = null;
			
			if (null !== ($page = $pageT->getPage())) {
				$subsystemName = $page->getSubsystemName();
			}
			
			$homePageT = $pageDao->getHomePageTExcept($n2nLocale, $subsystemName, $pageT);
			
			
			test(spl_object_hash($eiuEntry->getEiEntry()));
			$fieldErrorInfo = $eiuEntry->getEiEntry()->getValidationResult()->getEiFieldValidationResult(EiPropPath::create('home'));
			$fieldErrorInfo->addError(new MessageCode('home_already_exists_txt', ['current_home' => $homePageT->getRealTitle()],
					 Message::SEVERITY_ERROR, 'page'));
		});
	}
}