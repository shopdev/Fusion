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
							<div class="submodule">
								<label for="{$name}_slide_{$slide@iteration}_image">Image</label>
								<select id="{$name}_slide_{$slide@iteration}_image" name="{$name}[slides][{$slide@iteration}][image]">
									<option value="">-- Please Select --</option>
									{foreach $images as $image}
										<option value="{$image}" {if $slide.image == $image}selected="selected"{/if}>{basename($image)}</option>
									{/foreach}
								</select>
							</div>
							<div class="submodule">
								<label for="{$name}_slide_{$slide@iteration}_background_color">Background Color</label>
								<input type="text" id="{$name}_slide_{$slide@iteration}_background_color" name="{$name}[slides][{$slide@iteration}][background_color]" class="colorpicker miniColors" value="{$slide.background_color}" />
							</div>
							<div class="submodule">
								<label for="{$name}_slide_{$slide@iteration}_background_image">Background Image</label>
								<select id="{$name}_slide_{$slide@iteration}_background_image" name="{$name}[slides][{$slide@iteration}][background_image]">
									<option value="">None</option>
									{foreach $background_images as $background_image}
										<option value="{$background_image}" {if $slide.background_image == $background_image}selected="selected"{/if}>{$background_image}</option>
									{/foreach}
								</select>
							</div>
							<div class="submodule">
								<label for="{$name}_slide_{$slide@iteration}_url">Link</label>
								<input type="text" id="{$name}_slide_{$slide@iteration}_url" name="{$name}[slides][{$slide@iteration}][url]" value="{$slide.url}" />
							</div>
						</div>
					{/foreach}
					
					<div id="{$name}_slide_new">
						<div class="submodule">
							<label for="{$name}_slide_new_image">Image</label>
							<select id="{$name}_slide_new_image" name="{$name}[slides][{count($slides)+1}][image]">
								<option value="">-- Please Select --</option>
								{foreach $images as $image}
									<option value="{$image}">{basename($image)}</option>
								{/foreach}
							</select>
						</div>
						<div class="submodule">
							<label for="{$name}_slide_new_background_color">Background Color</label>
							<input type="text" id="{$name}_slide_new_background_color" name="{$name}[slides][{count($slides)+1}][background_color]" class="colorpicker miniColors" value="#FFFFFF" />
						</div>
						<div class="submodule">
							<label for="{$name}_slide_new_background_image">Background Image</label>
							<select id="{$name}_slide_new_background_image" name="{$name}[slides][{count($slides)+1}][background_image]">
								<option value="">None</option>
								{foreach $background_images as $background_image}
									<option value="{$background_image}">{$background_image}</option>
								{/foreach}
							</select>
						</div>
						<div class="submodule">
							<label for="{$name}_slide_new_url">Link</label>
							<input type="text" id="{$name}_slide_new_url" name="{$name}[slides][{count($slides)+1}][url]" value="" />
						</div>
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