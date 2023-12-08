<?php

/***************************************************************************
 *
 *    OUGC Landing Page plugin (/inc/plugins/ougcLandingPage/Hooks/forums.php)
 *    Author: Omar Gonzalez
 *    Copyright: © 2021 Omar Gonzalez
 *
 *    Website: https://ougc.network
 *
 *    Set a forced landing page for certain groups.
 *
 ***************************************************************************
 ****************************************************************************
 * This program is protected software: you can make use of it under
 * the terms of the OUGC Network EULA as detailed by the included
 * "EULA.TXT" file.
 *
 * This program is distributed with the expectation that it will be
 * useful, but WITH LIMITED WARRANTY; with a limited warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * OUGC Network EULA included in the "EULA.TXT" file for more details.
 *
 * You should have received a copy of the OUGC Network EULA along with
 * the package which includes this file.  If not, see
 * <https://ougc.network/eula.txt>.
 ****************************************************************************/

declare(strict_types=1);

namespace ougc\LandingPage\Hooks\Forum;

use MyBB;

use function ougc\LandingPage\Core\getSetting;

use function ougc\LandingPage\Core\loadLanguage;

use const THIS_SCRIPT;

use const TIME_NOW;

function global_start()
{
    global $templatelist, $mybb;

    if (isset($templatelist)) {
        $templatelist .= ',';
    } else {
        $templatelist = '';
    }

    $templatelist .= ',';

    if (!is_member(getSetting('showToGroups'))) {
        return;
    }

    if (defined('THIS_SCRIPT')) {
        $scriptName = my_strtolower(THIS_SCRIPT);
    } else {
        $scriptName = my_strtolower(basename($_SERVER['SCRIPT_NAME']));
    }

    $showLandingPage = true;

    $bypassScripts = (array)json_decode(getSetting('exceptScripts'));

    foreach ($bypassScripts as $fileName => $inputKeys) {
        if ($scriptName === $fileName) {
            if (empty($inputKeys)) {
                $showLandingPage = false;

                break;
            }

            foreach ($inputKeys as $inputKey => $inputValues) {
                if (!isset($mybb->input[$inputKey]) || in_array($mybb->get_input($inputKey), $inputValues)) {
                    $showLandingPage = false;

                    break;
                }
            }
        }
    }

    if (!$showLandingPage) {
        return;
    }

    $mybb->settings['redirects'] = $mybb->user['showredirect'] = 0;

    redirect(getSetting('redirectPage'));
    //my_setcookie(), my_unsetcookie()
}