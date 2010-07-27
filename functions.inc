<?php

/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | calendarv2 Plugin 0.1                                                     |
// +---------------------------------------------------------------------------+
// | functions.inc                                                             |
// |                                                                           |
// | This file does two things: 1) it implements the necessary Geeklog Plugin  |
// | API methods and 2) implements all the common code needed by this plugin.  |
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

if (strpos(strtolower($_SERVER['PHP_SELF']), 'functions.inc') !== false) {
    die ('This file can not be used on its own.');
}

/**
 * Language file include
 */
$plugin_path = $_CONF['path'] . 'plugins/calendarv2/';
$langfile = $plugin_path . 'language/' . $_CONF['language'] . '.php';



if (file_exists($langfile)) {
    require_once $langfile;
} else {
    require_once $plugin_path . 'language/english.php';
}

/**
 * calendarv2 plugin table(s)
 */
$_TABLES['calendarv2'] = $_DB_table_prefix . 'calendarv2';
$_TABLES['c2events'] = $_DB_table_prefix . 'c2events';
$_TABLES['cv2submission'] = $_DB_table_prefix . 'cv2submission';
$_TABLES['recurring_events'] = $_DB_table_prefix . 'recurring_events';
$_TABLES['recurring_specification'] = $_DB_table_prefix . 'recurring_specification';

/**
* Load the config settings
*/
require_once $_CONF['path_system'] . 'classes/config.class.php';

$ca_config = config::get_instance();
$_CAV2_CONF = $ca_config->get_config('calendarv2');
$_CAV2_CONF['site_url'] = $_CONF['site_url'] . '/' . $_CAV2_CONF['folder'];


// +---------------------------------------------------------------------------+
// | Geeklog Plugin API Implementation                                         |
// +---------------------------------------------------------------------------+

/**
* Returns the item(s) for this plugin that should appear on the main menu
*
* NOTE: this MUST return the url/value pairs in the following format
* $<arrayname>[<label>] = <url>
*
* @return   mixed   menu entry, or boolean false if disabled / hidden
*
*/
function plugin_getmenuitems_calendarv2()
{
    global $_CONF,$_CAV2_CONF, $LANG_CALENDARV2_1;

    $url = $_CAV2_CONF['site_url'] . '/index.php';
    $menuitems[$LANG_CALENDARV2_1['plugin_name']] = $url;

    return $menuitems;
}

/**
* Geeklog is asking us to provide any items that show up in the type
* drop-down on search.php.  Let's users search for links.
*
* @return   array   (plugin name/entry title) pair for the dropdown
*
*/
/*
function plugin_searchtypes_calendarv2()
{
    global $LANG_CALENDARV2_1;

    $tmp['calendarv2'] = $LANG_CALENDARV2_1['plugin_name'];

    return $tmp;
}
*/

/**
* This searches for entries matching the user query and returns an array for the
* header and table rows back to search.php where it will be formated and printed
*
* @param    string  $query      Keywords user is looking for
* @param    date    $datestart  Start date to get results for
* @param    date    $dateend    End date to get results for
* @param    string  $topic      The topic they were searching in
* @param    string  $type       Type of items they are searching, or 'all' (deprecated)
* @param    int     $author     Get all results by this author
* @param    string  $keyType    search key type: 'all', 'phrase', 'any'
* @param    int     $page       page number of current search (deprecated)
* @param    int     $perpage    number of results per page (deprecated)
* @return   object              search result object
*
*/
/*
function plugin_dopluginsearch_calendarv2($query, $datestart, $dateend, $topic, $type, $author, $keyType, $page, $perpage)
{
    global $_TABLES, $LANG_CALENDARV2_1;

    // Make sure the query is SQL safe
    $query = trim(addslashes($query));

    // NOTE: Update SQL query to match the actual table used by the
    //       calendarv2 plugin!

    $sql = "SELECT cid AS id, title, description, UNIX_TIMESTAMP(date) AS date, owner_id AS uid, hits, ";
    $sql .= "CONCAT('/calendarv2/index.php?item=', cid) AS url ";
    $sql .= "FROM {$_TABLES['calendarv2']} WHERE date <> 1 ";
    $sql .= COM_getPermSQL('AND') . ' ';

    if (!empty($datestart) && !empty($dateend)) {
        $delim = substr($datestart, 4, 1);
        if (!empty($delim)) {
            $DS = explode($delim, $datestart);
            $DE = explode($delim, $dateend);
            $startdate = mktime(0,0,0,$DS[1],$DS[2],$DS[0]);
            $enddate = mktime(23,59,59,$DE[1],$DE[2],$DE[0]);
            $sql .= "AND (UNIX_TIMESTAMP(date) BETWEEN '$startdate' AND '$enddate') ";
        }
    }
    if (!empty ($author)) {
        $sql .= "AND (owner_id = '$author') ";
    }

    $search = new SearchCriteria('calendarv2', $LANG_CALENDARV2_1['plugin_name']);
    $columns = array('title', 'description');
    list($sql,$ftsql) = $search->buildSearchSQL($keyType, $query, $columns, $sql);
    $search->setSQL($sql);
    $search->setFTSQL($ftsql);
    $search->setRank(3);

    return $search;
}
*/

