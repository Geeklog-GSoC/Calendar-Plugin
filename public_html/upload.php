 <?php

/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | calendarv2 Plugin 0.1                                                     |
// +---------------------------------------------------------------------------+
// | upload.php                                                                 |
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


require_once($_CONF['path_system'] . 'classes/upload.class.php');
require_once $_CONF['path'] . 'plugins/calendarv2/classes/iCalcreator.class.php';
require_once $_CONF['path'] . 'plugins/calendarv2/classes/eventv2.class.php';
require_once $_CONF['path'] . 'plugins/calendarv2/classes/calendarv2.class.php';
require_once $_CONF['path'] . 'plugins/calendarv2/classes/aeventsv2.class.php';
 

function calendarv2_getCalendarfromics($filename, $directory)
{
    $v = new vcalendar();
    $v->setConfig('directory', $directory);
    $v->setConfig('filename', $filename);
    $v->parse();
    $v->sort();
    $calendar = new Calendarv2();
    $calendar->setCid(COM_applyFilter($_POST['calendar_cid'], true));
    while ($comp = $v->getComponent()) {
        $title = $comp->getProperty("SUMMARY"); 
        if (!empty($title)) {
            $event = new Event();
            $eid = COM_makeSid();
            $event->setEid($eid);
            $start_date = calendarv2_createDate($comp->getProperty("DTSTART"), $timezone);
            $event->setStartDate($start_date);
            $end_date = calendarv2_createDate($comp->getProperty("DTEND"), $timezone);
            $event->setEndDate($end_date);
            $location = $comp->getProperty("location");
            $event->setLocation($location);
            $description = $comp->getProperty('description');
            $event->setDescription($description);
            $event->setTitle($title);
            $calendar->addEvent($event);
        } 
    }
    return $calendar;
}

if (isset($_POST['uploadfile'])) {            
    $upload = new upload();
    $upload->setLogFile ($_CONF['path'] . 'logs/error.log');
    $upload->setAllowedMimeTypes (array ('text/calendar' => '.ics'));
    $directory = $_CONF['path'] . 'plugins/calendarv2/uploads';
    if (!$upload->setPath ($directory)) {
        $upload->printErrors();
    }
    $filename = COM_makeSid();
    $filename .= ".ics";
    $upload->setFilenames($filename);
    $upload->setPerms('0644');
    $upload->uploadFiles();
    $upload->printErrors(); 
    $calendar = calendarv2_getCalendarfromics($filename, $directory);    
    $page = calendarv2_display_uploadEvents($calendar, $filename);
}

if (isset($_POST['delete_wholea'])) {
    $filename = $_POST['filename'];
    $directory = $_CONF['path'] . 'plugins/calendarv2/uploads';
    $calendar = calendarv2_getCalendarfromics($filename, $directory);
    $calendar->saveEvents();
}


if (isset($_POST['modify'])) {
    $filename = $_POST['filename'];
    $directory = $_CONF['path'] . 'plugins/calendarv2/uploads';
    $calendar = calendarv2_getCalendarfromics($filename, $directory); 
    $event = $calendar->getEvent($_POST['eid']); // Some bug here because another eid is created.
    $page = $calendarv2_modify_event($event);
}

if ($_GET['import'] == true) {
    $calendars = new Acalendarv2();
    // Get the calendars where the user has write right.
    $calendars->getCalendars(3); 
    $page = calendarv2_display_uploadForm($calendars); 
}

if ($_GET['export'] == true) {
    $cid = COM_applyFilter($_GET['cid']);
    calendarv2_exportics($cid);
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

