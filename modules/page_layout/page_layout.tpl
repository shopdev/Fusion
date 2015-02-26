{literal}
	<script>
		head.ready(function() {
			$(".{/literal}{$name}{literal}_tabs_container").tabs();
		
			$('select[name="{/literal}{$name}{literal}[template]"]').each(function() {
				$('#' + $(this).attr('id') + '_' + $(this).val()).show();
			}).change(function() {
				$('.{/literal}{$name}{literal}_template_regions').hide();
				$('#' + $(this).attr('id') + '_' + $(this).val()).show();
			});
		});
	</script>
{/literal}

<div class="submodule">
	<label>Layout</label>
	<select id="{$name}" class="page_layout_template" name="{$name}[template]">
		{if $can_use_default}<option value="" {if !$value.template}selected="selected"{/if}>(use default)</option>{/if}
		{foreach $templates as $template}
			<option value="{$template.attributes.name}" {if $value.template == $template.attributes.name}selected="selected"{/if}>{$template.attributes.title}</option>
		{/foreach}
	</select>
</div>

{foreach $templates as $template}
	<div id="{$name}_{$template.attributes.name}" class="{$name}_template_regions" style="border: 1px solid #B6B6B6; display: none;">
		<div class="{$name}_tabs_container">
			<ul class="tabs">
				{foreach $template.region as $region}
					{if count($region.widget) > 0}
						<li><a href="#{$name}_{$template.attributes.name}_{$region.attributes.name}">{$region.attributes.title}</a></li>
					{/if}
				{/foreach}
			</ul>
			{foreach $template.region as $region}
				{if count($region.widget) > 0}
					<div id="{$name}_{$template.attributes.name}_{$region.attributes.name}" class="tab_content">
						{if $region.attributes.image}
							<label><img src="{$images_path}/{$region.attributes.image}" alt="{$region.attributes.title}" /></label>
						{else}
							<label>{$region.attributes.title}</label>
						{/if}
					
						<select name="{$name}[{$template.attributes.name}][{$region.attributes.name}][]" class="multiselect" multiple>
							{* Add selected widgets first *}
							{foreach $value[$template.attributes.name][$region.attributes.name] as $widgetName}
								{foreach $region.widget as $widget}
									{if $widgetName == $widget.attributes.name}
										<option value="{$widget.attributes.name}" selected>{$widget.attributes.title}</option>
									{/if}
								{/foreach}
							{/foreach}
							{* Unselected widgets are added to the list after selected widgets to preserve order *}
							{foreach $region.widget as $widget}
								{if !is_array($value[$template.attributes.name][$region.attributes.name]) || !in_array($widget.attributes.name, $value[$template.attributes.name][$region.attributes.name])}
									<option value="{$widget.attributes.name}">{$widget.attributes.title}</option>
								{/if}
							{/foreach}
						</select>
					</div>
				{/if}
			{/foreach}
		</div>
	</div>
{/foreach}
