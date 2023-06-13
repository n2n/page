<?php
namespace page\rocket\ei\field;

use n2n\reflection\property\PropertyAccessProxy;
use rocket\impl\ei\component\prop\enum\EnumEiPropNature;

class PageHookEiPropNature extends EnumEiPropNature {

	function __construct(PropertyAccessProxy $propertyAccessProxy, string $label, array $options) {
		parent::__construct($propertyAccessProxy);
		$this->setLabel($label);
		$this->setOptions($options);
	}

// 	public function createEiPropConfigurator(): EiPropConfigurator {
// 		return new PageHookEiPropConfigurator($this);
// 	}
	
//	function prepare() {
//		$this->getConfigurator()->removeAdaption($this->getEnumConfig());
//		$this->getConfigurator()->addAdaption(new PageHookKeyConfig($this->getEnumConfig()));
//	}
	
// 	public function createMag(Eiu $eiu): Mag {
// 		$mag = parent::createMag($eiu);
// 		CastUtils::assertTrue($mag instanceof EnumMag);
		
// 		if (null !== ($characteristicsKey = $eiu->entry()->getValue($this))){
// 			$mag->setOptions(array_merge(array($characteristicsKey => $characteristicsKey), $mag->getOptions()));
// 		}
		
// 		return $mag;
// 	}
}