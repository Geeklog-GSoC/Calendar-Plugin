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
    private $_eid;
    private $_creation_date;
    private $_event_title;
    private $_event_start;
    private $_event_end;
    private $_recurring;
    private $_location;
    private $_description;
    private $_allday;
    private $_valid;

    /**
    *
    * Constructor
    *
    * Initializes event object
    *
    */ 

    public function __construct() {
        $this->_valid = true;
        $this->_creation_date = time();
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
    private function check_date($date_to_check) {
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

    private function check_time($time_to_check) {
        preg_match('/[0-9]{2}:[0-9]{2} (PM|AM)/' , $time_to_check, $matches);
        if (empty($matches)) {
            return false;
        }
        list($hour, $garbage) = explode(':' , $matches[0]);
        list($minutes, $amorpm) = explode(' ', $garbage);
 
        //Nice trick to transform 12 hour date format in 24 hour date format
        list($hour,$minutes) = explode(':' , (date("H:i", strtotime("$hour:". "$minutes". "$amorpm"))));
            
        $time = array('hour' => $hour, 'minutes' => $minutes);
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
    public function load_event_from_array($A) {
        $this->_event_title = addslashes($A['event_title']);
        $date = $this->check_date($A['start_date']);
        if ($date) {
            $day = $date['day'];
            $month = $date['month'];
            $year = $date['year'];
        }
 
        $start_time = $this->check_time($A['start_time']);
        $this->_event_start = mktime($start_time['hour'], $start_time['minutes'], NULL, $day , $month, $year);
        $date = $this->check_date($A['end_date']);
        if ($date) {
            $day = $date['day'];
            $month = $date['month'];
            $year = $date['year'];
        }
        $end_time = $this->check_time($A['end_time']); 
        $this->_event_end = mktime($end_time['hour'], $end_time['minutes'], NULL, $day , $month, $year);
        if ($this->_event_start > $this->_event_end) {
            $this->_valid = false;
        }

        //TODO depending on recurring_type get recurring events info
        $recurring_type = COM_applyFilter($A['recurring_type'], true);
        $this->_event_description = addslashes($A['event_description']);
        $this->_event_location = addslashes($A['event_location']);
        if ($A['all_day'] == 'on') {
            $this->_allday = 1;
        }
        else { 
            $this->_allday = 1;
        }
    } 
    /**
    *
    * save to database
    *
    * Saves information to database from an event object
    *
    */   
    
    public function save_to_database()
    {
        global $_TABLES;
        if ($this->_valid == false)
            return false;
        $fields = 'title,' . 'description,'. 'datestart,'. 'dateend,'. 'location,'. 'allday';
        $elements = "'$this->_event_title' ," . "'$this->_event_description' ," . "'$this->_event_start'," . "'$this->_event_end'," . "'$this->_event_location'," . "'$this->_allday'";
        DB_save($_TABLES['c2events'], $fields, $elements);
    }
    /**
    *
    * removes an element from database
    *
    * Saves information to database from an event object
    *
    */   
    
    public function remove_from_database($eid)
    {
        global $_TABLES;
        $sql = "delete from {$_TABLES['c2events']} where eid = {$eid}";
        DB_query($sql);
    }
    
    /**
    *
    * update database
    *
    * Updates information to database from an event object
    *
    */   
    
    public function update_to_database($eid)
    {
        global $_TABLES;
        if ($this->_valid == false)
            return false;
        $fields = "title = '$this->_event_title' ," . "description = '$this->_event_description',";
        $fields .= "datestart = '$this->_event_start',";
        $fields .= "dateend = '$this->_event_end',";
        $fields .= "location = '$this->_event_location',";
        $fields .= "allday = '$this->_allday'";
        $sql = "update {$_TABLES['c2events']} set {$fields} where eid = {$eid}";
        DB_query($sql);
    }

    /**
    *
    *
    * Modifies the information of an event, based on his eid.
    * And saves the new event in the database
    *
    */    

    public function modify($P) 
    {
        $this->load_event_from_array($P);
        $this->update_to_database($P['modify_eid']);
    }
    
    /**
    *
    * Deletes an event based on his EID
    * 
    * 
    *
    */    

    public function delete($eid) 
    {
        $this->remove_from_database($eid);
    }

    /**
    *
    * Fils and event with information from database based on an eid.
    *
    * Modifies the information of an event, based on his eid.
    *
    */    

    public function get_event($eid) 
    {
        global $_TABLES;
        //Eid comes from $_POST so it must be verified
        if (is_numeric($eid)) {
            $sql = "select * from {$_TABLES['c2events']} where eid = {$eid}";
            $result = DB_query($sql);
            $event = DB_fetchArray($result);
            $this->_event_title = $event['title'];
            $this->_event_start = $event['datestart'];     
            $this->_event_end = $event['dateend'];
            $this->_location = $event['location'];
            $this->_description = $event['description'];
            $this->_allday = $event['allday'];
            $this->_eid = $eid;
            $this->_valid = true; 
        }
    }

    /**
    *
    * Returns a string with all the information needed for an event
    * 
    *
    */     

    public function get_details()
    {
        $A['title'] = $this->_event_title;
        $A['datestart'] = $this->_event_start;
        $A['dateend'] = $this->_event_end;
        $A['location'] = $this->_location;
        $A['description'] = $this->_description;
        $A['allday'] = $this->_allday;
        $A['eid'] = $this->_eid;
        return $A;
    }
        
}                               


class Aevents {
    private $events;         
    public function __construct() {
    }
    public function getElements(DateTime $date_start, DateTime $date_end) {
    }
}

?>
     
