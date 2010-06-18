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
    private $_owner;
    private $_calendar_id;
    private $_perm;
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

    public function __construct(){
        $this->_creation_date = time();
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
        global $_USER;
        $this->_event_title = addslashes($A['event_title']);
        if (COM_isAnonUser()) {
            $this->_owner = 1;
        }
        else {
            $this->_owner = $_USER['uid'];
        }
        $start_date = DateTime::createFromFormat('m/d/Y', $A['start_date']);
        $day = $start_date->format('d');
        $month = $start_date->format('m');
        $year = $start_date->format('Y');
 
        $start_time = DateTime::createFromFormat('h:i A', $A['start_time']);
        $this->_event_start = mktime($start_time->format('H '), $start_time->format('i'), NULL, $month , $day, $year);
        $end_date = DateTime::createFromFormat('m/d/Y', $A['end_date']);
        $day = $end_date->format('d');
        $month = $end_date->format('m');
        $year = $end_date->format('Y'); 
        
        $end_time = DateTime::createFromFormat('h:i A', $A['end_time']);
        $this->_event_end = mktime($end_time->format('h'), $end_time->format('i'), NULL, $month , $day, $year);
        if ($this->_event_start > $this->_event_end) {
            $this->_valid = false;
        }

        //TODO depending on recurring_type get recurring events info
        $recurring_type = COM_applyFilter($A['recurring_type'], true);
        $this->_description = addslashes($A['event_description']);
        $this->_location = addslashes($A['event_location']);
        $this->_calendar_id = intval($A['calendar_cid']);
        if ($A['all_day'] == 'on') {
            $this->_allday = 1;
        }
        else { 
            $this->_allday = 1;
        }
    }
    /**
    *
    * load_event_from_DB
    *
    * Takes a DB query and adds info to the class elements
    *
    */  
    
    public function load_event_from_DB($A) {
        $this->_eid = $A['eid'];
        $this->_event_title = $A['title'];
        $this->_event_start = $A['datestart'];
        $this->_event_end = $A['dateend'];
        $this->_recurring = $A['recurring'];
        $this->_location = $A['location'];
        $this->_description = $A['description'];
        $this->_allday = $A['allday']; 
        $this->_calendar_id =$A['cid'];
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
        $fields = 'title,' . 'description,'. 'datestart,'. 'dateend,'. 'location,'. 'allday,' . 'owner_id,' . 'cid';
        $elements = "'$this->_event_title' ," . "'$this->_description' ,"  
                    . "'$this->_event_start'," . "'$this->_event_end'," 
                    . "'$this->_location'," . "'$this->_allday'," . "'$this->_owner',"
                    . "'$this->_calendar_id'";
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
        $fields = "title = '$this->_event_title' ," . "description = '$this->_description',";
        $fields .= "datestart = '$this->_event_start',";
        $fields .= "dateend = '$this->_event_end',";
        $fields .= "location = '$this->_location',";
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
        $this->_eid = intval($P['modify_eid']);
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
            $this->_calendar_id = $event['cid']; 
            $this->_event_title = $event['title'];
            $this->_event_start = $event['datestart'];     
            $this->_event_end = $event['dateend'];
            $this->_location = $event['location'];
            $this->_description = $event['description'];
            $this->_allday = $event['allday'];
            $this->_eid = $eid;
            $this->_calendar_id = $event['cid'];
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
        $A['cid'] = $this->_calendar_id;
        return $A;
    }
        
}                               


class Aevents {
    private $_events;
    private $_length;         
    public function __construct() {
    }
    
    /**
    *
    * populates an array of events. It querys the base betwen 2 times
    * 
    *
    */
 
    public function getElements(DateTime $date_start, DateTime $date_end, $cid) {
        global $_TABLES;
        $sql = "select * from {$_TABLES['c2events']} where {$date_start->getTimestamp()}";
        $sql .= "<= datestart AND dateend < {$date_end->getTimestamp()} AND cid = '$cid'";
        $result = DB_query($sql);
        $i = 0;
        while($event = DB_fetchArray($result)) {
            $this->_events[$i] = new Event();
            if($event['eid'] != NULL) {
                $this->_events[$i]->load_event_from_DB($event);
                $i++;
            }
        }
        $this->_length = $i;
    }
    
    public function getLength() {
        return $this->_length;
    }
    
    /**
    *
    * gets an string array with all the events from the events array
    * 
    *
    */
    
    public function getElementsArray() {
        for ($i = 0; $i < $this->_length; $i++) {
            $events[$i] = $this->_events[$i]->get_details();
        }
        
        return $events;
    }
}

?>
     
