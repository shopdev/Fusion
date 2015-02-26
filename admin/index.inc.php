<?php
/**
 * Fusion Module Settings
 *
 * Handles module settings for the Fusion Framework.
 *
 * @author ShopDev
 * @version 1.0
 *
 * Copyright (c) 2011
 * Licensed under the GPL-3.0 software license agreement
 */

// Init. module
$module	= new Module(__FILE__, $_GET['module'], 'admin/index.tpl', true, false);

// Dispay the module
$module->fetch();
$page_content = $module->display();
