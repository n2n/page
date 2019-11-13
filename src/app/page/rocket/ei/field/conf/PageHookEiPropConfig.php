<?php
namespace page\rocket\ei\field\conf;

use n2n\util\type\CastUtils;
use page\config\PageConfig;
use page\rocket\ei\field\PageHookEiProp;
use rocket\ei\util\Eiu;
use n2n\util\type\attrs\DataSet;
use n2n\web\dispatch\mag\MagCollection;
use rocket\impl\ei\component\prop\adapter\config\ConfigAdaption;

class PageHookEiPropConfig extends ConfigAdaption {
	private $pageHookEiField;
	
	public function __construct(PageHookEiProp $pageHookEiField) {
		$this->pageHookEiField = $pageHookEiField;
	}
	
	public function getTypeName(): string {
		return 'Hooks Ei Field (Page)';
	}
	
	public function setup(Eiu $eiu, DataSet $dataSet) {
		$pageConfig = $eiu->getN2nContext()->getModuleConfig('page');
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
	public function mag(Eiu $eiu, DataSet $dataSet, MagCollection $magCollection) {
	}

	public function save(Eiu $eiu, MagCollection $magCollection, DataSet $dataSet) {
	}

}
