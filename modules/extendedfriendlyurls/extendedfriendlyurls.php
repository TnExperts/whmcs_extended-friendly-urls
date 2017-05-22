<?php if ( !defined( 'WHMCS' ) ) { die( 'You cannot load this file directly!' ); } // Security Check
/**
 * Extended Friendly URLs for WHMCS
 * by John Stray [https://www.johnstray.id.au/
 * Copyright 2017 (c) All Rights Reserved
 * Licensed under the GNU General Public License v3.0+
 */

function extendedfriendlyurls_config() {
    $configarray = array(
        "name"          => "Extended Friendly URLs for WHMCS",
        "desscription"  => "Adds an extended array of friendly urls to WHMCS and converts all navigation links to support them.",
        "version"       => "1.0",
        "author"        => "John Stray",
    );
    return $configarray;
}
