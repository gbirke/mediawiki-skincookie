<?php

global $wgExtensionCredits, $wgAutoloadClasses, $wgExtensionMessagesFiles, $wgHooks;

$wgExtensionCredits['parserhook'][] = array(
    'path' => __FILE__,
    'name' => 'PersistSkinInCookie',
    'author' => 'Gabriel Birke <gb@birke-software.de>', 
    'url' => '', 
    'descriptionmsg' => 'persistskinincookie-desc',
    'version'  => 1.0,
    'license-name' => "MIT",   // Short name of the license, links LICENSE or COPYING file if existing - string, added in 1.23.0
);


$wgAutoloadClasses['Birke\\Mediawiki\\PersistSkinInCookie\\EventHandler'] = __DIR__ . '/EventHandler.php';

$wgExtensionMessagesFiles['PersistSkinInCookie'] = __DIR__ . '/PersistSkinInCookie.i18n.php';

$eventHandler = new Birke\Mediawiki\PersistSkinInCookie\EventHandler();
$wgHooks['RequestContextCreateSkin'][] = $eventHandler;
$wgHooks['OutputPageCheckLastModified'][] = $eventHandler;
