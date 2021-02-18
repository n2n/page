<?php
namespace page\rocket\ei\field;

use rocket\impl\ei\component\prop\enum\EnumEiProp;
use page\rocket\ei\field\conf\PageSubsystemConfig;
use rocket\ei\util\Eiu;
use rocket\ei\manage\gui\GuiProp;

class PageSubsystemEiProp extends EnumEiProp {
	
	function prepare() {
		$this->getConfigurator()->removeAdaption($this->getEnumConfig());
		$this->getConfigurator()->addAdaption(new PageSubsystemConfig($this->getEnumConfig()));
	}
	
	function buildGuiProp(Eiu $eiu):?GuiProp {
		$subsystems = $eiu->getN2nContext()->getHttpContext()->getAvailableSubsystems();
		if (empty($subsystems)) {
			return null;
		}
		
		return parent::buildGuiProp($eiu);
	}
	
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