/**
* Implements a [calendarv2:] autotag.
*
* @param    string  $op         operation to perform
* @param    string  $content    item (e.g. story text), including the autotag
* @param    array   $autotag    parameters used in the autotag
* @param    mixed               tag names (for $op='tagname') or formatted content
*
*/
/*
function plugin_autotags_calendarv2($op, $content = '', $autotag = '')
{
    global $_CONF, $_TABLES, $LANG_CALENDARV2_1;

    if ($op == 'tagname') {
        return 'calendarv2';
    } elseif ($op == 'parse') {
        $cid = COM_applyFilter($autotag['parm1']);
        if (! empty($cid)) {
            $url = $_CONF['site_url'] . '/calendarv2/index.php?item=' . $cid);
            if (empty($autotag['parm2'])) {
                $linktext = 'calendarv2'; // or get title from db
            } else {
                $linktext = $autotag['parm2'];
            }
            $link = COM_createLink($linktext, $url);
            $content = str_replace($autotag['tagstr'], $link, $content);
        }

        return $content;
    }
}
*/

/**
* Return plugin entry for "Command and Control" (moderation.php)
*
* @return  array  Array containing (plugin name, admin url, url of plugin icon)
*
*/
function plugin_cclabel_calendarv2()
{
    global $_CONF, $LANG_CALENDARV2_1;

    $retval = array();
    if (SEC_hasRights('calendarv2.admin')) {
        $retval = array($LANG_CALENDARV2_1['plugin_name'],
            $_CONF['site_admin_url'] . '/plugins/calendarv2/index.php',
            plugin_geticon_calendarv2());
    }

    return $retval;
}

/**
* Return plugin entry for the Admins Only block
*
* @return array Array containing (plugin name, plugin admin url,  of plugin items or '')
*/
function plugin_getadminoption_calendarv2()
{
    global $_CONF, $LANG_CALENDARV2_1;

    if (SEC_hasRights('calendarv2.admin')) {
        return array($LANG_CALENDARV2_1['plugin_name'],
            $_CONF['site_admin_url'] . '/plugins/calendarv2/index.php', 0);
    }
}

/**
* Returns the URL of the plugin's icon
*
* @return   string      URL of the icon
*
*/
function plugin_geticon_calendarv2()
{
    global $_CONF;

    // popular alternative location:
    // return $_CONF['site_url'] . '/calendarv2/images/calendarv2.png';

    return $_CONF['site_admin_url'] . '/plugins/calendarv2/images/calendarv2.png';
}

/**
 * Returns the current plugin code version
 *
 * @return string    plugin version
 */
function plugin_chkVersion_calendarv2()
{
    global $_CONF;

    require_once $_CONF['path'] . 'plugins/calendarv2/autoinstall.php';

    $inst_parms = plugin_autoinstall_calendarv2('calendarv2');

    return $inst_parms['info']['pi_version'];
}

/**
* Update the plugin
*
* @return   int     Number of message to display or true for success
*
*/
function plugin_upgrade_calendarv2()
{
    global $_CONF, $_TABLES;

    $installed_version = DB_getItem($_TABLES['plugins'], 'pi_version',
                                    "pi_name = 'calendarv2'");
    $code_version = plugin_chkVersion_calendarv2();
    if ($installed_version == $code_version) {
        // nothing to do
        return true;
    }

    require_once $_CONF['path'] . 'plugins/calendarv2/autoinstall.php';

    if (! plugin_compatible_with_this_version_calendarv2('calendarv2')) {
        return 3002;
    }

    // other update code goes here

    // update plugin version number
    $inst_parms = plugin_autoinstall_calendarv2('calendarv2');
    $pi_gl_version = $inst_parms['info']['pi_gl_version'];

    DB_query("UPDATE {$_TABLES['plugins']} SET pi_version = '$code_version', pi_gl_version = '$pi_gl_version' WHERE pi_name = 'calendarv2'");

    return true;
}

