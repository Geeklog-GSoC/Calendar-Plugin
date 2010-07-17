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
    'lang_every' => 'Every',
    'lang_modify_msg' => 'Modify Event',
    'lang_calendars' => 'Calendars'
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

$LANG_CALENDARV2_SINGLE = array (
    'event_title' => 'Event Title:',
    'when' => 'When:',
    'where' => 'Where:',
    'description' => 'Description:',
    'lang_modify' => 'Modify',
    'lang_delete' => 'Delete',
    'go_back' => 'Go back',
    'lang_deleteall' => 'Delete All'
);

$LANG_CALENDARV2_MODERATION = array (
    'submissionlabel' => 'Calendarv2 Moderation',
    '1' => 'Event Id',
    '2' => 'Title',
    '3' => 'Description',
    '4' => 'Location'
);

$LANG_CALENDARV2_EVENT = array (
    'back' => '[ Go back to Calendar ]',
    'lang_create_new' => 'Create new',
    'calendar_name' => 'Calendar Name'
); 

$LANG_confignames['calendarv2'] = array(
    'folder' => 'Default plugin folder',
    'calendarlimit' => 'Number of calendars an user can have',
    'sitewide' => 'Annonymous users can see the site wide calendar',
    'zones' => 'Default Time Zone'
);

$LANG_configsubgroups['calendarv2'] = array(
    'sg_main' => 'Main Settings'
);

