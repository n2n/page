<?php
namespace page\rocket\ei\field;

use n2n\l10n\DynamicTextCollection;
use n2n\impl\web\dispatch\mag\model\EnumMag;
use rocket\impl\ei\component\field\enum\EnumEiProp;
use rocket\spec\ei\manage\util\model\Eiu;
use n2n\web\dispatch\mag\Mag;
use rocket\spec\ei\component\field\indepenent\EiPropConfigurator;
use page\rocket\ei\field\conf\PageSubsystemEiPropConfigurator;
use rocket\spec\ei\manage\gui\DisplayDefinition;
use n2n\reflection\CastUtils;

class PageSubsystemEiField extends EnumEiProp {
	
	public function getTypeName(): string {
		return 'Subsystem';
	}
		
	public function setDisplayDefinition(DisplayDefinition $displayDefinition) {
		$this->displayDefinition = $displayDefinition;
	}
	
	public function createEiPropConfigurator(): EiPropConfigurator {
		return new PageSubsystemEiPropConfigurator($this);
	}
	
	public function createMag(string $propertyName, Eiu $eiu): Mag {
		$enumMag = parent::createMag($propertyName, $eiu);
		CastUtils::assertTrue($enumMag instanceof EnumMag);
		
		if ($eiu->entry()->isNew()) {
			return $enumMag;
		}
		
		$attrs['class'] = 'rocket-critical-input';
		$dtc = new DynamicTextCollection('page', $eiu->frame()->getEiFrame()->getN2nLocale());
		$attrs['data-confirm-message'] = $dtc->translate('field_subsystem_unlock_confirm');
		$attrs['data-edit-label'] =  $dtc->translate('common_edit_label');
		$attrs['data-cancel-label'] =  $dtc->translate('common_cancel_label');
		$enumMag->setInputAttrs($attrs);
		
		return $enumMag;
	}
}