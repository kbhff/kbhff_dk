<?php

/**
* This file contains definitions
*
* @package Config
*/
header("Content-type: text/html; charset=UTF-8");
error_reporting(E_ALL);

/**
* Site name
*/
define("SITE_UID", "KBHFF");
define("SITE_NAME", "Københavns Fødevarefællesskab");
define("SITE_URL", (isset($_SERVER["HTTPS"]) ? "https" : "http")."://".$_SERVER["SERVER_NAME"]);
define("SITE_EMAIL", "info@kbhff.dk");

/**
* Optional constants
*/
define("DEFAULT_PAGE_DESCRIPTION", "");
define("DEFAULT_LANGUAGE_ISO", "EN");
define("DEFAULT_COUNTRY_ISO", "DK");
define("DEFAULT_CURRENCY_ISO", "DKK");


// ENABLE ITEMS MODEL
define("SITE_ITEMS", true);

define("SITE_SIGNUP", "/bliv-medlem");
define("SITE_SUBSCRIPTIONS", true);
define("SITE_MEMBERS", true);

// Enable shop model
define("SITE_SHOP", true);
define("SHOP_ORDER_NOTIFIES", "soren@parentnode.dk");

// Enable notifications (send collection email after N notifications)
define("SITE_COLLECT_NOTIFICATIONS", 50);

// INSTALL MODE (DISABLES ALL SECURITY)
//define("SITE_INSTALL", true);

?>
