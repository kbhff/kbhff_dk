<?php

/**
* This file contains definitions
*
* @package Config
*/
header("Content-type: text/html; charset=UTF-8");
error_reporting(E_ALL);

define("VERSION", "0.7.9.2");
define("UI_BUILD", "UN-BUILT");

define("SITE_UID", "KBHFF");
define("SITE_NAME", "Københavns Fødevarefællesskab");
define("SITE_URL", (isset($_SERVER["HTTPS"]) ? "https" : "http")."://".$_SERVER["SERVER_NAME"]);
define("SITE_EMAIL", "info@kbhff.dk");

define("DEFAULT_PAGE_DESCRIPTION", "");
define("DEFAULT_PAGE_IMAGE", "/img/logo.png");

define("DEFAULT_LANGUAGE_ISO", "DA");
define("DEFAULT_COUNTRY_ISO", "DK");
define("DEFAULT_CURRENCY_ISO", "DKK");

define("SITE_LOGIN_URL", "/login");

define("SITE_SIGNUP", true);
define("SITE_SIGNUP_URL", "/bliv-medlem");

define("SITE_ITEMS", true);

define("SITE_SHOP", true);
define("SHOP_ORDER_NOTIFIES", "it@kbhff.dk");

define("SITE_SUBSCRIPTIONS", true);

define("SITE_MEMBERS", true);

define("SITE_COLLECT_NOTIFICATIONS", 50);

define("RENEWAL_DATE", "05-01");

define("SITE_PAYMENT_REGISTER_INTENT", SITE_URL."/butik/betalingsgateway/{GATEWAY}/register-intent");
define("SITE_PAYMENT_REGISTER_PAID_INTENT", SITE_URL."/butik/betalingsgateway/{GATEWAY}/register-paid-intent");

define("ORDERING_DEADLINE_TIME", "Wednesday 23:59");
define("ORDERING_REMINDER_TIME_DELTA_HOURS", 8);
// define("ORDERING_REMINDER_TIME_DELTA_HOURS", 151);

define("PICKUP_DAY", "Wednesday");
define("PICKUP_REMINDER_TIME_DELTA_HOURS", 4);
// define("PICKUP_REMINDER_TIME_DELTA_HOURS", 128);

