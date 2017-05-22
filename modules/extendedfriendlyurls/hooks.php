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

function extendedfriendlyurls_webRootRecursive($menuObject) {
    $vars = extendedfriendlyurls_getVariables();
    if ( $navbarObject->hasChildren ) {
	foreach ( $navbarObject->getChildren() as $childName => $childObject ) {
	    $childUri = $child_object->getUri();
	    if ( !is_null( $childUri ) && stripos( $childUri, $vars["WEB_ROOT"] ) !== 0 ) {
	        $childObject->setUri( $vars["WEB_ROOT"] . $childUri );
	    }
	    extendedfriendlyurls_webRootRecursive( $childObject );
	}
    } elseif ( !is_null( $itemUri = $navbarObject->getUri() ) ) {
        if ( stripos( $itemUri, $vars["WEB_ROOT"] ) !== 0 ) {
            $navbarObject->setUri( $vars["WEB_ROOT"] . $itemUri );
        }
    }
}
