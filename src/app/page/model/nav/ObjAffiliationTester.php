<?php
namespace page\model\nav;

interface ObjAffiliationTester {
	
	/**
	 * @param object $obj
	 * @return bool
	 */
	public function isAffiliatedWith($obj): bool;
}