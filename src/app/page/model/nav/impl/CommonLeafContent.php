<?php
namespace page\model\nav\impl;

use page\model\nav\LeafContent;
use page\model\nav\Leaf;
use n2n\util\uri\Path;
use n2n\http\controller\Controller;
use n2n\http\controller\ControllerContext;
use page\model\nav\UnknownContentItemPanelException;

class CommonLeafContent implements LeafContent {
	private $controller;
	private $controllerContext;
	private $seTitle;
	private $seDescription;
	private $seKeywords;
	
	public function __construct(Leaf $leaf, Path $cmdPath, Path $cmdContextPath, Controller $controller) {
		$this->leaf = $leaf;
		$this->controllerContext = new ControllerContext($cmdPath, $cmdContextPath, $controller);
	}
	
	/**
	 * @return \page\model\nav\Leaf
	 */
	public function getLeaf(): Leaf {
		return $this->leaf;
	}
	
	/**
	 * @throws IllegalStateException
	 * @return \n2n\http\controller\ControllerContext
	 */
	public function getControllerContext(): ControllerContext {
		return $this->controllerContext;
	}
	
	public function getSeTitle() {
		return $this->seTitle;
	}
	
	public function setSeTitle(string $seTitle = null) {
		$this->seTitle = $seTitle;
	}
	
	public function getSeDescription() {
		return $this->seDescription;
	}
	
	public function setSeDescription(string $seDescription = null) {
		$this->seDescription = $seDescription;
	}
	
	public function getSeKeywords() {
		return $this->seKeywords;
	}
	
	public function setSeKeywords(string $seKeywords = null) {
		$this->seKeywords = $seKeywords;
	}
	
	public function containsContentItemPanelName(string $panelName): bool {
		return false;
	}

	public function getContentItemsByPanelName(string $panelName): array {
		throw new UnknownContentItemPanelException();
	}
	
}