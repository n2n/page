<?php
namespace page\rocket\ei\field;

use rocket\spec\ei\component\field\impl\bool\BooleanEiProp;
use rocket\spec\ei\component\field\indepenent\EiPropConfigurator;
use page\rocket\ei\field\conf\PageSslEiPropConfigurator;
use rocket\spec\ei\component\field\impl\adapter\DisplaySettings;

class PageSslEiProp extends BooleanEiProp {
	
	public function getTypeName(): string {
		return 'Ssl ScriptField (Page)';
	}
	
	public function setDisplaySettings(DisplaySettings $displaySettings) {
		$this->displaySettings = $displaySettings;
	}
	
	public function createEiPropConfigurator(): EiPropConfigurator {
		return new PageSslEiPropConfigurator($this);
	}
}