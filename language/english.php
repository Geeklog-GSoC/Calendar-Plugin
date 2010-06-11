<?php

/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | calendarv2 Plugin 0.1                                                     |
// +---------------------------------------------------------------------------+
// | english.php                                                               |
// |                                                                           |
// | English language file                                                     |
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

/**
* Import Geeklog plugin messages for reuse
*
* @global array $LANG32
*/
global $LANG32;

// +---------------------------------------------------------------------------+
// | Array Format:                                                             |
// | $LANGXX[YY]:  $LANG - variable name                                       |
// |               XX    - specific array name                                 |
// |               YY    - phrase id or number                                 |
// +---------------------------------------------------------------------------+

$LANG_CALENDARV2_1 = array(
    'plugin_name' => 'calendarv2',
    'hello' => 'Hello, world!' // this is an example only - feel free to remove
);

$LANG_CALENDARV2_FORM = array (
    'lang_on' => 'on',
    'lang_days' => 'Days',
    'lang_intro_msg' => 'Add Event' ,
    'lang_event_title' => 'Event title',
    'lang_event_location' => 'Event location',
    'lang_event_description' => 'Event description',
    'lang_add' => 'Save Event',
    'lang_event_at' => 'at',
    'lang_event_ends' => 'Event ends',
    'lang_event_starts' => 'Event starts',
    'lang_event_all_day' => 'All day event',
    'lang_daily' => 'Every Day',
    'lang_weekley' => 'Every week day',
    'lang_monthly' => 'Every Month',
    'lang_yearly' => 'Every Year',
    'lang_repeats' => 'Repeats',
    'lang_does_not' => 'Does not repeat',
    'lang_monday' => 'Monday',
    'lang_tuesday' => 'Tuesday',
    'lang_wednesday' => 'Wensday',
    'lang_thursday' => 'Thursday',
    'lang_friday' => 'Friday',
    'lang_saturday' => 'Saturday',
    'lang_sunday' => 'Sunday',
    'lang_which_day' => 'Which day',
    'lang_date_recurring' => 'Recuring ends',
    'lang_date_never' => 'Never',
    'lang_every' => 'Every'
);

$LANG_CALENDARV2_MONTH = array (
    '1' => 'January',
    '2' => 'February',
    '3' => 'March',
    '4' => 'April',
    '5' => 'May',
    '6' => 'June',
    '7' => 'July', 
    '8' => 'August',
    '9' => 'September',
    '10' => 'Octomber',
    '11' => 'November',
    '12' => 'December'
);

$LANG_CALENDARV2_EVENT = array (
    'back' => '[ Go back to Calendar ]'
); 

$LANG_confignames['calendarv2'] = array(
    'folder' => 'Default plugin folder'
);

$LANG_configsubgroups['calendarv2'] = array(
    'sg_main' => 'Main Settings'
);

$LANG_fs['calendarv2'] = array(
    'fs_main' => 'Calendar Settings'
);


// Messages for the plugin upgrade
$PLG_calendarv2_MESSAGE3002 = $LANG32[9]; // "requires a newer version of Geeklog"

?>