/**
* Automatic uninstall function for plugins
*
* This code is automatically uninstalling the plugin.
* It passes an array to the core code function that removes
* tables, groups, features and php blocks from the tables.
* Additionally, this code can perform special actions that cannot be
* foreseen by the core code (interactions with other plugins for example)
*
* @return   array   Plugin information
*
*/
function plugin_autouninstall_calendarv2()
{
    $out = array (
        /* give the name of the tables, without $_TABLES[] */
        'tables' => array('calendarv2', 'c2events', 'cv2submission', 'recurring_events', 'recurring_specification'),
        /* give the full name of the group, as in the db */
        'groups' => array('calendarv2 Admin', 'calendarv2Moderator'),
        /* give the full name of the feature, as in the db */
        'features' => array('calendarv2.admin, calendarv2.moderate, calendarv2.submit'),
        /* give the full name of the block, including 'phpblock_', etc */
        'php_blocks' => array(phpblock_calendarv2),
        /* give all vars with their name */
        'vars' => array()
    );

    return $out;
}

 /**
* Get path for the template files.
*
* @param    string  $path   subdirectory within the base template path
* @return   string          full path to template directory
*
*/
function calendarv2_templatePath ($path = '')
{
    global $_CONF;

    if (empty ($path)) {
        $layout_path = $_CONF['path_layout'] . calendarv2;
    } else {
        $layout_path = $_CONF['path_layout'] . calendarv2 . '/' . $path;
    }

    if (is_dir ($layout_path)) {
        $retval = $layout_path;
    } else {
        $retval = $_CONF['path'] . 'plugins/calendarv2/templates';
        if (!empty ($path)) {
            $retval .= '/' . $path;
        }
    }

    return $retval;
}
 
 /**
* Creates the main page from the template
*
* @param    int  $month   current Month to be displayed
* @param    int  $year   current Year to be displayed
* @param    cid         current calendar to be displayed
*
* @return   string      proper page made from template
*
*/  

function calendarv2_display($A, $calendars, $calendar)
{
    
    global $_CONF, $_CAV2_CONF;
    $page = new Template(calendarv2_templatePath());
    $page->set_file(array ('page' => 'main.thtml'));
    $page->set_var('site_url', $_CAV2_CONF['site_url']);
    $page->set_var('calendar', "test_text");
    $page->set_var('display_today_events', 'today events');
    $form = calendarv2_form_display($A, $calendars);
    $page->set_var('event_form', $form);
    $minimal = calendarv2_minimal_display($A, $calendar);
    $page->set_var('minimal' , $minimal);
    $retval .= $page->finish($page->parse('output', 'page'));
    return $retval;
}
 
  /**
* Creates the form page from the template
*
* @param    array   Post Variables.
* @return   string  the page from template
*
*/  

function calendarv2_form_display($A, $calendars)
{
    
    global $_CONF, $LANG_CALENDARV2_FORM, $_GROUPS, $LANG_ACCESS, $_USER;
    $page = new Template(calendarv2_templatePath());
    $page->set_file(array ('page' => 'form.thtml',
                            'calendar' => 'calendars.thtml'));
    $lang = $LANG_CALENDARV2_FORM;
    foreach ($lang as $key => $value) {
            $page->set_var($key, $value);
    }
    $date = new DateTime();
    if((!empty($A['month'])) && !(empty($A['year']))) {
        $date->setDate(intval($A['year']), intval($A['month']), 1);
    }      
    $timezone = TimeZoneConfig::getUserTimeZone();
    $timezone = new DateTimeZone($timezone);
    $date->setTimezone($timezone); 
        
    $page->set_var('month_recurring_day' , $date->format('d'));
    $page->set_var('year_recurring_day' , $date->format('m/d/Y'));
    $page->set_var('default_start_date', $date->format('m/d/Y'));
    $page->set_var('default_end_date' , $date->format('m/d/Y'));
    $page->set_var('default_start_time', $date->format('h:i A'));
    $page->set_var('default_end_time', $date->format('h:i A'));
    $page->set_var('calendar', calendarv2_display_calendars($calendars));
    $page->set_var('permissions', SEC_getPermissionsHTML(3, 2, 2, 0));
    $group_id = $_GROUPS['calendarv2 Admin'];
    $acces = 3;
    $page->set_var('lang_owner' , $LANG_ACCESS['owner']);
    $ownername = COM_getDisplayName ($_USER['uid']);
    $page->set_var('owner_name' , $ownername);
    $page->set_var('lang_group', $LANG_ACCESS['group']);
    $page->set_var('lang_permissions', $LANG_ACCESS['permissions']);
    $page->set_var('group_dropdown', SEC_getGroupDropdown($group_id, $acces));
    $retval .= $page->finish($page->parse('output', 'page'));
    
    // Generate Recurring Events in the database if it's the case (1 year have passed)
    calendarv2_create_events($date);
    return $retval; 
     
}
 /**
 *
 * Searches for events in the current day in the database. If there
 * are events there are displayed at a certain hour.
 * 
 * @param   array   Post Variables
 * @param   object  Calendar to seach in
 * @return  string  display from template
 */   

