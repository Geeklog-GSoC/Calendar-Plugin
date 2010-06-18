<?php

/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | calendarv2 Plugin 0.1                                                     |
// +---------------------------------------------------------------------------+
// | index.php                                                                 |
// |                                                                           |
// | Public plugin page                                                        |
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

require_once '../lib-common.php';

// take user back to the homepage if the plugin is not active
if (! in_array('calendarv2', $_PLUGINS)) {
    echo COM_refresh($_CONF['site_url'] . '/index.php');
    exit;
}

require_once $_CONF['path'] . 'plugins/calendarv2/classes/calendarv2.class.php';
require_once $_CONF['path'] . 'plugins/calendarv2/classes/eventv2.class.php';
$A = $_GET;
$display = '';
if (isset($A['cid'])) {
    $cid = $A['cid'];
}
else {
    $cid = 1;
}

if (!COM_isAnonUser()) {
    $calendars = calendarv2_get_calendars($_USER['uid']);
}
$calendar = new Calendarv2 ();
$event = new Event();
if (isset($_POST['submit'])) {
    $event->load_event_from_array($_POST);
    $event->save_to_database();
    $page .= COM_showMessageText("You have succesfully added an event", "Alert");
}

$matrix = $calendar->c2_generateMatrix($A['month'] , $A['year']);
$page .= calendarv2_display_calendar_links($calendars);


// MAIN
$display .= COM_siteHeader('menu', $LANG_CALENDARV2_1['plugin_name']);
$display .= COM_startBlock($LANG_CALENDARV2_1['plugin_name']);
$display .= '<p>Welcome to the ' . $LANG_CALENDARV2_1['plugin_name'] . ' plugin, '
         . $_USER['username'] . '!</p>';
$display .= $page;
$display .= calendarv2_display($matrix, $A, $calendars, $cid);
$display .= COM_endBlock();
$display .= COM_siteFooter();

COM_output($display);

?>
