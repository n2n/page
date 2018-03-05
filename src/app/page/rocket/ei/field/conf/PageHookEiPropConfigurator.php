<?php
namespace page\rocket\ei\field\conf;

use rocket\ei\component\EiSetupProcess;
use n2n\reflection\CastUtils;
use page\config\PageConfig;
use rocket\impl\ei\component\prop\adapter\AdaptableEiPropConfigurator;
use page\rocket\ei\field\PageHookEiProp;

class PageHookEiPropConfigurator extends AdaptableEiPropConfigurator {
	private $pageHookEiField;
	
	public function __construct(PageHookEiProp $pageHookEiField) {
		parent::__construct($pageHookEiField);
		$this->autoRegister();
		$this->pageHookEiField = $pageHookEiField;
	}
	
	public function getTypeName(): string {
		return 'Hooks Ei Field (Page)';
	}
	
	public function setup(EiSetupProcess $eiSetupProcess) {
		parent::setup($eiSetupProcess);
		
		$pageConfig = $eiSetupProcess->getN2nContext()->getModuleConfig('page');
		CastUtils::assertTrue($pageConfig instanceof PageConfig);
		
		
//		@todo later
// 		$pageDao = $eiSetupProcess->getN2nContext()->lookup('page\model\PageDao');
// 		$pageDao instanceof PageDao;
		
// 		$choicesMap = array();
// 		foreach ($pageConfig->getCharacteristicKeys() as $characteristicKey) {
// 			if (null !== $pageDao->getPageByCharacteristicKey($characteristicKey)) continue;
// 			$choicesMap[$characteristicKey] = $characteristicKey;
// 		}
		
		$this->pageHookEiField->setOptions($pageConfig->getHooks());
	}
}