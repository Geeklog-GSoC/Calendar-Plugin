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
require_once $_CONF['path'] . 'plugins/calendarv2/classes/calendarv2.class.php'; 
require_once $_CONF['path'] . 'plugins/calendarv2/classes/reventv2.class.php';
require_once $_CONF['path'] . 'plugins/calendarv2/classes/aeventsv2.class.php'; 

$A = $_GET;
$B = $_POST;
$cid = COM_applyFilter($_GET['cid']);
// Must do some security here.
$calendar = new Calendarv2();
$calendar->setCid($cid);
if (empty($B)) {
    // Check if we need to display a single event.
    if ($A['new'] == true) {
        $cid = COM_applyFilter($A['cid'], true);
        $calendars = new Acalendarv2();
        // Get the calendars where the user has write right for displaying the dropbox
        $calendars->getCalendars(3); 
        $page .= calendarv2_display_form($calendars);
    }
    if (isset($A['eid'])) {
        $event = new Event();
        $event->get_event($A['eid'], 'c2_events');
        $page .= calendarv2_single_event($event);
    }
    if (isset($A['day'])) {
            $page .= calendarv2_day_events($_GET, $calendar);
    }
}
// Check if mofication of an event or deletion is asked by a $_POST variable
else {
    $event = new Event();
    if (isset($B['modify'])) {
        $event->get_event($B['eid'], 'c2_events');
        // Creates a template for a single instance modification
        $page = calendarv2_modify_event($event);
    }
    if (isset($B['delete'])) {
        $event->delete($B['eid']);
        $page .= COM_refresh("index.php?alert=1");
    }
    if (isset($B['modify_eid']) && !isset($B['cancel'])) {
        try {
            $event->modify($B);
        } catch (Exception $e) {
            $errors = $e->getMessage();
        }
        if (empty($errors)) {
            $page = calendarv2_single_event($event);
        }
        else { 
            $page .= COM_showMessageText($errors, "Error");
            $page .= calendarv2_modify_event($event);
        }
        
    }
    if (isset($B['delete_whole'])) {
        calendarv2_delete_recurring($B['hidden_parent']);
        $page .= COM_refresh("index.php");
    } 
    // Handle things if an event is subbmited via POST
    if (isset($B['submit'])) {
        if ($B['recurring_type'] == 1) {
            try {
                $event = new Event($B);
            } catch (Exception $e) {
                $errors = $e->getMessage();
            }
        }
        else {
            $event = new Revent($B);
        }
        if ($B['calendar_cid'] == 1) {
            if (empty($errors)) {
                if (SEC_hasRights('calendarv2.admin')) {
                    plugin_savesubmission_calendarv2($event, false);
                    COM_output(COM_refresh('index.php?alert=2'));
                    exit();
                }
                else {
                    plugin_savesubmission_calendarv2($event, true);
                    COM_output(COM_refresh('index.php?alert=3'));
                    exit();
                }
            }
        }
        else {
            if (calendarv2_checkCalendar($B['calendar_cid'], $_USER['uid'], 3)) {
                plugin_savesubmission_calendarv2($event, false);
                COM_output(COM_refresh('index.php?alert=2'));
                exit();
            }
        }
    } 
    if (isset($B['cancel'])) {
        COM_output(COM_refresh('index.php'));
        exit(); 
    }
}

    


// MAIN
$display .= COM_siteHeader('menu', $LANG_CALENDARV2_1['plugin_name']);
$display .= COM_startBlock($LANG_CALENDARV2_1['plugin_name']);
$display .= $page;
$display .= COM_endBlock();
$display .= COM_siteFooter();

COM_output($display);

?> 
 
