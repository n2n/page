<?php
namespace page\rocket\ei\field;

use n2n\l10n\DynamicTextCollection;
use n2n\impl\web\dispatch\mag\model\EnumMag;
use rocket\spec\ei\component\field\impl\enum\EnumEiProp;
use rocket\spec\ei\manage\util\model\Eiu;
use n2n\web\dispatch\mag\Mag;
use rocket\spec\ei\component\field\indepenent\EiPropConfigurator;
use page\rocket\ei\field\conf\PageSubsystemEiPropConfigurator;
use n2n\reflection\CastUtils;
use rocket\spec\ei\component\field\impl\adapter\DisplaySettings;

class PageSubsystemEiProp extends EnumEiProp {
	
	public function getTypeName(): string {
		return 'Subsystem';
	}
		
	public function setDisplaySettings(DisplaySettings $displaySettings) {
		$this->displaySettings = $displaySettings;
	}
	
	public function createEiPropConfigurator(): EiPropConfigurator {
		return new PageSubsystemEiPropConfigurator($this);
	}
	
	public function createMag(Eiu $eiu): Mag {
		$enumMag = parent::createMag($eiu);
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