function calendarv2_day_events($A, $calendar)
{
    global $_CAV2_CONF, $LANG_CALENDARV2_EVENT;
    $date = new DateTime();
    $date->setDate($A['year'] , $A['month'], $A['day']);
    $date->setTime(0 , 0, 0);
    $events = $calendar->getTodayEvents($date);
    $page = new Template(calendarv2_templatePath()); 
    $page->set_file(array ('page' => 'dayevent.thtml'));
    $page->set_var('site_url', $_CAV2_CONF['site_url']); 
    for ($i = 1; $i <= 24; $i++) {
        // No DateTime object needed for this display trick
        $page->set_var($i . '_hour' , date('h:i A' , mktime($i-1 , 0 ,0 ,0, 0 )));
    }
    $go_back_link = $_CAV2_CONF['site_url'] . '/index.php';
    $prev = COM_createLink($LANG_CALENDARV2_EVENT['back'] , $go_back_link);
    $page->set_var('go_back_link' , $prev);
    if($events->getNumEvents() > 0) {
        foreach($events as $key => $event) {
                if ($event) {
                    $event_link = $_CAV2_CONF['site_url'] . '/event.php?eid=' . $event->getEid();
                    $timezone = TimeZoneConfig::getUserTimeZone();
                    $timezone = new DateTimeZone($timezone);
                    $date_start = new DateTime('@' . $event->getDatestart());
                    $date_ends = new DateTime('@' . $event->getDateend());
                    $date_start->setTimezone($timezone);
                    $date_ends->setTimezone($timezone);
                    $new_event = COM_createLink($date_start->format('h:i A') . ' - ' . $date_ends->format('h:i A') .' '. $event->getTitle(), $event_link);
                    $page->set_var($date_start->format('G') + 1 . '_event', $new_event);
                } 
        }
    }
    $retval .= $page->finish($page->parse('output', 'page'));
    return $retval; 
}
    

/**
* Creates the calendar from the template
*
*
* @return   string      proper page made from template
*
*/ 

function calendarv2_minimal_display($A, $calendar, $block = false)
{
    global $_CONF, $_CAV2_CONF, $LANG_CALENDARV2_MONTH;

    $date = new DateTime();
    $generate = new DateTime();
    if (!isset($A['month']) && (!isset($A['year']))) {
        $A['month'] = $date->format('m');
        $A['year'] = $date->format('Y');
    }
    $matrix = $calendar->generateMatrix($A['month'], $A['year']);
    if (isset($A['month']) && isset($A['year'])) {
        $generate->setDate($A['year'], $A['month'], 1);
    }
    $page = new Template(calendarv2_templatePath());
    $page->set_file(array ('page' => 'month_view.thtml',
                            'each_element' => 'each_element.thtml',
                            'each_element2'=> 'each_element2.thtml'));
    $link = $_CAV2_CONF['site_url'] . "/event.php?cid={$calendar->getCid()}&";
    $link2 = $_CAV2_CONF['site_url'] . '/index.php?';
    // If we display the calendar in a block, we don't display the prev and next buttons
    if ($block == false) {
        $prev_month = $generate->format('m') - 1;
        if ($prev_month == 0) {
            $prev_year = -1;
            $prev_month = 12;
        }
        $prev_year += $generate->format('Y');
        $prev = COM_createLink('<<', $link2 . 'year=' . $prev_year .'&'. 'month=' . $prev_month . '&cid=' . $calendar->getCid());
        $page->set_var('prev', $prev);

        $next_month = $generate->format('m') + 1;
        if ($next_month == 13) {
            $next_month = 1;
            $next_year =  1;
        }
        $next_year += $generate->format('Y');
        $next = COM_createLink('>>', $link2 . 'year=' . $next_year . '&' . 'month=' . $next_month . '&cid=' . $calendar->getCid());
        $page->set_var('next', $next);
    }
    $month_name = $LANG_CALENDARV2_MONTH[intval($generate->format("m"))];
    $page->set_var('month', $month_name);
    $page->set_var('year' , $generate->format('Y'));
    $i = 0;
    for ($i = 1; $i <= 6; $i++) {
        for ($j = 0; $j < 7; $j++) {
            if(!empty($matrix[$i][$j])) {
                $page->set_var('strong' ,'');
                $page->set_var('end_strong', ''); 
                $day_link = COM_createLink($matrix[$i][$j], $link . 'day=' . $matrix[$i][$j] . '&year=' . $generate->format('Y') . '&month=' . $generate->format('m'));
                $page->set_var('value', $day_link);
                $date->setDate($generate->format("Y"), $generate->format("m"), $matrix[$i][$j]);
                $date->setTime(0, 0 , 0);
                $something = $calendar->getTodayEvents($date);
                $num =  $something->getNumEvents();
                if($num != 0) {
                    $page->set_var('strong' ,'<strong>');
                    $page->set_var('end_strong', '</strong>');
                }
                
                $page->parse('line' . $i , 'each_element2', true);
            }
            else {
                $page->set_var('value', ' ');
                $page->parse('line' . $i, 'each_element2', true);
            }
        }
    $page->finish($page->parse('calendar_view', 'each_element')); 
    }
    $retval .= $page->finish($page->parse('output', 'page'));
    return $retval;
}

/**
* Creates a page with a single event 
*
* @param    array  $details     an event object
*
* @return   string      proper page made from template
*
*/  

