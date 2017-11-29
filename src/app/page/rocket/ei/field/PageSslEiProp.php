<?php
namespace page\rocket\ei\field;

use rocket\spec\ei\component\field\impl\bool\BooleanEiProp;
use rocket\spec\ei\manage\gui\DisplayDefinition;
use rocket\spec\ei\component\field\indepenent\EiPropConfigurator;
use page\rocket\ei\field\conf\PageSslEiPropConfigurator;

class PageSslEiProp extends BooleanEiProp {
	
	public function getTypeName(): string {
		return 'Ssl ScriptField (Page)';
	}
	
	public function setDisplayDefinition(DisplayDefinition $displayDefinition) {
		$this->displayDefinition = $displayDefinition;
	}
	
	public function createEiPropConfigurator(): EiPropConfigurator {
		return new PageSslEiPropConfigurator($this);
	}
}