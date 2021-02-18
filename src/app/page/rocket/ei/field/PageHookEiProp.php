<?php
namespace page\rocket\ei\field;

use page\rocket\ei\field\conf\PageHookKeyConfig;
use rocket\impl\ei\component\prop\enum\EnumEiProp;

class PageHookEiProp extends EnumEiProp {

// 	public function createEiPropConfigurator(): EiPropConfigurator {
// 		return new PageHookEiPropConfigurator($this);
// 	}
	
	function prepare() {
		$this->getConfigurator()->removeAdaption($this->getEnumConfig());
		$this->getConfigurator()->addAdaption(new PageHookKeyConfig($this->getEnumConfig()));
	}
	
// 	public function createMag(Eiu $eiu): Mag {
// 		$mag = parent::createMag($eiu);
// 		CastUtils::assertTrue($mag instanceof EnumMag);
		
// 		if (null !== ($characteristicsKey = $eiu->entry()->getValue($this))){
// 			$mag->setOptions(array_merge(array($characteristicsKey => $characteristicsKey), $mag->getOptions()));
// 		}
		
// 		return $mag;
// 	}
}