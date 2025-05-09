<?php
namespace page\rocket\ei\field;

use rocket\op\ei\util\Eiu;
use rocket\impl\ei\component\prop\enum\EnumEiPropNature;
use n2n\reflection\property\PropertyAccessProxy;
use rocket\op\ei\manage\gui\EiGuiProp;

class PageSubsystemEiPropNature extends EnumEiPropNature {

	function __construct(PropertyAccessProxy $propertyAccessProxy, string $label, array $options) {
		parent::__construct($propertyAccessProxy);
		$this->setLabel($label);
		$this->setOptions($options);
	}
	
//	function buildEiGuiProp(Eiu $eiu): ?EiGuiProp {
//		$subsystems = $eiu->getN2nContext()->getHttpContext()->getAvailableSubsystems();
//		if (empty($subsystems)) {
//			return null;
//		}
//
//		return parent::buildEiGuiProp($eiu);
//	}
//
//
	
// 	public function createEiPropConfigurator(): EiPropConfigurator {
// 		return new PageSubsystemEiPropConfigurator($this);
// 	}
	
// 	public function buildDisplayDefinition(Eiu $eiu): ?DisplayDefinition {
// 		if (1 == count($this->getOptions())) return null;
		
// 		return parent::buildDisplayDefinition($eiu);
// 	}
	
// 	public function createMag(Eiu $eiu): Mag {
// 		$enumMag = parent::createMag($eiu);
// 		CastUtils::assertTrue($enumMag instanceof EnumMag);
		
// 		if ($eiu->entry()->isNew()) {
// 			return $enumMag;
// 		}
		
// 		$attrs = [];
// 		$attrs['class'] = 'rocket-critical-input';
// 		$dtc = new DynamicTextCollection('page', $eiu->frame()->getN2nLocale());
// 		$attrs['data-confirm-message'] = $dtc->translate('field_subsystem_unlock_confirm');
// 		$attrs['data-edit-label'] =  $dtc->translate('common_edit_label');
// 		$attrs['data-cancel-label'] =  $dtc->translate('common_cancel_label');
// 		$enumMag->setInputAttrs($attrs);
		
// 		return $enumMag;
// 	}
}