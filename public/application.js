head.ready(function() {
	// Layout
	$('body').layout({
		north__size: 30,
		south__size: 30,
		autoResize: false,
		resizable: false,
		spacing_open: 0,
		spacing_closed: 0
	});
	
	var panelLayoutOptions = {
		west__size: 250,
		resizable: false,
		spacing_open: 0,
		spacing_closed: 0
	}
	
	if ($('.panel').length) var panelLayout = $('.panel').layout(panelLayoutOptions);
	
	// Panel buttonset
	$('#panels').buttonset().each(function() {
		$(this).find('input').each(function() {
			$(this).button('option', 'icons', {
				primary: $(this).data('icon'),
				secondary: null
			}).button('refresh');
		});		
	});
	
	// Tab switching
	$('#tabs li a').click(function() {
		var anchor = $(this).attr('href');
		$panel = $('.panel-content');
		$tabs = $panel.find('.tab');
		$tab = $panel.find(anchor);
		if ($tab[0]) {
			$(this).parents('ul:first').find('.active').removeClass('active');
			$(this).parent().addClass('active');
			$tabs.hide();
			$tab.show();
			// Save to local storage
			$.jStorage.set('tab', $(this).attr('href'));
		}
		return false;
	});
	
	// Panel switching
	$('#panels input[type=radio]').change(function() {
		var $panel = $('#' + $(this).val());
		if ($panel[0]) {
			// Hide all panels
			$('.panel').not($panel).hide();
			// Destroy layout instance of current panel
			panelLayout.destroy();
			// Show the requested panel
			$panel.show();
			// Apply layout to requested panel
			panelLayout = $panel.layout(panelLayoutOptions);
			// Show active or first tab
			$tabs = $panel.find('#tabs');
			if ($tabs.find('li.active')) {
				$tabs.find('li.active').find('a').click();
			} else {
				$tabs.find('li:first a').click();
			}
			// Save to local storage
			$.jStorage.set('panel', $(this).attr('id'));
		}
	});
	
	// Open last active panel and tab
	var panelID = $.jStorage.get('panel'),
		tabAnchor = $.jStorage.get('tab');
	
	if (panelID) $('#' + panelID).click();
	if (tabAnchor) $('#tabs a[href="'+tabAnchor+'"]').click();
	
	// Find validation errors
	$('.validation-error-msg').each(function() {
		var $tabLink = $('#tabs a[href="#'+$(this).parents('.tab').attr('id')+'"]'),
			$panelInput = $('#panels input[value="'+$(this).parents('.panel').attr('id')+'"]'),
			$panelLabel = $panelInput.siblings('label[for="'+$panelInput.attr('id')+'"]'),
			$panelErrors = $panelLabel.find('span.validation-errors'),
			$tabErrors = $tabLink.find('span.validation-errors');
			
		if ($panelErrors.length <= 0) {
			$panelLabel.append('<span class="validation-errors">1</span>');
		} else {
			$panelErrors.text(parseInt($panelErrors.text()) + 1);
		}
		
		if ($tabErrors.length <= 0) {
			$tabLink.append('<span class="validation-errors">1</span>');
		} else {
			$tabErrors.text(parseInt($tabErrors.text()) + 1);
		}
	});
	
	
	// Save all action
	$('#footer a[href="#save"]').button().click(function() {		
		$('form').submit();
	});
	
	// Buttons
	$('.button').button().each(function() {
		$(this).button('option', 'icons', {
			primary: $(this).data('icon'),
			secondary: null
		}).button('refresh');
	});
	
	// Uniform
	$('select.uniform, input:checkbox.uniform, input.uniform:radio, input.uniform:file').uniform();
	$(".toggle-switch:checkbox").uniform({ checkboxClass: 'switch' });
	
	// Color picker
	$('input.colorpicker').miniColors({
		letterCase: 'uppercase'
	});
	
	// Multiselect
	$("select.multiselect").multiselect({
		searchable: false,
		animated: false,
		width: 460,
		height: 150
	});
	
	// Tooltips
	$('.module').find('input, select').tipsy({gravity: 'w'});
	
	// Fatal errors
	$fatalErrors = $('.fatal-error');
	if ($fatalErrors.length > 0) {
		$fatalErrors.dialog({
			modal: true,
			resizable: false,
			draggable: false,
			closeOnEscape: false,
			open: function(event, ui) { $(".ui-dialog-titlebar-close").hide(); }
		});
	}
	
	// License error
	$licenseErrors = $('.license-error');
	if ($licenseErrors.length > 0) {
		$licenseErrors.dialog({
			modal: true,
			resizable: false,
			draggable: false,
			closeOnEscape: false,
			open: function(event, ui) { $(".ui-dialog-titlebar-close").hide(); },
			buttons: {
				'Submit': function() {
					$(this).find('form').submit();
				}
			}
		});
	}
	
	// Non-fatal errors
	$nonFaralErrors = $('.non-fatal-error');
	if ($nonFaralErrors.length > 0) {
		$nonFaralErrors.dialog({
			modal: true,
			resizable: false,
			draggable: false
		});
	}
	
	// Theme selector
	$themeSelector = $('.select-theme');
	if ($themeSelector.length > 0) {
		$themeSelector.dialog({
			modal: true,
			resizable: false,
			draggable: false,
			closeOnEscape: false,
			width: 600,
			open: function(event, ui) { $(".ui-dialog-titlebar-close").hide(); }
		});
	}
});

head.js(
	{jquery: '//ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js'},
	{jstorage: public_path + '/lib/jquery/jstorage/jquery.jstorage.js'},
	{actual: public_path + '/lib/jquery/actual/jquery.actual.js'},
	{ui: public_path + '/lib/jquery/ui/jquery-ui-1.8.11.custom.js'},
	{ui_paging: public_path + '/lib/jquery/ui/jquery.ui.tabs.paging.js'},
	{ui_closable: public_path + '/lib/jquery/ui/jquery.ui.tabs.closable.js'},
	{ui_label: public_path + '/lib/jquery/ui/jquery.ui.tabs.label.js'},
	{ui_selectmenu: public_path + '/lib/jquery/ui/jquery.ui.selectmenu.js'},
	{ui_multiselect: public_path + '/lib/jquery/ui/jquery.ui.multiselect.js'},
	{layout: public_path + '/lib/jquery/layout/jquery.layout.js'},
	{uniform: public_path + '/lib/jquery/uniform/jquery.uniform.js'},
	{tipsy: public_path + '/lib/jquery/tipsy/jquery.tipsy.js'},
	{minColors: public_path + '/lib/jquery/miniColors/jquery.miniColors.min.js'}
);