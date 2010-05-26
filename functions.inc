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
    global $_CONF, $LANG_CALENDARV2_1;

    $url = $_CONF['site_url'] . '/calendarv2/index.php';
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
* @return array Array containing (plugin name, plugin admin url, # of plugin items or '')
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
        'tables' => array('calendarv2'),
        /* give the full name of the group, as in the db */
        'groups' => array('calendarv2 Admin'),
        /* give the full name of the feature, as in the db */
        'features' => array('calendarv2.admin'),
        /* give the full name of the block, including 'phpblock_', etc */
        'php_blocks' => array(),
        /* give all vars with their name */
        'vars' => array()
    );

    return $out;
}

 /**
* Get path for the tmplate files.
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

function calendarv2_display()
{
    global $_CONF;
    $page = new Template(calendarv2_templatePath());
    $page->set_file(array ('page' => 'main.thtml'));
    $page->set_var('site_url', $_CONF['site_url']);
    $page->set_var('calendar', "test_text");
    $page->set_var('display_today_events', 'today events');
    $form = calendarv2_form_display();
    $page->set_var('event_form', $form);
    $retval .= $page->finish($page->parse('output', 'page'));
    return $retval;
}

function calendarv2_form_display()
{
    global $_CONF, $LANG_CALENDARV2_FORM;
    $page = new Template(calendarv2_templatePath());
    $page->set_file(array ('page' => 'form.thtml'));
    $lang = $LANG_CALENDARV2_FORM;
    foreach ($lang as $key => $value) {
        $page->set_var($key, $value);
    }
    $time = time();
    $date = date('m/d/Y');
    $start_minute = date('h:i A');
    $page->set_var('default_start_date', $date);
    $page->set_var('default_end_date' , $date);
    $page->set_var('default_start_time', $start_minute);
    $page->set_var('default_end_time', $start_minute);
    $retval .= $page->finish($page->parse('output', 'page'));
    return $retval; 
     
}

?>