function calendarv2_single_event($event)
{
    $details = $event->get_details();
    global $CAV2_CONF, $LANG_CALENDARV2_SINGLE;
    // start creating the page based on event details
    $page = new Template(calendarv2_templatePath());
    $page->set_file(array ('page' => 'single_event.thtml',
                            'recurring' => 'recurring_info.thtml'));
    $page->set_var('site_url', $CAV2_CONF['site_url']);
    $pave->set_var('action' , 'event.php');
    $lang = $LANG_CALENDARV2_SINGLE;
    foreach ($lang as $key => $value) {
        $page->set_var($key, $value);
    }
    $timezone = TimeZoneConfig::getUserTimeZone();
    $timezone = new DateTimeZone($timezone);
    $datestart = new DateTime('@' . $details['datestart']);
    $dateend = new DateTime('@' . $details['dateend']);
    $datestart->setTimezone($timezone);
    $dateend->setTimezone($timezone);
    $page->set_var('event_title_value' , $details['title']);
    $page->set_var('when_content' , $datestart->format('h:i A') . ' - ' . $dateend->format('h:i A') . ' ');
    $page->set_var('where_content' , $details['location']);
    $page->set_var('description_content' , $details['description']);
    $page->set_var('hidden_eid' , $details['eid']);
    list($day, $month, $year) = array($datestart->format('d'), $datestart->format('m'), $datestart->format('Y'));
    $link = COM_createLink($lang['go_back'], $_CAV2_CONF['site_url'] . "/event.php?cid={$details['cid']}&day=$day&month=$month&year=$year");
    $page->set_var('go_back_link' , $link); 
    // if the event is recurring we will have to display aditional information
    if ($details['pid'] > 0) {
        $page->set_var('hidden_parent', $details['pid']);
        $page->set_var('recurring_info', $page->parse('output', 'recurring'));
    }
    $retval .= $page->finish($page->parse('output', 'page'));
    return $retval;
}

/**
* Gets details about the current event then generates a page from 
* a template
*
* @param    array  $details     an event object
*
* @return   string      proper page made from template
*
*/

function calendarv2_modify_event($event)
{
    global $LANG_CALENDARV2_FORM, $CAV2_CONF;
    $lang = $LANG_CALENDARV2_FORM;
    $details = $event->get_details();   
    $timezone = TimeZoneConfig::getUserTimeZone();
    $timezone = new DateTimeZone($timezone); 
    $date_end  = new DateTime('@' . $details['datestart']);
    $date_start = new DateTime('@' . $details['dateend']);
    $date_end->setTimezone($timezone);
    $date_start->setTimezone($timezone);
    $page = new Template(calendarv2_templatePath());
    $page->set_file(array ('page' => 'modify_instance.thtml'));
    foreach ($lang as $key => $value) {
            $page->set_var($key, $value);
    }
    if ($details['allday'] == 1) {
        $page->set_var('checked' , 'yes');
    }  
    $page->set_var('action' , 'event.php');
    $page->set_var('event_title', $details['title']);
    $page->set_var('event_description', $details['description']);
    $page->set_var('event_location', $details['location']);
    $page->set_var('default_start_date' , $date_start->format('m/d/Y'));
    $page->set_var('default_end_date' , $date_end->format('m/d/Y'));
    $page->set_var('default_start_time' , $date_start->format('h:i A'));
    $page->set_var('default_end_time' , $date_end->format('h:i A'));
    $page->set_var('modify_eid' , $details['eid']);
    $page->set_var('modify_cid' , $details['cid']);
    $retval .= $page->finish($page->parse('output', 'page')); 
    return $retval;
}

/**
* Creates a page from template with a <select> that has all calendars
* suplied by the calendars variable
*
* @param    array   a list of calendars 
*
* @return   string  html
*
*/ 

function calendarv2_display_calendars($calendars)
{   
    global $_CAV2_CONF, $LANG_CALENDARV2_EVENT;
    $page = new Template(calendarv2_templatePath());
    $page->set_file(array ('page' => 'calendars.thtml'));
    foreach($calendars as $key => $calendar) {
        $options .= "<option value = {$calendar->getCid()}> {$calendar->getTitle()} </option>";
    }
    $page->set_var('options', $options); 
    $link = $_CAV2_CONF['site_url'] . '/index.php?display=new';
    $link = COM_createLink($LANG_CALENDARV2_EVENT['lang_create_new'] , $link);
    $page->set_var('create_new_link' , $link);
    $retval .= $page->finish($page->parse('output', 'page'));
    return $retval;
} 

/**
*
* return the page that allows creating of mutliple calendars
*
*
* @return   string  html
*
*/  

function calendarv2_display_calendars_new()
{
    global $LANG_CALENDARV2_EVENT, $_GROUPS, $LANG_ACCESS, $_USER;
    $page = new Template(calendarv2_templatePath());
    $page->set_file(array ('page' => 'create_calendars.thtml'));
    $page->set_var('lang_calendar_name', $LANG_CALENDARV2_EVENT['calendar_name']); 
    $group_id = $_GROUPS['calendarv2 Admin'];
    $acces = 3;
    $page->set_var('lang_owner' , $LANG_ACCESS['owner']);
    $ownername = COM_getDisplayName ($_USER['uid']);
    $page->set_var('owner_name' , $ownername);
    $page->set_var('lang_group', $LANG_ACCESS['group']);
    $page->set_var('lang_permissions', $LANG_ACCESS['permissions']);
    $page->set_var('group_dropdown', SEC_getGroupDropdown($group_id, $acces)); 
    $page->set_var('permissions', SEC_getPermissionsHTML(3, 2, 2, 0));
    $retval .= $page->finish($page->parse('output', 'page'));
    return $retval;
} 

