<?php
namespace page\model\nav;

use n2n\http\controller\ControllerContext;

interface LeafContent {
	
	/**
	 * @return \page\model\nav\Leaf
	 */
	public function getLeaf(): Leaf;
	
	/**
	 * @throws IllegalStateException
	 * @return \n2n\http\controller\ControllerContext
	 */
	public function getControllerContext(): ControllerContext;
	
	/**
	 * @return string 
	 */
	public function getSeTitle();
	
	/**
	 * @return string 
	 */
	public function getSeDescription();
	
	/**
	 * @return string 
	 */
	public function getSeKeywords();
	
	
	/**
	 * @param string $panelName
	 * @return bool
	 */
	public function containsContentItemPanelName(string $panelName): bool;
	
	/**
	 * @param string $panelName
	 * @throws UnknownContentItemPanelException
	 */
	public function getContentItemsByPanelName(string $panelName): array;
}