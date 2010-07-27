 <?php

/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | Calendarv2 plugin 0.1                                                     |
// +---------------------------------------------------------------------------+
// | install_defaults.php                                                      |
// |                                                                           |
// | Initial Installation Defaults used when loading the online configuration  |
// | records. These settings are only used during the initial installation     |
// | and not referenced any more once the plugin is installed.                 |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2010 by the following authors:                              |
// |                                                                           |
// | Authors: Vlad Voicu        - vladvoic AT gmail DOT com                    |
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
//
// $Id: install_defaults.php 

global $_CAV2_CONF, $_CONF;
$_CAV2_CONF = array();
$_CAV2_CONF['folder'] = 'calendarv2';
$_CAV2_CONF['first_day'] = 1;
$_CAV2_CONF['calendarlimit'] = '10';
$_CAV2_CONF['sitewide'] = 'false';

function plugin_initconfig_calendarv2()
{
    global $_CONF, $_CAV2_CONF;

    $c = config::get_instance();
    if (!$c->group_exists('calendarv2')) {

        $c->add('sg_main', NULL, 'subgroup', 0, 0, NULL, 0, true, 'calendarv2');
        $c->add('fs_main', NULL, 'fieldset', 0, 0, NULL, 0, true, 'calendarv2');
        $c->add('folder', $_CAV2_CONF['folder'],
                'text', 0, 0, 0, 10, true, 'calendarv2');
        $c->add('first_day', $_CAV2_CONF['first_day'], 
                'select', 0, 0, 2, 20, true, 'calendarv2');
        $c->add('calendarlimit', $_CAV2_CONF['calendarlimit'],
                'text', 0, 0, 0, 30, true, 'calendarv2');
        $c->add('sitewide', $_CAV2_CONT['sitewide'],
                'select', 0, 0, 1, 40, true, 'calendarv2');
    }

    return true;
}

?> 
