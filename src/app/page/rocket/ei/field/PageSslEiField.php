<?php
namespace page\rocket\ei\field;

use rocket\spec\ei\component\field\impl\bool\BooleanEiField;
use rocket\spec\ei\manage\gui\DisplayDefinition;
use rocket\spec\ei\component\field\indepenent\EiFieldConfigurator;
use page\rocket\ei\field\conf\PageSslEiFieldConfigurator;

class PageSslEiField extends BooleanEiField {
	
	public function getTypeName(): string {
		return 'Ssl ScriptField (Page)';
	}
	
	public function setDisplayDefinition(DisplayDefinition $displayDefinition) {
		$this->displayDefinition = $displayDefinition;
	}
	
	public function createEiFieldConfigurator(): EiFieldConfigurator {
		return new PageSslEiFieldConfigurator($this);
	}
}