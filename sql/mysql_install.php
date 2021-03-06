<?php

/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | calendarv2 Plugin 0.1                                                     |
// +---------------------------------------------------------------------------+
// | mysql_install.php                                                         |
// |                                                                           |
// | Installation SQL                                                          |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2010 by the following authors:                              |
// |                                                                           |
// | Authors: Vlad Voicu - vladvoic AT gmail DOT com                           |
// +---------------------------------------------------------------------------+
// | Created with the Geeklog Plugin Toolkit.                                  |
// +---------------------------------------------------------------------------+
// |                                                                           |
// | This program is licensed under the terms of the GNU General Public License|
// | as published by the Free Software Foundation; either version 2            |
// | of the License, or (at your option) any later version.                    |
// |                                                                           |
// | This program is distributed in the hope that it will be useful,           |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of            |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.                      |
// | See the GNU General Public License for more details.                      |
// |                                                                           |
// | You should have received a copy of the GNU General Public License         |
// | along with this program; if not, write to the Free Software Foundation,   |
// | Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.           |
// |                                                                           |
// +---------------------------------------------------------------------------+

$_SQL[] = "
CREATE TABLE {$_TABLES['c2_calendars']} (
  cid int(10) NOT NULL auto_increment,
  title varchar(40) default NULL,
  owner_id mediumint(8) unsigned NOT NULL default '1',
  group_id mediumint(8) unsigned NOT NULL default '1',
  perm_owner tinyint(1) unsigned NOT NULL default '3',
  perm_group tinyint(1) unsigned NOT NULL default '2',
  perm_members tinyint(1) unsigned NOT NULL default '2',
  perm_anon tinyint(1) unsigned NOT NULL default '2',
  PRIMARY KEY (cid)
) TYPE=MyISAM
";

$_SQL[] = "
CREATE TABLE {$_TABLES['c2_events']} (
  eid varchar(40) NOT NULL default '',
  pid varchar(40) NOT NULL default '',
  cid int(10) unsigned NOT NULL default '1',
  title varchar(128) default NULL,
  description text,
  datestart int(10) unsigned default NULL,
  dateend int(10) unsigned default NULL,
  location varchar(40) default NULL,
  allday tinyint(1) NOT NULL default '0', 
  owner_id mediumint(8) unsigned NOT NULL default '1',
  group_id mediumint(8) unsigned NOT NULL default '1',
  perm_owner tinyint(1) unsigned NOT NULL default '3',
  perm_group tinyint(1) unsigned NOT NULL default '3',
  perm_members tinyint(1) unsigned NOT NULL default '2',
  perm_anon tinyint(1) unsigned NOT NULL default '2',
  PRIMARY KEY  (eid)
) ENGINE=MyISAM
"; 

$_SQL[] = "
CREATE TABLE {$_TABLES['c2_submission']} (
  eid varchar(20) NOT NULL default '',
  pid varchar(20) NOT NULL default '',
  cid int(10) unsigned NOT NULL default '1',
  title varchar(128) default NULL,
  description text,
  datestart int(10) unsigned default NULL,
  dateend int(10) unsigned default NULL,
  location varchar(40) default NULL,
  allday tinyint(1) NOT NULL default '0',
  owner_id mediumint(8) unsigned NOT NULL default '1',
  group_id mediumint(8) unsigned NOT NULL default '1',
  perm_owner tinyint(1) unsigned NOT NULL default '3',
  perm_group tinyint(1) unsigned NOT NULL default '3',
  perm_members tinyint(1) unsigned NOT NULL default '2',
  perm_anon tinyint(1) unsigned NOT NULL default '2', 
  PRIMARY KEY  (eid)
) ENGINE=MyISAM
";

$_SQL[] = "
INSERT INTO {$_TABLES['c2_calendars']} (owner_id, title, perm_anon) VALUES (2, 'Site Wide', '0');";

$_SQL[] = "
CREATE TABLE {$_TABLES['c2_recurring_events']} (
    reid varchar(20) NOT NULL default '',
    cid int(10) unsigned NOT NULL default '1',
    datestart int(10) unsigned NOT NULL default '0',
    dateend int(10) unsigned NOT NULL default '0',
    recurring_ends int(10) unsigned NOT NULL default '0', 
    last_event int(10) unsigned default NULL, 
    title varchar(128) default NULL,
    description text,
    location varchar(40) default NULL,
    allday tinyint(1) NOT NULL default '0',
    owner_id mediumint(8) unsigned NOT NULL default '1',
    group_id mediumint(8) unsigned NOT NULL default '1',
    perm_owner tinyint(1) unsigned NOT NULL default '3',
    perm_group tinyint(1) unsigned NOT NULL default '3',
    perm_members tinyint(1) unsigned NOT NULL default '2',
    perm_anon tinyint(1) unsigned NOT NULL default '2'
    ) ENGINE=MyISAM  
";

$_SQL[] = "
CREATE TABLE {$_TABLES['c2_recurring_specification']} (
    preid varchar(20) NOT NULL default '',
    MID int(10) unsigned default '0',
    exception tinyint(1) unsigned NOT NULL default '0',
    day_period tinyint(2) unsigned default NULL,
    week_period tinyint(2) unsigned default NULL, 
    month_period tinyint(2) unsigned default NULL,
    year_period tinyint(2) unsigned default NULL, 
    which_day tinyint(2) unsigned default NULL,
    which_weekday tinyint(1) unsigned default NULL,
    which_week tinyint(1) unsigned default NULL, 
    which_month tinyint(2) unsigned default NULL,
    owner_id mediumint(8) unsigned NOT NULL default '1',
    group_id mediumint(8) unsigned NOT NULL default '1',
    perm_owner tinyint(1) unsigned NOT NULL default '3',
    perm_group tinyint(1) unsigned NOT NULL default '3',
    perm_members tinyint(1) unsigned NOT NULL default '2',
    perm_anon tinyint(1) unsigned NOT NULL default '2' 
) ENGINE=MyISAM
"; 

$_SQL[] = "INSERT INTO {$_TABLES['blocks']} (
    is_enabled, name, type, title, tid, blockorder, content, onleft, 
    phpblockfn, owner_id, group_id, perm_owner, perm_group) VALUES 
    (1,'events_block','phpblock','Events','all',100,'',1,'phpblock_calendarv2',
    {$_USER['uid']},#group#,3,3)"; 
?>
