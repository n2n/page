/**
 * 
 */

jQuery(document).ready(function ($) {
	rocketTs.registerUiInitFunction("select.page-method", function(jqSelect) {
		var methodPanelNames = jqSelect.data('panel-names');
		var jqCiContainer = jqSelect.parent().parent().parent().find("div.rocket-property.rocket-gui-field-pageControllerTs-contentItems");
		var jqCiDivs = jqCiContainer.find("div.rocket-content-items").children("div.rocket-content-item-panel");
		
		var restrictCiPanels = function () {
			var methodName = jqSelect.val();
			var panelNames = methodPanelNames[methodName];
			var display = false;
			jqCiDivs.each(function () {
				var jqCiDiv = $(this);
				
				//exclude content items in content items - only regard content items in page content item panels
				if (jqCiDiv.parents("div.rocket-content-item-panel").length > 0) return;
				
				var panelName = jqCiDiv.data("name");
				if (0 <= panelNames.indexOf(panelName)) {
					jqCiDiv.show();
					display = true;
				} else {
					jqCiDiv.hide();
				}
			});
			
			if (display) {
				jqCiContainer.show();
			} else {
				jqCiContainer.hide();
			}
		};
		
		restrictCiPanels();
		
		jqSelect.change(restrictCiPanels);
		
	});
});

//$("select.page-method").parent().parent().parent().children("div.rocket-property").children("div").children("div.rocket-properties").children("div.rocket-property").children("div").children("div.rocket-content-items").children("div.rocket-content-item-panel").size()