<?php
namespace page\bo;

use n2n\reflection\ObjectAdapter;
use page\model\PageMonitor;
use n2n\context\ThreadScoped;
use n2n\context\RequestScoped;

class PageEntityListener extends ObjectAdapter implements RequestScoped {
	
	public function _postInsert(PageMonitor $pageMonitor) {
		$pageMonitor->flush();
	}
	
	public function _postUpdate(PageMonitor $pageMonitor) {
		$pageMonitor->flush();
	}
	
	public function _postRemove(PageMonitor $pageMonitor) {
		$pageMonitor->flush();
	}
}