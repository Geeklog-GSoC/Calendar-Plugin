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
        'php_blocks' => array(),
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
    
    global $_CONF, $LANG_CALENDARV2_FORM;
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
    
        
    $page->set_var('month_recurring_day' , $date->format('d'));
    $page->set_var('year_recurring_day' , $date->format('m/d/Y'));
    $page->set_var('default_start_date', $date->format('m/d/Y'));
    $page->set_var('default_end_date' , $date->format('m/d/Y'));
    $page->set_var('default_start_time', $date->format('h:i A'));
    $page->set_var('default_end_time', $date->format('h:i A'));
    $page->set_var('calendar', calendarv2_display_calendars($calendars));
    $retval .= $page->finish($page->parse('output', 'page'));
    
    // Generate Recurring Events in the database if it's the case (1 year have passed)
    calendarv2_create_events($date);
    // Ening an important section 
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
    //var_dump($calendar->getCid());
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
                    $date_start = new DateTime('@' . $event->getDatestart());
                    $date_ends = new DateTime('@' . $event->getDateend());
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

function calendarv2_minimal_display($A, $calendar)
{
    
    $date = new DateTime();
    $generate = new DateTime();
    if (!isset($A['month']) && (!isset($A['year']))) {
        $A['month'] = $date->format('m');
        $A['year'] = $date->format('Y');
    }
    $matrix = $calendar->generateMatrix($A['month'], $A['year']);
    global $_CONF, $_CAV2_CONF, $LANG_CALENDARV2_MONTH;
    if (isset($A['month']) && isset($A['year'])) {
        $generate->setDate($A['year'], $A['month'], 1);
    }
    $page = new Template(calendarv2_templatePath());
    $page->set_file(array ('page' => 'month_view.thtml',
                            'each_element' => 'each_element.thtml',
                            'each_element2'=> 'each_element2.thtml'));
    $link = $_CAV2_CONF['site_url'] . "/event.php?cid={$calendar->getCid()}&";
    $link2 = $_CAV2_CONF['site_url'] . '/index.php?';
    $prev_month = $generate->format('m') - 1;
    if ($prev_month == 0) {
        $prev_year = -1;
        $prev_month = 12;
    }
    $prev_year += $generate->format('Y');
    $prev = COM_createLink('<<', $link2 . 'year=' . $prev_year .'&'. 'month=' . $prev_month);
    $page->set_var('prev', $prev);
    
    $next_month = $generate->format('m') + 1;
    if ($next_month == 13) {
        $next_month = 1;
        $next_year =  1;
    }
    $next_year += $generate->format('Y');
    $next = COM_createLink('>>', $link2 . 'year=' . $next_year . '&' . 'month=' . $next_month);
    $page->set_var('next', $next);
    
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
    global $_CAV2_CONF;
    $details = $event->get_details();
    global $CAV2_CONF, $LANG_CALENDARV2_SINGLE;
    // start creating the page based on event details
    $page = new Template(calendarv2_templatePath());
    $page->set_file(array ('page' => 'single_event.thtml')); 
    $page->set_var('site_url', $CAV2_CONF['site_url']);
    $lang = $LANG_CALENDARV2_SINGLE;
    foreach ($lang as $key => $value) {
        $page->set_var($key, $value);
    }
    $page->set_var('event_title_value' , $details['title']);
    $page->set_var('when_content' , date('h:i A', $details['datestart']) . ' - ' . date('h:i A', $details['dateend']) . ' ');
    $page->set_var('where_content' , $details['location']);
    $page->set_var('description_content' , $details['description']);
    $page->set_var('hidden_eid' , $details['eid']);
    list($day, $month, $year) = explode ('/' , date('d/m/Y' , $details['datestart']));
    $link = COM_createLink($lang['go_back'], $_CAV2_CONF['site_url'] . "/event.php?cid={$details['cid']}&day=$day&month=$month&year=$year");
    $page->set_var('go_back_link' , $link); 
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
    $date_end  = new DateTime('@' . $details['datestart']);
    $date_start = new DateTime('@' . $details['dateend']);
    $page = new Template(calendarv2_templatePath());
    $page->set_file(array ('page' => 'modify_instance.thtml'));
    foreach ($lang as $key => $value) {
            $page->set_var($key, $value);
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
    global $LANG_CALENDARV2_EVENT;
    $page = new Template(calendarv2_templatePath());
    $page->set_file(array ('page' => 'create_calendars.thtml'));
    $page->set_var('lang_calendar_name', $LANG_CALENDARV2_EVENT['calendar_name']);
    $retval .= $page->finish($page->parse('output', 'page'));
    return $retval;
} 

/**
*
* Adds the new created calendars to the database
*
*/   

function calendarv2_create_calendar($name) 
{
    global $_TABLES, $_USER;
    $name = addslashes($name);
    $sql = "insert into {$_TABLES['calendarv2']} (owner_id, title) values ('{$_USER['uid']}', '{$name}')";
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
        $link_body .= COM_createLink($calendar->getTitle(), $link . 'cid=' . $calendar->getCid());
        $link_body .= '<br>'; 
    } 
    $page->set_var('link', $link_body);
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
    $eid = COM_makeSid();
    $event->setEid($eid);
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
    while ($start_generating < $end_generating) {
        $event = new Event();
        $recurrence_start->modify($rule);
        $recurrence_end->modify($rule);
        $array['datestart'] = $recurrence_start->format('U');
        $array['dateend'] = $recurrence_end->format('U');
        $event->load_event_from_DB_array($array);
        $event->setEid(COM_makeSid() . $i++);
        $events->addEvent($event);
        $start_generating->modify($rule);
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


 
?>
