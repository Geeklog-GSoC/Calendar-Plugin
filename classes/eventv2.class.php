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

// This file contains the description of the events class that will be used in 
// the new calendar plugin. Developed During GSoC 2010

class Event {
    var $_creation_date;
    var $_event_title;
    var $_event_start_date;
    var $_event_end_date;
    var $_event_start_day;
    var $_event_start_year;
    var $_event_start_month;
    var $_event_end_day;
    var $_event_end_year;
    var $_event_end_month;
    var $_event_start_hour;
    var $_event_end_hour;
    var $_recurring;
    var $_location;
    var $_description;

    /**
    *
    * Constructor
    *
    * Initializes event object
    *
    */ 

    function Event($A) {
        $this->load_event_from_array($A);
    }

    /**
    *
    * check_date
    *
    * checks if a date is in the required format and returns values for month, day and year.
    * This function will have to be modified when user settings kick. Time must be 
    * returned in GMT format for database storage.
    *
    */
    function check_date($date_to_check) {
        preg_match('/[0-9]{2}\/[0-9]{2}\/[0-9]{4}/' , $date_to_check, $matches);
        if (empty($matches)) {
            return false;
        }
        list($day, $month, $year) = explode('/' , $matches[0]);
        $date = array('day' => intval($day), 'month' => intval($month), 'year' => intval($year));
        return $date;
    } 
    
    /**
    *
    * check_time
    *
    * Same as check date only checks the starting time.
    *
    */

    function check_time($time_to_check) {
        preg_match('/[0-9]{2}:[0-9]{2} (PM|AM)/' , $time_to_check, $matches);
        if (empty($matches)) {
            return false;
        }
        list($hour, $garbage) = explode(':' , $matches[0]);
        list($minutes, $amorpm) = explode(' ', $garbage);
        $time = array('hour' => $hour, 'minutes' => $minutes, 'amorpm' => $amorpm);
        return $time;
    }  
    /**
    *
    * load_event_from_array
    *
    * Takes a $_POST variable and adds info to the class elements
    * the elemnts are not secure, they must be processed before adding
    * to the database
    *
    */  
    function load_event_from_array($A) {
        //var_dump($A);
        $this->_event_title = addslashes($A['event_title']);
        $date = $this->check_date($A['start_date']);
        if ($date) {
            $this->_event_start_day = $date['day'];
            $this->_event_start_month = $date['month'];
            $this->_event_start_year = $date['month'];
        }
        $date = $this->check_date($A['end_date']);
        if ($date) {
            $this->_event_end_day = $date['day'];
            $this->_event_end_month = $date['month'];
            $this->_event_end_year = $date['month'];
        }
        //TODO make time from that info and add it in the database GMT
        $start_time = $this->check_time($A['start_time']);
        $end_time = $this->check_time($A['end_time']);
        //TODO depending on recurring_type get recurring events info
        $recurring_type = COM_applyFilter($A['recurring_type'], true);
        $this->_event_description = addslashes($A['event_description']);
        $this->_event_location = addslashes($A['event_location']);
    }     

}

?>
     
