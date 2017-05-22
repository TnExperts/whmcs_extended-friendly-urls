<?php if ( !defined( 'WHMCS' ) ) { die( 'You cannot load this file directly!' ); } // Security Check
/**
 * Extended Friendly URLs for WHMCS
 * by John Stray [https://www.johnstray.id.au/
 * Copyright 2017 (c) All Rights Reserved
 * Licensed under the GNU General Public License v3.0+
 */

use WHMCS\Database\Capsule;
use WHMCS\View\Menu\Item as MenuItem;

function extendedfriendlyurls_getVariables() {
    GLOBAL $smarty, $CONFIG;
  
    return array(
        "WEB_ROOT" => $smarty->tpl_vars["WEB_ROOT"]->value;
        "SEO_URLS" => $CONFIG["SEOFriendlyUrls"] == "on" ? true : false;
    );
}
