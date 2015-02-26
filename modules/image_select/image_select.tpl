<label for="{$name}">{$title}</label>
<select id="{$name}" name="{$name}" title="{$description}">
	<option value="">None</option>
	{foreach $background_images as $background_image}
		<option value="{$background_image}" {if $value == $background_image}selected="selected"{/if}>{$background_image}</option>
	{/foreach}
</select>