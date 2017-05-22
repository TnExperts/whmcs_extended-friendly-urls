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
	
	# WEB_ROOT #
	$WEB_ROOT = $smarty->tpl_vars["WEB_ROOT"]->value;
	if ( substr( $WEB_ROOT, -1 ) != "/" ) { $WEB_ROOT = $WEB_ROOT . "/"; }
	
	# SEO_URLS : true / false #
	$SEO_URLS = $CONFIG["SEOFriendlyUrls"] == "on" ? true : false;
	
	# MEMBERS #
	
	
	# SUPPORT #
	
	
    return array( "WEB_ROOT" => $WEB_ROOT, "SEO_URLS" => $SEO_URLS, "MEMBERS"  => $MEMBERS, "SUPPORT"  => $SUPPORT );
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

function extendedfriendlyurls_modifyNavLinks( MenuItem $navigationObject ) {
    $vars = extendedfriendlyurls_getVariables();
	$supportUrls = array(
		'knowledgebase.php' => 'knowledgebase',
		'announcements.php' => 'announcements',
		'serverstatus.php' => 'network-status',
		'supporttickets.php' => 'support-tickets',
		'submitticket.php' => 'submit-ticket'
	);
	
	foreach ( $navigationObject->getChildren() as $childName => $childObject ) {
		$childObjectUri = $childObject->getUri();
		if ( !is_null( $childObjectUri ) ) {
			if ( stripos( $childObjectUri, "clientarea.php" ) !== false ) {
				$childObject->setUri( $vars["MEMBERS"] );
			} elseif ( in_array( $childObjectUri, $supportUrls ) ) {
				$childObject->setUri( $vars["SUPPORT"] . $supportUrls[$childObjectUri] . "/" );
			}
		}
		if ( $childObject->hasChildren() ) {
			foreach ( $childObject->getChildren() as $subChildObject ) {
				$subChildObjectUri = $subChildObject->getUri();
				if ( ( $pos = stripos( $subChildObjectUri, "clientarea.php?action=" ) ) !== FALSE ) {
					$subChildObject->setUri( $vars["MEMBERS"] . substr( $subChildObjectUri, $pos+22) . "/" );
				} elseif ( in_array( $subChildObjectUri, $supportUrls ) ) {
					$subChildObject->setUri( $vars["SUPPORT"] . $supportUrls[$subChildObjectUri] . "/" );
				}
			}
		}
	}
	extendedfriendlyurls_webRootRecursive( $navigationObject );
}

add_hook( 'ClientAreaPrimaryNavbar', 1, 'extendedfriendlyurls_modifyNavLinks' );
add_hook( 'ClientAreaSecondaryNavbar', 1, 'extendedfriendlyurls_modifyNavLinks' );
add_hook( 'ClientAreaPrimarySidebar', 1, 'extendedfriendlyurls_modifyNavLinks' );
add_hook( 'ClientAreaSecondarySidebar', 1, 'extendedfriendlyurls_modifyNavLinks' );
