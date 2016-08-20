<?php
namespace page\rocket\ei\field;

use rocket\spec\ei\component\field\impl\enum\EnumEiField;
use rocket\spec\ei\manage\gui\FieldSourceInfo;
use page\rocket\ei\field\conf\PageHookEiFieldConfigurator;
use rocket\spec\ei\component\field\indepenent\EiFieldConfigurator;
use n2n\reflection\CastUtils;
use n2n\impl\web\dispatch\mag\model\EnumMag;
use n2n\web\dispatch\mag\Mag;

class PageHookEiField extends EnumEiField {

	public function createEiFieldConfigurator(): EiFieldConfigurator {
		return new PageHookEiFieldConfigurator($this);
	}
	
	public function isMandatory(FieldSourceInfo $fieldSourceInfo): bool {
		return false;
	}
	
	public function createMag(string $propertyName, FieldSourceInfo $entrySourceInfo): Mag {
		$mag = parent::createMag($propertyName, $entrySourceInfo);
		CastUtils::assertTrue($mag instanceof EnumMag);
		
		if (null !== ($characteristicsKey = $entrySourceInfo->getEiMapping()->getValue($this))){
			$mag->setOptions(array_merge(array($characteristicsKey => $characteristicsKey), $mag->getOptions()));
		}
		
		return $mag;
	}
}