/**
*
* Adds the new created calendars to the database
*
*/   

function calendarv2_create_calendar($A) 
{
    global $_TABLES, $_USER; 
    if (is_array($A['group_id']) or is_array($A['perm_owner']) or is_array($A['perm_group']) or is_array($A['perm_anon'])) {
            list($perm_owner, $perm_group, $perm_members, $perm_anon) = 
            SEC_getPermissionValues($A['perm_owner'], $A['perm_group'] , $A['perm_members'], $A['perm_anon']); 
    }       
    $A['group_id'] = COM_applyFilter($A['group_id'], true);
    $name = addslashes($A['new_calendar_name']);
    $sql = "insert into {$_TABLES['calendarv2']} (owner_id, title, perm_owner, perm_group, perm_members, perm_anon, group_id) values" . 
            "('{$_USER['uid']}', '{$name}', '{$perm_owner}' , '{$perm_group}', '{$perm_members}', '{$perm_anon}', '{$A['group_id']}')";
    DB_query($sql);
}

 /**
*
* return a list of links for the calendars.
*
*
* @return   string  html
*
*/   


function calendarv2_display_calendar_links($calendars)
{
    global $_CAV2_CONF;
    $page = new Template(calendarv2_templatePath());
    $page->set_file(array ('page' => 'calendar_links.thtml'));
    $link = $_CAV2_CONF['site_url'] . '/index.php?';
    foreach($calendars as $key => $calendar) {
        $link_body .= COM_createLink($calendar->getTitle() . "  ", $link . 'cid=' . $calendar->getCid());
        $export_link .= COM_createLink("Export" . "  ", $link . 'cid=' . $calendar->getCid() . '&export=1');
        $page->set_var('link', $link_body);
        $page->set_var('export_link', $export_link);
        $page->parse('output', 'page', true);
    } 
    $retval .= $page->finish($page->parse('output', 'page'));
    return $retval;
}

function calendarv2_display_uploadForm($calendars)
{
    global $LANG_CALENDARV2_FORM;
    $page = new Template(calendarv2_templatePath());
    $page->set_file(array ('page' => 'uploadfile.thtml'));
    $page->set_var('lang_upload', $LANG_CALENDARV2_FORM['lang_upload']);
    $selects = calendarv2_display_calendars($calendars);
    $page->set_var('calendars', $selects);
    $retval .= $page->finish($page->parse('output', 'page'));
    return $retval;
}

// Site Wide Event Submission functions, Part of the moderation capability

function plugin_ismoderator_calendarv2()
{
    global $_USER, $_TABLES;
    return (SEC_hasRights('calendarv2.admin'));
}

function plugin_submissioncount_calendarv2()
{
    global $_TABLES;
    return DB_count($_TABLES['cv2submission']);
}

function plugin_savesubmission_calendarv2($event, $moderate)
{   
    $object_type = get_class($event);
    if ($object_type = 'Event') {        
        $eid = COM_makeSid();
        $event->setEid($eid);
    }
    $event->setModeration($moderate);
    $event->save_to_database();
}

function plugin_moderationvalues_calendarv2()
{
    global $_TABLES;
    return array('eid' , $_TABLES['c2events'], 'eid, datestart, dateend, description, title, location', $_TABLES['cv2submission']);
}

function plugin_itemlist_calendarv2()
{
    global $_TABLES, $LANG_CALENDARV2_MODERATION;
    $lang = $LANG_CALENDARV2_MODERATION;
    if (plugin_ismoderator_calendarv2()) {
        $plugin = new Plugin();
        $plugin->submissionlabel = $lang['submissionlabel'];
        $plugin->getsubmissionssql = "select eid, location, description from {$_TABLES['cv2submission']}";
        $plugin->addSubmissionHeading($lang[2]);
        $plugin->addSubmissionHeading($lang[3]);
        return $plugin;
    }
}

function calendarv2_fixed_recurrence($start_generating, $end_generating, &$array, $rule) 
{
    $recurrence_start = new DateTime('@' . $array['datestart']);
    $recurrence_end = new DateTime('@' . $array['dateend']);
    $events = new Aevents(); 
    if ($recurrence_end > $end_generating) {
        return $events;
    }
    while ($start_generating < $end_generating) {
        $event = new Event();
        $array['datestart'] = $recurrence_start->format('U');
        $array['dateend'] = $recurrence_end->format('U');
        $event->load_event_from_DB_array($array);
        $event->setEid(COM_makeSid() . $i++);
        $event->setPid($array['preid']);
        $events->addEvent($event);
        $start_generating->modify($rule);
        $recurrence_start->modify($rule);
        $recurrence_end->modify($rule); 
    }
    return $events; 
}


