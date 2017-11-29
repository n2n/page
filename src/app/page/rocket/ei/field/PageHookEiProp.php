<?php
namespace page\rocket\ei\field;

use rocket\spec\ei\manage\util\model\Eiu;
use n2n\reflection\CastUtils;
use n2n\impl\web\dispatch\mag\model\EnumMag;
use n2n\web\dispatch\mag\Mag;
use rocket\spec\ei\component\field\indepenent\EiPropConfigurator;
use rocket\spec\ei\component\field\impl\enum\EnumEiProp;
use page\rocket\ei\field\conf\PageHookEiPropConfigurator;

class PageHookEiProp extends EnumEiProp {

	public function createEiPropConfigurator(): EiPropConfigurator {
		return new PageHookEiPropConfigurator($this);
	}
	
	public function isMandatory(Eiu $eiu): bool {
		return false;
	}
	
	public function createMag(Eiu $eiu): Mag {
		$mag = parent::createMag($eiu);
		CastUtils::assertTrue($mag instanceof EnumMag);
		
		if (null !== ($characteristicsKey = $eiu->entry()->getEiMapping()->getValue($this))){
			$mag->setOptions(array_merge(array($characteristicsKey => $characteristicsKey), $mag->getOptions()));
		}
		
		return $mag;
	}
}