$LANG_configselects['calendarv2'] = array(
        0 => array('True' => 1, 'False' => 0),
        1 => array('True' => TRUE, 'False' => FALSE)
        // ripped from ultramegatech.com
        /*2 => array('(GMT-12:00) International Date Line West' => 'Kwajalein',
            '(GMT-11:00) Midway Island' => 'Pacific/Midway',
            '(GMT-11:00) Samoa' => 'Pacific/Samoa' ,
            '(GMT-10:00) Hawaii' => 'Pacific/Honolulu',
            '(GMT-09:00) Alaska' => 'America/Anchorage',
            '(GMT-08:00) Pacific Time (US &amp; Canada)' => 'America/Los_Angeles',
            '(GMT-08:00) Tijuana, Baja California' => 'America/Tijuana',
            '(GMT-07:00) Mountain Time (US &amp; Canada)' => 'America/Denver' ,
            '(GMT-07:00) Chihuahua' => 'America/Chihuahua' ,
            '(GMT-07:00) Mazatlan' => 'America/Mazatlan'  ,
            '(GMT-07:00) Arizona' => 'America/Phoenix' ,
            '(GMT-06:00) Saskatchewan' => 'America/Regina' ,
            '(GMT-06:00) Central America' => 'America/Tegucigalpa',
            '(GMT-06:00) Central Time (US &amp; Canada)' => 'America/Chicago',
            '(GMT-06:00) Mexico City' => 'America/Mexico_City' ,
            '(GMT-06:00) Monterrey' => 'America/Monterrey' ,
            '(GMT-05:00) Eastern Time (US &amp; Canada)' => 'America/New_York',
            '(GMT-05:00) Bogota' => 'America/Bogota',
            '(GMT-05:00) Lima' => 'America/Lima',
            '(GMT-05:00) Rio Branco' => 'America/Rio_Branco',
            '(GMT-05:00) Indiana (East)' => 'America/Indiana/Indianapolis',
            '(GMT-04:30) Caracas' => 'America/Caracas',
            '(GMT-04:00) Atlantic Time (Canada)' => 'America/Halifax',
            '(GMT-04:00) Manaus' => 'America/Manaus',
            '(GMT-04:00) Santiago' => 'America/Santiago',
            '(GMT-04:00) La Paz' => 'America/La_Paz',
            '(GMT-03:30) Newfoundland' => 'America/St_Johns',
            '(GMT-03:00) Georgetown' => 'America/Argentina/Buenos_Aires',
            '(GMT-03:00) Brasilia' => 'America/Sao_Paulo',
            '(GMT-03:00) Greenland' => 'America/Godthab',
            '(GMT-03:00) Montevideo' => 'America/Montevideo',
            '(GMT-02:00) Mid-Atlantic' => 'Atlantic/South_Georgia',
            '(GMT-01:00) Azores' => 'Atlantic/Azores',
            '(GMT-01:00) Cape Verde Is.' => 'Atlantic/Cape_Verde',
            '(GMT) Dublin' => 'Europe/Dublin',
            '(GMT) Lisbon' => 'Europe/Lisbon',
            '(GMT) London' => 'Europe/London',
            '(GMT) Monrovia' => 'Africa/Monrovia',
            '(GMT) Reykjavik' => 'Atlantic/Reykjavik',
            '(GMT) Casablanca' => 'Africa/Casablanca',
            '(GMT+01:00) Belgrade' => 'Europe/Belgrade',
            '(GMT+01:00) Bratislava' => 'Europe/Bratislava',
            '(GMT+01:00) Budapest' => 'Europe/Budapest',
            '(GMT+01:00) Ljubljana' => 'Europe/Ljubljana',
            '(GMT+01:00) Prague' => 'Europe/Prague',
            '(GMT+01:00) Sarajevo' => 'Europe/Sarajevo',
            '(GMT+01:00) Skopje' => 'Europe/Skopje',
            '(GMT+01:00) Warsaw' => 'Europe/Warsaw',
            '(GMT+01:00) Zagreb' => 'Europe/Zagreb',
            '(GMT+01:00) Brussels' => 'Europe/Brussels',
            '(GMT+01:00) Copenhagen' => 'Europe/Copenhagen',
            '(GMT+01:00) Madrid' => 'Europe/Madrid',
            '(GMT+01:00) Paris' => 'Europe/Paris',
            '(GMT+01:00) West Central Africa' => 'Africa/Algiers',
            '(GMT+01:00) Amsterdam' => 'Europe/Amsterdam',
            '(GMT+01:00) Berlin' => 'Europe/Berlin',
            '(GMT+01:00) Rome' => 'Europe/Rome',
            '(GMT+01:00) Stockholm' => 'Europe/Stockholm',
            '(GMT+01:00) Vienna' => 'Europe/Vienna',
            '(GMT+02:00) Minsk' => 'Europe/Minsk',
            '(GMT+02:00) Cairo' => 'Africa/Cairo',
            '(GMT+02:00) Helsinki' => 'Europe/Helsinki',
            '(GMT+02:00) Riga' => 'Europe/Riga',
            '(GMT+02:00) Sofia' => 'Europe/Sofia',
            '(GMT+02:00) Tallinn' => 'Europe/Tallinn',
            '(GMT+02:00) Vilnius' => 'Europe/Vilnius',
            '(GMT+02:00) Athens' => 'Europe/Athens',
            '(GMT+02:00) Bucharest' => 'Europe/Bucharest',
            '(GMT+02:00) Istanbul' => 'Europe/Istanbul',
            '(GMT+02:00) Jerusalem' => 'Asia/Jerusalem',
            '(GMT+02:00) Amman' => 'Asia/Amman',
            '(GMT+02:00) Beirut' => 'Asia/Beirut',
            '(GMT+02:00) Windhoek' => 'Africa/Windhoek',
            '(GMT+02:00) Harare' => 'Africa/Harare',
            '(GMT+03:00) Kuwait' => 'Asia/Kuwait',
            '(GMT+03:00) Riyadh' => 'Asia/Riyadh',
            '(GMT+03:00) Baghdad' => 'Asia/Baghdad',
            '(GMT+03:00) Nairobi' => 'Africa/Nairobi',
            '(GMT+03:00) Tbilisi' => 'Asia/Tbilisi',
            '(GMT+03:00) Moscow' => 'Europe/Moscow',
            '(GMT+03:00) Volgograd' => 'Europe/Volgograd',
            '(GMT+03:30) Tehran' => 'Asia/Tehran' ,
            '(GMT+04:00) Muscat' => 'Asia/Muscat',
            '(GMT+04:00) Baku' => 'Asia/Baku',
            '(GMT+04:00) Yerevan' => 'Asia/Yerevan',
            '(GMT+05:00) Ekaterinburg' => 'Asia/Yekaterinburg',
            '(GMT+05:00) Karachi' => 'Asia/Karachi',
            '(GMT+05:00) Tashkent' => 'Asia/Tashkent',
            '(GMT+05:30) Calcutta' => 'Asia/Kolkata',
            '(GMT+05:30) Sri Jayawardenepura' => 'Asia/Colombo',
            '(GMT+05:45) Kathmandu' => 'Asia/Katmandu',
            '(GMT+06:00) Dhaka' => 'Asia/Dhaka',
            '(GMT+06:00) Almaty' => 'Asia/Almaty',
            '(GMT+06:00) Novosibirsk' => 'Asia/Novosibirsk',
            '(GMT+06:30) Yangon (Rangoon)' => 'Asia/Rangoon',
            '(GMT+07:00) Krasnoyarsk' => 'Asia/Krasnoyarsk',
            '(GMT+07:00) Bangkok' => 'Asia/Bangkok',
            '(GMT+07:00) Jakarta' => 'Asia/Jakarta',
            '(GMT+08:00) Beijing' => 'Asia/Brunei',
            '(GMT+08:00) Chongqing' => 'Asia/Chongqing',
            '(GMT+08:00) Hong Kong' => 'Asia/Hong_Kong',
            '(GMT+08:00) Urumqi' => 'Asia/Urumqi',
            '(GMT+08:00) Irkutsk' => 'Asia/Irkutsk',
            '(GMT+08:00) Ulaan Bataar' => 'Asia/Ulaanbaatar',
            '(GMT+08:00) Kuala Lumpur' => 'Asia/Kuala_Lumpur',
            '(GMT+08:00) Singapore' => 'Asia/Singapore',
            '(GMT+08:00) Taipei' => 'Asia/Taipei',
            '(GMT+08:00) Perth' => 'Australia/Perth',
            '(GMT+09:00) Seoul' => 'Asia/Seoul',
            '(GMT+09:00) Tokyo' => 'Asia/Tokyo',
            '(GMT+09:00) Yakutsk' => 'Asia/Yakutsk',
            '(GMT+09:30) Darwin' => 'Australia/Darwin' ,
            '(GMT+09:30) Adelaide' => 'Australia/Adelaide',
            '(GMT+10:00) Canberra' => 'Australia/Canberra',
            '(GMT+10:00) Melbourne' => 'Australia/Melbourne',
            '(GMT+10:00) Sydney' => 'Australia/Sydney',
            '(GMT+10:00) Brisbane' => 'Australia/Brisbane',
            '(GMT+10:00) Hobart' => 'Australia/Hobart',
            '(GMT+10:00) Vladivostok' => 'Asia/Vladivostok',
            '(GMT+10:00) Guam' => 'Pacific/Guam',
            '(GMT+10:00) Port Moresby' => 'Pacific/Port_Moresby',
            '(GMT+11:00) Magadan' => 'Asia/Magadan',
            '(GMT+12:00) Fiji' => 'Pacific/Fiji',
            '(GMT+12:00) Kamchatka' => 'Asia/Kamchatka',
            '(GMT+12:00) Auckland' => 'Pacific/Auckland',
            '(GMT+13:00) Nukualofa' => 'Pacific/Tongatapu') */
);

$LANG_fs['calendarv2'] = array(
            'fs_main' => 'Calendarv2 Settings'
            );


// Messages for the plugin upgrade
$PLG_calendarv2_MESSAGE3002 = $LANG32[9]; // "requires a newer version of Geeklog"

?>
