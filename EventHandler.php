<?php

/**
 * This file contains the event handler for the RequestContextCreateSkin event
 * 
 * @file
 * @ingroup Extensions
 * @author Gabriel Birke <gb@birke-software.de>
 */

namespace Birke\Mediawiki\PersistSkinInCookie;

/**
 * Handles various skin-related events 
 * 
 * @author Gabriel Birke
 */
class EventHandler {
    
    const COOKIE_CURRENT_SKIN = "currentSkin";
    const COOKIE_LAST_CHANGE  = "lastSkinChange";
    
    /**
     * Set current skin name from "useskin" param or from cookie.
     * 
     * If param or cookie contains a skin name, `$skin` is overwritten with the 
     * stored skin name.
     * 
     * @param \RequestContext $context
     * @param mixed $skin Skin name (object or string)
     * @return boolean Always returns true to enable other plugins to modify `$skin`
     */
    public function onRequestContextCreateSkin($context, &$skin) {
        $request = $context->getRequest();
        
        // use new skin via useskin param 
        $useskin = $request->getVal("useskin"); 
        if ($useskin) {
            $request->response()->setcookie(self::COOKIE_CURRENT_SKIN, $useskin);
            $request->response()->setcookie(self::COOKIE_LAST_CHANGE, wfTimestamp(TS_MW));
            $skin = $useskin;
            return true;
        }

        // Use a previously persisted skin name
        $currentSkin = $request->getCookie(self::COOKIE_CURRENT_SKIN);
        if ($currentSkin) {
            // If you want to remove the cookie, add the &usedefaultskin=1 parameter
            if ($request->getBool("usedefaultskin")) {
                $request->response()->setcookie(self::COOKIE_CURRENT_SKIN, false);
                $request->response()->setcookie(self::COOKIE_LAST_CHANGE, wfTimestamp(TS_MW));
                return true;
            }
            
            $skin = $currentSkin;
            return true;
        }
        return true;
    }
    
    /**
     * Add timestamp of last skin persistence to list of `$modifiedTimes`
     * 
     * This event handler helps with returning the "304 Not Modified" HTTP status
     * when the stylesheet is requested.
     * 
     * @global \WebRequest $wgRequest
     * @param array $modifiedTimes
     * @return boolean Always returns true to enable other plugins to change `$modifiedTimes`
     */
    public function onOutputPageCheckLastModified( &$modifiedTimes ) {
        /* @var $wgRequest \WebRequest **/
        global $wgRequest;
        if (empty($_COOKIE[self::COOKIE_LAST_CHANGE])) {
            $lastChange = $wgRequest->getCookie(self::COOKIE_LAST_CHANGE);
        }
        else {
            $lastChange = $_COOKIE[self::COOKIE_LAST_CHANGE];
        }
        if ($lastChange) {
            $modifiedTimes['lastSkinChange'] = $lastChange;
        }
        return true;
    }
    
}