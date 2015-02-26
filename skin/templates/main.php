<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8" />
		<title>Fusion Configuration Panel</title>
		<link rel="stylesheet" type="text/css" href="{$PUBLIC_PATH}/css/lib/jquery/ui/themes/Aristo/jquery-ui-1.8.7.custom.css" />
		<link rel="stylesheet" type="text/css" href="{$PUBLIC_PATH}/css/lib/jquery/layout/aristo.css" />
		<link rel="stylesheet" type="text/css" href="{$PUBLIC_PATH}/css/reset.css">
		<link rel="stylesheet" type="text/css" href="{$PUBLIC_PATH}/css/lib/jquery/uniform/uniform.aristo.css" />
		<link rel="stylesheet" type="text/css" href="{$PUBLIC_PATH}/css/lib/jquery/tipsy/tipsy.css" />
		<link rel="stylesheet/less" type="text/css" href="{$PUBLIC_PATH}/css/application.less">
		<script src="{$PUBLIC_PATH}/lib/less/less.js"></script>

		<!-- Script Loader (head.js) -->
		<script src="{$PUBLIC_PATH}/lib/head/head.min.js"></script>

		<script>
			var public_path = "{$PUBLIC_PATH}";
		</script>
	</head>

	<body>
		<header class="ui-layout-north" id="toolkit">
			<div id="panels">
				{foreach from=$PANELS_NAVIGATION item=panel}
					<input type="radio" id="panel_{$panel@iteration}" name="panels" value="panel_{$panel.name}" {if $panel@first}checked="checked"{/if} data-icon="{$panel.icon}" />
					<label for="panel_{$panel@iteration}">{$panel.title}</label>
				{/foreach}
			</div>
			<div id="exit">
				<a href="admin.php" class="button admin-home">Back to CubeCart</a>
			</div>
		</header>
		<form action="{$VAL_SELF}" method="post" enctype="multipart/form-data">
			<div class="ui-layout-center">
				{foreach from=$PANELS item=panel}
					<div id="panel_{$panel.attributes.name}" class="panel" {if !$panel@first}style="display: none;"{/if}>
						<aside class="ui-layout-west" id="tabs">
					        <ul>
								{foreach from=$panel.tab item=tab}
					        		<li {if $tab@first}class="active"{/if}><a href="#tab_{$tab.attributes.name}">{$tab.attributes.title}</a></li>
					        	{/foreach}
					        </ul>
						</aside>
						<section class="ui-layout-center panel-content">
							{foreach from=$panel.tab item=tab}
								<div class="tab" id="tab_{$tab.attributes.name}" {if !$tab@first}style="display: none;"{/if}>
									{foreach from=$tab.section item=section}
										<fieldset>
											<legend>{$section.attributes.title}</legend>
											{if isset($section.attributes.description) && $section.attributes.description != ""}
												<div class="section-description">
													{$section.attributes.description}
												</div>
											{/if}
											{foreach from=$section.setting item=setting}
												{if isset($setting.attributes.module)}
													<div id="module_{$setting.attributes.module}" class="module {if (isset($VALIDATION[$setting.attributes.name]))}invalid{/if}">
														{$fusion->module($setting)}
														{if (isset($VALIDATION[$setting.attributes.name]))}
															<span class="validation-error-msg">{$VALIDATION[$setting.attributes.name]}</span>
														{/if}
													</div>
												{/if}
											{/foreach}
										</fieldset>
									{foreachelse}
										{foreach from=$tab.setting item=setting}
											{if isset($setting.attributes.module)}
												<div id="module_{$setting.attributes.module}" class="module">
													{$fusion->module($setting)}
													{if (isset($VALIDATION[$setting.attributes.name]))}
														<span class="validation-error-msg">{$VALIDATION[$setting.attributes.name]}</span>
													{/if}
												</div>
											{/if}
										{/foreach}
									{/foreach}
								</div>
							{/foreach}
						</section>
					</div>
				{/foreach}
				<!-- Check skin can be configured -->
				{if !isset($SKINS) || empty($SKINS)}
			        <div title="No Compatible Themes" class="fatal-error">
			        	<p>No compatible themes were detected.</p>
			        </div>
				{else if (!$SKIN && !empty($SKINS))}
					<div title="Select Theme" class="select-theme">
						{foreach from=$SKINS key=skinName item=skinTitle}
							<a href="{$VAL_SELF}&amp;skin={$skinName}" class="theme">
								<img src="{$STORE_URL}/skins/{$skinName}/{$skinName}.png" alt="{$skinTitle}" />
								<span>{$skinTitle}</span>
							</a>
						{/foreach}
			        </div>
				{else if !isset($SKINS[$SKIN])}
			        <div title="Incompatible Theme" class="fatal-error">
			        	<p>The selected theme is not compatible with Fusion.</p>
			        </div>
				{else if isset($INCOMPATIBLE)}
					<div title="Update Required" class="fatal-error">
			        	<p>This theme requires Fusion V{$INCOMPATIBLE}.</p>
			        </div>
				{else if isset($LOADED_DEFAULT)}
					<div title="Default Configuration Loaded" class="non-fatal-error">
			        	<p>Fusion has loaded the default configuration included with this theme.  You can now proceed to edit any settings.</p>
			        </div>
				{else if isset($VALIDATION)}
					<!-- Check for validation errors -->
					<div title="Invalid Settings" class="non-fatal-error">
						<p>
							One or more validation errors occurred.  Please review and amend your changes before trying again.
							<span style="display: none;">{var_dump($VALIDATION)}</span>
						</p>
					</div>
				{/if}
			</div>
			<input type="hidden" name="token" value="{$SESSION_TOKEN}" />
		</form>
		<footer class="ui-layout-south" id="footer">
			<span class="version">Fusion V{$fusion->getVersion()}</span>
			<a href="#save" class="button" data-icon="ui-icon-disk">Save All</a>
		</footer>
	</body>

	<!-- Application JS -->
	<script src="{$PUBLIC_PATH}/application.js"></script>
</html>
