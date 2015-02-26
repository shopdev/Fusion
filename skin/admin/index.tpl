<form action="{$VAL_SELF}" method="post" enctype="multipart/form-data">
	<div id="Fusion" class="tab_content">
		<h3>Fusion Framework</h3>
	  	<fieldset>
			<legend>{$LANG.module.config_settings}</legend>
			<div>
				<label for="fusion_status">{$LANG.common.status}</label>
				<span><input type="hidden" name="module[status]" id="fusion_status" class="toggle" value="{$MODULE.status}" />&nbsp;</span>
			</div>
  		</fieldset>
  </div>
  {$MODULE_ZONES}
  <div class="form_control">
	<input type="submit" value="{$LANG.common.save}" />
  </div>
  <input type="hidden" name="token" value="{$SESSION_TOKEN}" />
</form>