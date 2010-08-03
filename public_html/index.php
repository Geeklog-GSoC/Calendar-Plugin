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
if (!in_array('calendarv2', $_PLUGINS)) {
    echo COM_refresh($_CONF['site_url'] . '/index.php');
    exit;
}
require_once $_CONF['path'] . 'plugins/calendarv2/classes/calendarv2.class.php';
require_once $_CONF['path'] . 'plugins/calendarv2/classes/eventv2.class.php';
require_once $_CONF['path'] . 'plugins/calendarv2/classes/reventv2.class.php';
require_once $_CONF['path'] . 'plugins/calendarv2/classes/aeventsv2.class.php';
require_once $_CONF['path'] . 'plugins/calendarv2/classes/iCalcreator.class.php';
$A = $_GET;
$display = '';

// If the user is not logged in set his $_USER value
if (COM_isAnonUser()) {
    $_USER['uid'] = 1;
}

// CHeck to see if a message should be displayed
if (isset($A['alert'])) {
    $alert = COM_applyFilter($A['alert'], true);
    $page .= COM_showMessageText($LANG_CALENDARV2_MESSAGES[$alert]);
}

// Check if the current user requested a specific calendar
// If not show him first available calendar or an error message if none.
if (isset($A['cid'])) {
    $cid = COM_applyFilter($A['cid'], true);
}
else {   
    $cid = 1;
    // If the user is not logged in, check to see if a calendar is
    // available for him
    if (($_CAV2_CONF['sitewide']) and ($_USER['uid'] == 1)) {
        $readable = new Acalendarv2();
        $readable->getCalendars(2);
        if ($readable->getNum() == 0) {
            $errors = "There are no calendars to display";
        }
        else {
            // Display the first readable Calendar
            $cid = $readable[0]->getCid(); 
        }
    }  
}



// Here we have either the user logged in and the display the site wide calendar
// or the user is anonymous and it's displayed a readable calendar, or 
// the user is displayed an error message

// Check if the current user can acces the requested calendar
// he must have read rights.
if (empty($errors)) {
    if (!calendarv2_checkCalendar($cid, $_USER['uid'], 2)) {
        $errors .= "The calendar you requested is not available for you";
    } 
}
    
// If creation of a new calendar was requested display the form
if ($A['display'] == 'new') {
        $page .= calendarv2_display_calendars_new();
}

// Create a new calendar
if (isset($_POST['calendar_submit'])) {
    // Check if the user has reached max calendars
    if (calendarv2_checkMaxCalendar() and ($_USER['uid'] != 1)) {
        calendarv2_create_calendar($_POST);
    }
    else {
        $errors = "You cannot create anymore calendars";
    }
} 

if (empty($errors)) {  // if everything is allright then display the current calendar
    $calendars = new Acalendarv2();
    // Get the calendars where the user has read right.
    $calendars->getCalendars(2);
    // Get the calendar with the selected cid.
    $calendar = new Calendarv2();
    $calendar->setCid($cid);   
    $page .= calendarv2_display($A, $calendars, $calendar);
} 

$page .= $errors;

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
