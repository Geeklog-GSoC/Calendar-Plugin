<?php

/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | calendarv2 Plugin 0.1                                                     |
// +---------------------------------------------------------------------------+
// | index.php                                                                 |
// |                                                                           |
// | Plugin administration page                                                |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2010 by the following authors:                              |
// |                                                                           |
// | Authors: Vlad Voicu - vladvoic AT gmail DOT com                           |
// +---------------------------------------------------------------------------+
// | Created with the Geeklog Plugin Toolkit.                                  |
// +---------------------------------------------------------------------------+
// |                                                                           |
// | This program is free software; you can redistribute it and/or             |
// | modify it under the terms of the GNU General Public License               |
// | as published by the Free Software Foundation; either version 2            |
// | of the License, or (at your option) any later version.                    |
// |                                                                           |
// | This program is distributed in the hope that it will be useful,           |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of            |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the             |
// | GNU General Public License for more details.                              |
// |                                                                           |
// | You should have received a copy of the GNU General Public License         |
// | along with this program; if not, write to the Free Software Foundation,   |
// | Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.           |
// |                                                                           |
// +---------------------------------------------------------------------------+

/**
* @package calendarv2
*/

require_once '../../../lib-common.php';
require_once '../../auth.inc.php';
require_once $_CONF['path'] . 'plugins/calendarv2/classes/eventv2.class.php';

$display = '';
$A = $_GET;

// Ensure user even has the rights to access this page
if (! SEC_hasRights('calendarv2.admin')) {
    $display .= COM_siteHeader('menu', $MESSAGE[30])
             . COM_showMessageText($MESSAGE[29], $MESSAGE[30])
             . COM_siteFooter();

    // Log attempt to access.log
    COM_accessLog("User {$_USER['username']} tried to illegally access the calendarv2 plugin administration screen.");

    echo $display;
    exit;
}


if ($A['mode'] == 'editsubmission') {
    $event = new Event();
    $event->get_event($A['id'], 'cv2submission');
    $page = calendarv2_modify_event($event);
    
}


// MAIN
$display .= COM_siteHeader('menu', $LANG_CALENDARV2_1['plugin_name']);
$display .= COM_startBlock($LANG_CALENDARV2_1['plugin_name']);
$display .= '<p>Welcome to the ' . $LANG_CALENDARV2_1['plugin_name'] . ' plugin, '
         . $_USER['username'] . '!</p>';
$display .= $page;
$display .= COM_endBlock();
$display .= COM_siteFooter();

echo $display;

?>
