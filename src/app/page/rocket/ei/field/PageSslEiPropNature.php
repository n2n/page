<?php
namespace page\rocket\ei\field;

use rocket\impl\ei\component\prop\bool\BooleanEiProp;
use rocket\op\ei\component\prop\indepenent\EiPropConfigurator;
use page\rocket\ei\field\conf\PageSslEiPropConfigurator;
use rocket\impl\ei\component\prop\adapter\config\DisplayConfig;
use rocket\impl\ei\component\prop\bool\BooleanEiPropNature;
use n2n\reflection\property\PropertyAccessProxy;

class PageSslEiPropNature extends BooleanEiPropNature {

	function __construct(PropertyAccessProxy $propertyAccessProxy, string $label) {
		parent::__construct($propertyAccessProxy);
		$this->setLabel($label);
	}

	public function getTypeName(): string {
		return 'Ssl ScriptField (Page)';
	}
	
	public function setDisplayConfig(DisplayConfig $displayConfig) {
		$this->displayConfig = $displayConfig;
	}
	
// 	public function createEiPropConfigurator(): EiPropConfigurator {
// 		return new PageSslEiPropConfigurator($this);
// 	}
}