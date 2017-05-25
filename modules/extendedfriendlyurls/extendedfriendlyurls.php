<?php if (!defined('WHMCS')) { die('You cannot load this file directly!'); } // Security Check
/**
 * Extended Friendly URLs for WHMCS
 * by John Stray [https://www.johnstray.id.au/
 * Copyright 2017 (c) All Rights Reserved
 * Licensed under the GNU General Public License v3.0+
 */

use WHMCS\Database\Capsule;

function extendedfriendlyurls_config() {
    $configarray = array(
        "name"          => "Extended Friendly URLs for WHMCS",
        "description"  => "Adds an extended array of friendly urls to WHMCS and converts all navigation links to support them.",
        "version"       => "1.0",
        "author"        => "John Stray",
        "fields" => array(
            "members_url" => array(
                "FriendlyName"  => "\"Members\" URL",
                "Description"   => "A subfolder URL that ClientArea sections fall under, eg.: \"/members/invoices/\" (Leave blank for none)",
                "Default"       => "members",
                "Type"          => "text",
                "Size"          => "32"
            ),
            "support_url" => array(
                "FriendlyName"  => "\"Support\" URL",
                "Description"   => "A subfolder URL that Support sections fall under, eg.: \"/support/announcements/\" (Leave blank for none)",
                "Default"       => "support",
                "Type"          => "text",
                "Size"          => "32"
            ),
            "products_url" => array(
                "FriendlyName"  => "\"Products\" URL",
                "Description"   => "A subfolder URL that Products/Groups fall under, eg.: \"/products/1/my-product.html\" (Leave blank for none)",
                "Default"       => "support",
                "Type"          => "text",
                "Size"          => "32"
            ),
        )
    );
    return $configarray;
}

function extendedfriendlyurls_activate() {
    try {
        Capsule::table( 'tbladdonmodules' )->insert(
            ['module' => 'extendedfriendlyurls', 'setting' => 'version', 'value' => '1.0']
            ['module' => 'extendedfriendlyurls', 'setting' => 'members_url', 'value' => 'members']
            ['module' => 'extendedfriendlyurls', 'setting' => 'support_url', 'value' => '']
            ['module' => 'extendedfriendlyurls', 'setting' => 'products_url', 'value' => 'products']
        );
        return array( 'status' => 'success', 'description' => 'Extended Friendly URLs sucessfully activated!' );
    } catch (\Exception $e) {
        return array( 'status' => 'error', 'description' => 'Unable to successfully activate Extended Friendly URLs! ' . $e->getMessage() );
    }
}

function extendedfriendlyurls_deactivate() {
    try {
        Capsule::table( 'tbladdonmodules' )->where( 'module', '=', 'extendedfriendlyurls' )->delete(); );
        return array( 'status' => 'success', 'description' => 'Extended Friendly URLs successfully deactivated!' );
    } catch (\Exception $e) {
        return array( 'status' => 'error', 'description' => 'Unable to deactivate Extended Friendly URLs! ' . $e->getMessaget() );
    }
}
