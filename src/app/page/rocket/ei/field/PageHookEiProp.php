<?php
namespace page\rocket\ei\field;

use rocket\ei\util\Eiu;
use n2n\util\type\CastUtils;
use n2n\impl\web\dispatch\mag\model\EnumMag;
use rocket\impl\ei\component\prop\enum\EnumEiProp;
use rocket\si\content\SiField;
use page\config\PageConfig;

class PageHookEiProp extends EnumEiProp {

	function prepare() {
		$this->getConfigurator()->addSetupCallback(function (Eiu $eiu) {
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
			
			$this->getEnumConfig()->setOptions($pageConfig->getHooks());
		});
	}
	
	public function isMandatory(Eiu $eiu): bool {
		return false;
	}
	
	public function createInSiField(Eiu $eiu): SiField {
		$mag = parent::createMag($eiu);
		CastUtils::assertTrue($mag instanceof EnumMag);
		
		if (null !== ($characteristicsKey = $eiu->entry()->getValue($this))){
			$mag->setOptions(array_merge(array($characteristicsKey => $characteristicsKey), $mag->getOptions()));
		}
		
		return $mag;
	}
}