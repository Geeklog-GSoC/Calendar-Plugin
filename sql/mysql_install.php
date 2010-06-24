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
CREATE TABLE {$_TABLES['calendarv2']} (
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
CREATE TABLE {$_TABLES['c2events']} (
  eid varchar(20) NOT NULL default '',
  pid int(10) unsigned NOT NULL default '0',
  cid int(10) unsigned NOT NULL default '1',
  title varchar(128) default NULL,
  description text,
  datestart int(10) unsigned default NULL,
  dateend int(10) unsigned default NULL,
  owner_id mediumint(8) unsigned NOT NULL default '1',
  group_id mediumint(8) unsigned NOT NULL default '1',
  perm_owner tinyint(1) unsigned NOT NULL default '3',
  perm_group tinyint(1) unsigned NOT NULL default '3',
  perm_members tinyint(1) unsigned NOT NULL default '2',
  perm_anon tinyint(1) unsigned NOT NULL default '2',
  location varchar(40) default NULL,
  allday tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (eid)
) ENGINE=MyISAM
"; 

$_SQL[] = "
CREATE TABLE {$_TABLES['cv2submission']} (
  eid varchar(20) NOT NULL default '',
  pid int(10) unsigned NOT NULL default '0',
  cid int(10) unsigned NOT NULL default '1',
  title varchar(128) default NULL,
  description text,
  datestart int(10) unsigned default NULL,
  dateend int(10) unsigned default NULL,
  owner_id mediumint(8) unsigned NOT NULL default '1',
  group_id mediumint(8) unsigned NOT NULL default '1',
  perm_owner tinyint(1) unsigned NOT NULL default '3',
  perm_group tinyint(1) unsigned NOT NULL default '3',
  perm_members tinyint(1) unsigned NOT NULL default '2',
  perm_anon tinyint(1) unsigned NOT NULL default '2',
  location varchar(40) default NULL,
  allday tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (eid)
) ENGINE=MyISAM
";

$_SQL[] = "
INSERT INTO {$_TABLES['calendarv2']} (owner_id, title) VALUES (2, 'Site Wide');";

$_SQL[] = "
CREATE TABLE {$_TABLES['recurring_events']} (
    reid varchar(20) NOT NULL default '',
    datestart int(10) unsigned NOT NULL default '0',
    dateend int(10) unsigned NOT NULL default '0', 
    last_event int(10) unsigned default NULL, 
    title varchar(128) default NULL,
    description text,
    location varchar(40) default NULL,
    allday tinyint(1) NOT NULL default '0') ENGINE=MyISAM
";

$_SQL[] = "
CREATE TABLE {$_TABLES['recurring_specification']} (
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
    which_month tinyint(2) unsigned default NULL
) ENGINE=MyISAM
"; 
      
     

?>
