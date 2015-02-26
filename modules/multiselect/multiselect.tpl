<label for="{$name}">{$title}</label>
<select multiple id="{$name}" name="{$name}[]" title="{$description}">
	{foreach $options as $option}
		<option value="{$option.attributes.name}" {if in_array($option.attributes.name, $selected)}selected="selected"{/if}>{$option.attributes.title}</option>
	{/foreach}
</select>