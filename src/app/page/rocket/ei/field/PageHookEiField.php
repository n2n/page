<?php
namespace page\rocket\ei\field;

use rocket\spec\ei\component\field\impl\enum\EnumEiField;
use rocket\spec\ei\manage\util\model\Eiu;
use page\rocket\ei\field\conf\PageHookEiFieldConfigurator;
use rocket\spec\ei\component\field\indepenent\EiFieldConfigurator;
use n2n\reflection\CastUtils;
use n2n\impl\web\dispatch\mag\model\EnumMag;
use n2n\web\dispatch\mag\Mag;

class PageHookEiField extends EnumEiField {

	public function createEiFieldConfigurator(): EiFieldConfigurator {
		return new PageHookEiFieldConfigurator($this);
	}
	
	public function isMandatory(Eiu $eiu): bool {
		return false;
	}
	
	public function createMag(string $propertyName, Eiu $eiu): Mag {
		$mag = parent::createMag($propertyName, $eiu);
		CastUtils::assertTrue($mag instanceof EnumMag);
		
		if (null !== ($characteristicsKey = $eiu->entry()->getEiMapping()->getValue($this))){
			$mag->setOptions(array_merge(array($characteristicsKey => $characteristicsKey), $mag->getOptions()));
		}
		
		return $mag;
	}
}