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
    private $_moderation;

    /**
    *
    * Constructor
    *
    * Initializes event object
    *
    */ 

    public function __construct(){
    }

    // Implementation of some getters
    public function getEid() {
        return $this->_eid;
    }
    
    public function getDatestart() {
        return $this->_event_start;
    }
    
    public function getDateend() {
        return $this->_event_end;
    }
        
    public function getTitle() {
        return $this->_event_title;
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
        if (!isset($this->_owner)) {
            if (COM_isAnonUser()) {
                $this->_owner = 1;
            }
            else {
                $this->_owner = $_USER['uid'];
            }
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
        $this->_owner = $A['owner_id'];
    }
    
    public function setEid($eid)
    {
        $this->_eid = $eid;
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
        $fields = 'eid,' . 'title,' . 'description,'. 'datestart,'. 'dateend,'. 'location,'. 'allday,' . 'owner_id,' . 'cid';
        $elements = "'$this->_eid' ," . "'$this->_event_title' ," . "'$this->_description' ,"  
                    . "'$this->_event_start'," . "'$this->_event_end'," 
                    . "'$this->_location'," . "'$this->_allday'," . "'$this->_owner',"
                    . "'$this->_calendar_id'";
        // Check to see if the events is directly saved into the database or mark for admin aproval.
        if ($this->_moderation == false) {
            DB_save($_TABLES['c2events'], $fields, $elements);
        }
        else {
            DB_save($_TABLES['cv2submission'], $fields, $elements);
        }
    }
 
    /**
    *
    * sets the moderation flag for an event
    *
    *
    */
    
    public function setModeration($bool)
    {
        $this->_moderation = $bool;
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
        $this->_eid = $P['modify_eid'];
        $this->update_to_database($P['modify_eid']);
    }

    /**
    *
    *
    * Modifies the information of an event, based on his eid.
    * And saves the new event in the database
    *
    */     
    public function modify_moderation($P)
    {
        $this->load_event_from_array($P);
        $this->_eid = $P['modify_eid'];
        $this->update_to_database_moderation($P['modify_eid']);
    }
    
    /**
    *
    * update database
    *
    * Updates information to database from an event object
    *
    */    

    public function update_to_database_moderation($eid) {
        global $_TABLES;
        $fields = "title = '$this->_event_title' ," . "description = '$this->_description',";
        $fields .= "datestart = '$this->_event_start',";
        $fields .= "dateend = '$this->_event_end',";
        $fields .= "location = '$this->_location',";
        $fields .= "allday = '$this->_allday'";
        $sql = "update {$_TABLES['cv2submission']} set {$fields} where eid = {$eid}";
        DB_query($sql); 
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
    *
    */    

    public function get_event($eid) 
    {
        global $_TABLES;
        //Eid comes from $_POST so it must be verified
        $eid = addslashes($eid);
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

    /**
    *
    * Fils and moderation event with information from database based on an eid.
    *
    *
    */     
    
    public function get_moderation_event($eid)
    {    
        global $_TABLES;
        //Eid comes from $_POST so it must be verified
        $eid = addslashes($eid);
        $sql = "select * from {$_TABLES['cv2submission']} where eid = {$eid}";
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


class Aevents implements arrayaccess, iterator {
    private $_events = array();
    private $_position;

    // Implement iterator abastract methods
    public function __construct() {
        $this->_position = 0;
    }
    public function rewind() {
        $this->_position = 0;
    }

    public function current() {
        return $this->_events[$this->_position];
    }
    
    public function key() {
        return $this->_position;
    }

    public function next() {
        ++$this->_position;
    }
    
    public function valid() {
        return isset($this->_events[$this->_position]);
    }

    //Implement array acces abastract methods.
    public function offsetSet($offset, $value) {
        if ($value instanceof Aevents) {
            if ($offset == "") {
                $this->_events[] = $value;
            }
            else {
                $this->_events[$offset] = $value;
            }
        }
    }
    
    public function offsetExists($offset) {
        return isset($this->_events[$offset]);
    }
    
    public function offsetGet($offset) {
        return $this->_events[$offset];
    }
    
    public function offsetUnset($offset) {
        unset($this->_events[$offset]);
    }
    
    /**
    * populates an array of events. It querys the base betwen 2 times
    */
    public function getElements(DateTime $date_start, DateTime $date_end, $cid) {
        global $_TABLES;
        $sql = "select * from {$_TABLES['c2events']} where {$date_start->getTimestamp()}";
        $sql .= "<= datestart AND dateend < {$date_end->getTimestamp()} AND cid = '$cid'";
        $result = DB_query($sql);
        $i = 0;
        while($event = DB_fetchArray($result)) {
            $this->_events[] = new Event();
            if($event['eid'] != NULL) {
                $this->_events[$i]->load_event_from_DB($event);
                $i++;
            }
        }
    }
    
    public function getNumEvents() {
        return count($this->_events);
    }
}

?>
     
