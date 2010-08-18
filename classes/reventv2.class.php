<?php

/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | Geeklog 1.6                                                               |
// +---------------------------------------------------------------------------+
// | eventv2.class.php                                                         |
// |                                                                           |
// | Geeklog calendar library.                                                 |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2000-2010 by the following authors:                         |
// |                                                                           |
// | Authors: Vlad Voicu       - vladvoic AT gmail DOT com                     |
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

// This file contains the description of the Revents class that will be used in 
// the new calendar plugin. Developed During GSoC 2010 


class Revent extends Event {
    private $_recurring_ends;
    private $_year;
    private $_week = array();
    private $_month;
    private $_day;
    private $_reid;
    private $_period;
    public function __construct($A) {
        $this->load_event_from_array($A);
    }

    /**
    * Loads an recurring event from a POST variable
    *
    */
    public function load_event_from_array($A) {
        parent::load_event_from_array($A);
        if ($A['recurring_ends_never'] == 'on') {
            $this->_recurring_ends = 1;
        }
        else {
            $this->_recurring_ends = new DateTime($A['recurring_ends']);
            // Usualy datetime objects are kept, but now it's really not necesary since only
            // the timestamp is needed.
            $this->_recurring_ends = $this->_recurring_ends->format('U');
        }
        $this->_recurring_type = intval($A['recurring_type']);
        $this->_reid = COM_makeSid();
        switch ($this->_recurring_type) {
            case 2:
                $this->_day = $A['recurring_every_day'];
                break;
            case 3:
                for ($i = 0; $i < 7; $i++) {
                    if ($A["day_recurring_$i"] == 'on') {
                        $this->_week[$i] = true;
                    }
                }
                $this->_period = COM_applyFilter($A['every_weeks']);
                break; 
            case 4:
                $this->_month = $A['monthly'];
                break;
            case 5:
                $this->_year = $A['yearly'];
                break;
            default:
                throw new Exception('Something is wrong with the recurring type value');
        } 
    }
    
    /**
    * Saves the event in c2_recurring_events table
    *
    */ 
    private function save_recurring_events() {
        global $_TABLES, $_USER;
        $sanitized = $this->getSanitized();
        $fields = 'reid,' . 'title,' . 'description,'. 'datestart,'. 'dateend,'. 'location,'. 'allday,' . 'recurring_ends,' . 'cid';
        $values = "'$this->_reid'," . "'{$sanitized['title']}'," . "'{$sanitized['description']}'," . 
                    "'{$sanitized['start']}' ," . "'{$sanitized['end']}'," ."'{$sanitized['location']}'," . "'$this->_allday'," . "'$this->_recurring_ends'," . "'$this->_cid'";
        if (calendarv2_checkCalendar($this->_cid, $_USER['uid'], 3)) {   
            DB_save($_TABLES['c2_recurring_events'], $fields, $values);
        }
        else {
            // Throw some exceptions?
        }
    } 
    
    /**
    * Save the event in the c2_recurring_specification table
    *
    */
    public function save_to_database() {
        global $_TABLES;
        $this->save_recurring_events(); 
        $fields = "preid,";
        $values = "'$this->_reid',";
        $save = true;
        if (isset($this->_year)) { 
            $fields .= "year_period";
            $values .= "'$this->_year'";
        }
        if (isset($this->_month)) {
            $fields .= "month_period";
            $values .= "'$this->_month'";
        }
        
        if (isset($this->_day)) {
            $fields .= "day_period";
            $values .= "'$this->_day'";
        }
        if (isset($this->_week)) {
            for ($i = 0; $i < 7; $i++) {
                if ($this->_week[$i]) {
                    $fields = 'week_period,' . 'preid,' . 'which_day';
                    $values = "'$this->_period', '$this->_reid'," . "'$i'";
                    DB_save($_TABLES['c2_recurring_specification'], $fields, $values);
                    $save = false;
                }
            }
        }
        if ($save) {
            DB_save($_TABLES['c2_recurring_specification'], $fields, $values);
        }
    }
}

?> 
