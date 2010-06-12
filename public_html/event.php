 <?php

/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | calendarv2 Plugin 0.1                                                     |
// +---------------------------------------------------------------------------+
// | event.php                                                                 |
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

require_once $_CONF['path'] . 'plugins/calendarv2/classes/eventv2.class.php'; 

$A = $_GET;
$B = $_POST;
// Check if mofication of an event or deletion is asked by a $_POST variable
if (empty($_POST)) {
    // Check if we need to display a single event.
    if (isset($A['eid'])) {
        $event = new Event();
        $event->get_event($A['eid']);
        $page = calendarv2_single_event($event);
    }
    else {
        if (is_array ($A)) {
            $page = calendarv2_day_events($_GET);
        }
    }
}
else {
    if (isset($B['modify'])) {
        $event = new Event();
        $event->get_event($eid);
        $page = calendarv2_modify_event($event);
        }
    if (isset($B['delete'])) {
        $page = calendarv2_delete_event($B['eid']);
    }
}


// MAIN
$display .= COM_siteHeader('menu', $LANG_CALENDARV2_1['plugin_name']);
$display .= COM_startBlock($LANG_CALENDARV2_1['plugin_name']);
$display .= '<p>Welcome to the ' . $LANG_CALENDARV2_1['plugin_name'] . ' plugin, '
         . $_USER['username'] . '!</p>';
$display .= $page;
$display .= COM_endBlock();
$display .= COM_siteFooter();

COM_output($display);

?> 
 
