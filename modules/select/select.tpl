<label for="{$name}">{$title}</label>
<select id="{$name}" name="{$name}" title="{$description}">
	{foreach $options as $option}
		<option value="{$option.attributes.name}" {if $option.attributes.name == $selected}selected="selected"{/if}>{$option.attributes.title}</option>
	{/foreach}
</select>