// Searches in the recurring_events table and does dirty work.
function calendarv2_create_events(DateTime $last_event_generated = NULL) 
{
    global $_TABLES;
    // Here will have to be added exceptions hashtable
    $events = new Aevents();
    $sql = "select * from {$_TABLES['recurring_events']} left join {$_TABLES['recurring_specification']} on " . 
            "reid = preid where last_event is NULL or last_event < {$last_event_generated->format('U')} ";
    $result = DB_query($sql);
    
    while ($array = DB_fetchArray($result)) {
        $end_generating  = clone $last_event_generated;
        $start_generating = new DateTime('@' . $array['datestart']);
        if ($array['recurring_ends'] == 1) {
            $end_generating->modify("+1 year");
        }
        else {
            $end_generating = new DateTime('@' . $array['recurring_ends']);
        }
 
        if ($array['exception'] == 0) {
            $recurrence_start = new DateTime('@' . $array['datestart']);
            $recurrence_end = new DateTime('@' . $array['dateend']);
            // There are many, many types of recurrences that can / must be handled here.
            // They should be grouped so that there are not many code duplications
            // This part handles the fixed size recurrences. Daily, Monthly and Yearly.
                if (!is_null($array['day_period'])) {
                    $rule = ('+' . $array['day_period'] . ' days');
                    $events = calendarv2_fixed_recurrence($start_generating, $end_generating, &$array, $rule);
                }
                if (!is_null($array['month_period'])) {
                    $rule = ('+' .$array['month_period'] . ' months');
                    $events = calendarv2_fixed_recurrence($start_generating, $end_generating, &$array, $rule);
                }
                if (!is_null($array['year_period'])) {
                    $rule = ('+' .$array['year_period'] . ' years');
                    $events = calendarv2_fixed_recurrence($start_generating, $end_generating, &$array, $rule);
                }
                if (!is_null($array['which_day'])) {
                    $date_start = new DateTime('@'. $array['datestart']);
                    $date_end = new DateTime('@'. $array['dateend']);
                    $rule = ('+7 days');
                    
                    if ($date_start->format('w') == $array['which_day']) {
                        $events = calendarv2_fixed_recurrence($start_generating, $end_generating, $array, $rule);
                    }
                    else {
                        $jump1 = $array['which_day'] - $date_start->format('w');
                        $jump2 = $array['which_day'] - $date_end->format('w');
                        if ($jump1 < 0) {
                            $jump1 = $jump1 + 7;
                        }
                        if ($jump2 < 0) {
                            $jump2 = $jump2 + 7;
                        }
                        $date_start->modify('+' . $jump1 . ' days');
                        $date_end->modify('+' . $jump2 . ' days');
                        $array['datestart'] = $date_start->format('U');
                        $array['dateend'] = $date_end->format('U');
                        $events = calendarv2_fixed_recurrence($start_generating, $end_generating, $array, $rule);
                    }
                }
                foreach($events as $key => $event) {
                    $event->save_to_database();
                }
        }
        // After generating 1 year of recurring events, we must update our event in the
        // recurring events table so that in can spawn again, another year. 
        $end = new DateTime('@'. $array['dateend']);
        $start = new DateTime('@' . $array['datestart']);
        $sql = "update {$_TABLES['recurring_events']} set last_event = {$end_generating->format('U')}," . 
                "datestart = {$start->format('U')}, dateend = {$end->format('U')} where reid = {$array['reid']}";
        DB_query($sql);   
    }
}


//Deletes a recurring event
function calendarv2_delete_recurring($id) 
{
    global $_TABLES;
    $sql = "delete from {$_TABLES['recurring_events']} where reid = {$id}";
    DB_query($sql);
    $sql = "delete from {$_TABLES['recurring_specification']} where preid = {$id}";
    DB_query($sql);
    $sql = "delete from {$_TABLES['c2events']} where pid = {$id}";
    DB_query($sql);
}


// Verifies that a certain user has read or read-write acces to a certain calendar
function calendarv2_checkCalendar($cid, $user, $right) 
{
    global $_TABLES;
    $sql = "select * from {$_TABLES['calendarv2']} where (cid = '$cid')";
    $sql .= COM_getPermSQL('AND', $user, $right); 
    $result = DB_query($sql);
    $array = DB_fetchArray($result);
    if (is_array($array)) {         // If we have some results we have acces
        return true;
    }
    return false;

}

// Verifies if an user can create more calendars
function calendarv2_checkMaxCalendar() 
{                                                                 
    global $_CAV2_CONF, $_USER, $_TABLES;
    // if the user is admin or in calendarv2 group
    //  he can create as many calendars as he wants
    if ((SEC_inGroup('Root')) or (SEC_inGroup('Calendarv2 Admin'))) {
        return true;
    }
    
    // else check how many calendars a user has created so far
    $sql = "select * from {$_TABLES['calendarv2']} where owner_id = {$_USER['uid']}";
    $result = DB_query($sql);                           
    $result = DB_numRows($result);
    // if the cap is reached don't allow anymore
    if ($result >= $_CAV2_CONF['calendarlimit']) {
        return false;
    }
    return true;
}

