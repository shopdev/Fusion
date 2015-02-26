<script>
	{literal}
		head.ready(function() {
			var $tabs = $("#{/literal}{$name}{literal}").find(".tabs_container");
			
			// Function for updating tab numbering
			var update_tab_numbering = function($tabs) {
				$tabs.find("ul.tabs li").each(function(index) {
					$(this).find("a:first:not(.new)").html("Slide " + (index+1));
				});
			};
			
			// Build existing tabs
			$tabs.tabs({
				closable: true,
				closableClick: function(event, ui) {
					if ($(ui.tab).hasClass('new')) return false;
							
					if (confirm('Are you sure you want to remove this slide?')) {
						return true;
					}
					
					return false;
				},
				closeComplete: function() {
					update_tab_numbering($tabs);
				}
			});
			
			// Remove close icon for "New Slide" tab
			$tabs.find('a.new').siblings('.ui-close-tab').remove();
			
			// Load and apply CKEditor
			head.js('includes/ckeditor/ckeditor.js', 'includes/ckeditor/adapters/jquery.js', function() {
				$('.{/literal}{$name}_ckeditor{literal}').ckeditor({
					path: 'includes/ckeditor/',
					config: {
						fullPage: ($(this).hasClass("ckeditor-full")) ? true : false
					},
					toolbar: "CubeCart",
					selector: "textarea.slider_ckeditor",
					resize_dir: "vertical",
					enterMode: CKEDITOR.ENTER_BR,
					shiftEnterMode: CKEDITOR.ENTER_P,
					toolbarStartupExpanded: false
				});
			});	
		});
	{/literal}
</script>

<table id="{$name}" class="formatted slider">
	<thead>
		<tr>
			<th>{$title}</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td style="padding: 0">
				<div class="tabs_container">
					<ul class="tabs">
						{foreach $slides as $slide}
							<li><a href="#{$name}_slide_{$slide@iteration}">Slide {$slide@iteration}</a></li>
						{/foreach}
						<li><a href="#{$name}_slide_new" class="new">New Slide</a></li>
					</ul>
					
					{foreach $slides as $slide}
						<div id="{$name}_slide_{$slide@iteration}">
							<textarea name="{$name}[slides][{$slide@iteration}][content_html]" class="{$name}_ckeditor" style="width: 100%;">{$slide.content_html}</textarea>
						</div>
					{/foreach}
					
					<div id="{$name}_slide_new">
						<textarea name="{$name}[slides][{count($slides)+1}][content_html]" class="{$name}_ckeditor" style="width: 100%;"></textarea>
					</div>
				</div>
			</td>
		</tr>
	</tbody>
	<tfoot>
		<tr>
			<td colspan="2">
				<input type="checkbox" id="{$name}_enabled" name="{$name}[enabled]" value="on" class="toggle-switch" title="Enable {$title}?" {if $enabled}checked="checked"{/if} />
			</td>
		</tr>
	</tfoot>
</table>