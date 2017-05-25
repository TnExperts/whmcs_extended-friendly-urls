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
	try {
        $MEMBERS = Capsule::table( 'tbladdonmodules' )
            ->where('module','extendedfriendlyurls')
            ->where('setting', 'members_url')
            ->value('value');
    } catch (\Exception $e) {
        die("Get MEMBERS failed: " . $e->getMessage() );
    }
    if ( substr( $MEMBERS, -1, 1 ) != "/" ) { $MEMBERS = $MEMBERS . "/"; }
    if ( substr( $MEMBERS, 0, 1 ) == "/" ) { $MEMBERS = substr( $MEMBERS, 1 ); }
	
	# SUPPORT #
	try {
        $SUPPORT = Capsule::table( 'tbladdonmodules' )
            ->where('module','extendedfriendlyurls')
            ->where('setting', 'support_url')
            ->value('value');
    } catch (\Exception $e) {
        die("Get SUPPORT failed: " . $e->getMessage() );
    }
    if ( substr( $SUPPORT, -1, 1 ) != "/" ) { $SUPPORT = $SUPPORT . "/"; }
    if ( substr( $SUPPORT, 0, 1 ) == "/" ) { $SUPPORT = substr( $SUPPORT, 1 ); }
	
    return array( "WEB_ROOT" => $WEB_ROOT, "SEO_URLS" => $SEO_URLS, "MEMBERS"  => $MEMBERS, "SUPPORT"  => $SUPPORT );
}

function extendedfriendlyurls_webRootRecursive($menuObject) {
    $vars = extendedfriendlyurls_getVariables();
    if ( $menuObject->hasChildren ) {
		foreach ( $menuObject->getChildren() as $childName => $childObject ) {
	    	$childUri = $child_object->getUri();
	    	if ( !is_null( $childUri ) && stripos( $childUri, $vars["WEB_ROOT"] ) !== 0 ) {
	        	$childObject->setUri( $vars["WEB_ROOT"] . $childUri );
	    	}
	    	extendedfriendlyurls_webRootRecursive( $childObject );
		}
    } elseif ( !is_null( $itemUri = $menuObject->getUri() ) ) {
        if ( stripos( $itemUri, $vars["WEB_ROOT"] ) !== 0 ) {
            $menuObject->setUri( $vars["WEB_ROOT"] . $itemUri );
        }
    }
}

function extendedfriendlyurls_modifyNavLinks( MenuItem $navigationObject ) {
    $vars = extendedfriendlyurls_getVariables();
    $supportUrls = array(
		'knowledgebase.php' => 'knowledgebase',
		'announcements.php' => 'announcements',
        'downloads.php' => 'downloads',
		'serverstatus.php' => 'network-status',
		'supporttickets.php' => 'support-tickets',
		'submitticket.php' => 'submit-ticket',
        'contact.php' => 'contact-us'
	);
	if ( $vars["SEO_URLS"] ) {
        foreach ( $navigationObject->getChildren() as $childObject ) {
            $childObjectUri = $childObject->getUri();
            if ( substr( $childObjectUri, 0, 1 ) == "/" ) { $childObjectUri = substr( $childObjectUri, 1 ); }
            if ( !is_null( $childObjectUri ) ) {
                if ( $childObjectUri == "clientarea.php" ) {
                    $childObject->setUri( $vars["MEMBERS"] );
                } elseif ( ( $pos = stripos( $childObjectUri, "clientarea.php?action=" ) ) !== FALSE ) {
                    $childObject->setUri( $vars["MEMBERS"] . substr( $childObjectUri, $pos+22) . "/" );
                } elseif ( array_key_exists( $childObjectUri, $supportUrls ) ) {
                    $childObject->setUri( $vars["SUPPORT"] . $supportUrls[$childObjectUri] . "/" );
                }
            }
            if ( $childObject->hasChildren() ) {
                foreach ( $childObject->getChildren() as $subChildObject ) {
                    $subChildObjectUri = $subChildObject->getUri();
                    if ( substr( $subChildObjectUri, 0, 1 ) == "/" ) { $subChildObjectUri = substr( $subChildObjectUri, 1 ); }
                    if ( $subChildObjectUri == "clientarea.php" ) {
                        $subChildObject->setUri( $vars["MEMBERS"] );
                    } elseif ( ( $pos = stripos( $subChildObjectUri, "clientarea.php?action=" ) ) !== FALSE ) {
                        $subChildObject->setUri( $vars["MEMBERS"] . substr( $subChildObjectUri, $pos+22) . "/" );
                    } elseif ( array_key_exists( $subChildObjectUri, $supportUrls ) ) {
                        $subChildObject->setUri( $vars["SUPPORT"] . $supportUrls[$subChildObjectUri] . "/" );
                    }
                }
            }
        }
        extendedfriendlyurls_webRootRecursive( $navigationObject );
    }
}

add_hook( 'ClientAreaPrimaryNavbar', 1, 'extendedfriendlyurls_modifyNavLinks' );
add_hook( 'ClientAreaSecondaryNavbar', 1, 'extendedfriendlyurls_modifyNavLinks' );
add_hook( 'ClientAreaPrimarySidebar', 1, 'extendedfriendlyurls_modifyNavLinks' );
add_hook( 'ClientAreaSecondarySidebar', 1, 'extendedfriendlyurls_modifyNavLinks' );