function calendarv2_createDate($date, $timezone) 
{
    // If the tz is set to Z then we have an UTC time and we ignore the timezone
    if ($date['tz'] == 'Z') {
        $timezone = "UTC";
    } 
    $datetime = new DateTime();
    $datetime->setDate($date['year'] , $date['month'], $date['day']);
    $datetime->setTime($date['hour'] , $date['min'], $date['sec']);
    return $datetime;
    
}

function calendarv2_display_uploadEvents($calendar, $filename) 
{
    global $CAV2_CONF, $LANG_CALENDARV2_SINGLE;
    $page = new Template(calendarv2_templatePath());
    $page->set_file(array ('page' => 'uploadevents.thtml',
                            'single_event' => 'single_event.thtml',
                            'recurring_info' => 'recurring_info.thtml'));
    // I am using recurring_info because it curently does the job needed. Must be renaimed because does not work only with recurring.
    $pseudo = $calendar->getEvents();
    $lang = $LANG_CALENDARV2_SINGLE;
    foreach ($lang as $key => $value) {
        $page->set_var($key, $value);
    } 
    $page->set_var('action', 'upload.php');
    $page->set_var('infos', $lang['upload_info']);
    $page->set_var('lang_deleteall', $lang['aprove_all']);
    $page->set_var('val', 'a');                                                                     
    $page->set_var('buttons', $page->finish($page->parse('output', 'recurring_info')));
    $page->parse('output', 'page');
    $page->unset_var('lang_deleteall');
    foreach ($pseudo as $event) {
        $page->set_var('event_title_value', $event->getTitle());
        $page->set_var('description_content', $event->getDescription());
        $page->set_var('where_content', $event->getLocation());
        $page->set_var('when_content' , date("Y-m-d H-i-s", $event->getDateStart()));
        $page->set_var('filename', $filename);
        $page->set_var('hidden_eid' , $event->getEid());   
        $page->set_var('lang_deleteall', 'Aprove');                                                                     
        $page->set_var('recurring_info' , $page->finish($page->parse('output', 'recurring_info', true)));
        $page->set_var('uploads' , $page->parse('page', 'single_event', true));
    }
    $result = $page->finish($page->parse('output', 'page'));
    return $result;
} 

function calendarv2_exportics($cid)
{        
    global $_USER, $_CONF;
    //check to see if the user has read rights on the requested calendar
    $calendar = new calendarv2();
    $calendar->getCalendar($cid);
    if (calendarv2_checkCalendar($cid, $_USER['uid'], 2)) {
        $v = new vcalendar();
        $id = COM_makeSid();
        $v->setConfig('unique_id', "geeklog.net" . $id);
        $directory = $_CONF['path'] . '/plugins/calendarv2/uploads';
        $v->setConfig('directory', $directory);
        $v->setConfig('filename', $id . '.ics');
        $v->setConfig('calscale', "GREGORIAN");
        $v->setProperty('x-wr-calname', $id);
        $events = $calendar->getEvents();
        foreach ($events as $event) {
            $ev = new vevent();
            $start = new DateTime('@' . $event->getDatestart());
            $end = new DateTime('@' . $event->getDateend());
            $ev->setProperty('dtstart', $start->format('Y'), $start->format('m'), 
                $start->format('d'), $start->format('h'), $start->format('i'), $start->format('s'));
            $ev->setProperty('dtend', $end->format('Y'), $end->format('m'), 
                $end->format('d'), $end->format('h'), $end->format('i'), $end->format('s'));
            $description = $event->getDescription();
            $ev->setProperty('description', $description);
            $title = $event->getTitle();
            $ev->setProperty('summary', $title);
            $v->setComponent($ev);
        }
        $v->saveCalendar(); 
        $v->returnCalendar();
    }
}


// Manages the display of the calendar in a left or right block.
function phpblock_calendarv2()
{
    global $_USER, $_CONF;
    require_once $_CONF['path'] . 'plugins/calendarv2/classes/calendarv2.class.php';  
    require_once $_CONF['path'] . 'plugins/calendarv2/classes/eventv2.class.php';
    require_once $_CONF['path'] . 'plugins/calendarv2/classes/reventv2.class.php';
    require_once $_CONF['path'] . 'plugins/calendarv2/classes/aeventsv2.class.php'; 
    // Check the user
    if (COM_isAnonUser()) {
        $_USER['uid'] = 1;
    }
    else {
        $uid = $_USER['uid'];
    }
    // Only the site wide event will be displayed in a block.
    $cid = 1;
    // Check to see if the user can read the site-wide event 
    if (calendarv2_checkCalendar($cid, $uid, 2)) {
        $calendar = new Calendarv2();
        $calendar->getCalendar($cid);
        $result = calendarv2_minimal_display(NULL, $calendar, true);
    }
    else {
        $result = "You don't have acces to the site wide calendar";
    }
    return $result;
}
        
        
    

?>
