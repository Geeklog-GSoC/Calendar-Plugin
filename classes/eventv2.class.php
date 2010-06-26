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
    
    // Geeklog permission items
    protected $_owner;
    protected $_group;
    protected $_group_id;
    protected $_perm_owner;
    protected $_perm_group;
    protected $_perm_members;
    protected $_perm_anon;

    // Event details
    protected $_eid;
    protected $_calendar_id;
    protected $_perm;
    protected $_creation_date;
    protected $_title;
    protected $_start;
    protected $_end;
    protected $_recurring;
    protected $_location;
    protected $_description;
    protected $_allday;
    protected $_moderation;

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
        return $this->_start->format('U');
    }
    
    public function getDateend() {
        return $this->_end->format('U');
    }
        
    public function getTitle() {
        return $this->_title;
    }

    // And some setters
    public function setEid($eid)
    {
        $this->_eid = $eid;
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
        $this->_title = $A['event_title'];
        
        if (!isset($this->_owner)) {
            if (COM_isAnonUser()) {
                $this->_owner = 1;
            }
            else {
                $this->_owner = $_USER['uid'];
            }
        }

        $start = $A['start_date'] . $A['start_time'];
        try { 
            $this->_start = new DateTime($start);
        } catch (Exception $e) {
            throw new Exception('DateTime failed' , $e);
        }

        $end = $A['end_date'] . $A['end_time'];
        try {
            $this->_end = new DateTime($end);
        } catch (Exception $e) {
            throw new Exception('DateTime failed' , $e);
        }

        $this->_description = $A['event_description'];
        $this->_location = $A['event_location'];
        $this->_calendar_id = intval($A['calendar_cid']);
        $this->_allday = 0;                                
        if ($A['all_day'] == 'on') {
            $this->_allday = 1;
        }

        // Deal breakers
        if (empty($this->_title)) {
            throw new Exception("Event must have a title");
        }
        if (empty($this->_calendar_id)) {
            throw new Exception("Event must have a calendar or else is useless");
        }
    }
    /**
    *
    * load_event_from_DB
    *
    * Takes a DB query and adds info to the class elements
    *
    */  
    
    public function load_event_from_DB_array($A) {
        $this->_title = stripslashes($A['title']);
        $this->_start = new DateTime('@' . $A['datestart']);
        $this->_end =  new DateTime('@' . $A['dateend']); 
        $this->_location = stripslashes($A['location']);
        $this->_description = stripslashes($A['description']);
        $this->_allday = $A['allday'];  
        $this->_eid = $A['eid'];
        $this->_recurring = $A['recurring'];
        $this->_calendar_id =$A['cid'];
        $this->_owner = $A['owner_id'];
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
        $fields = 'eid,' . 'title,' . 'description,'. 'datestart,'. 
                  'dateend,'. 'location,'. 'allday,' . 'owner_id,' . 'cid';
        $sanitized = $this->getSanitized();      
        $elements = "'{$sanitized['eid']}' ," . "'{$sanitized['title']}' ," . "'{$sanitized['description']}' ,"  
                    . "'{$sanitized['start']}'," . "'{$sanitized['end']}'," 
                    . "'{$sanitized['location']}'," . "'$this->_allday'," . "'$this->_owner',"
                    . "'$this->_calendar_id'";

        // Check to see if the events is directly saved into the database 
        // or mark for admin aproval.
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
    
    public function update_to_database($eid, $table)
    {
        global $_TABLES;
        
        $sanitized = $this->getSanitized();
        $fields = "title = '{$sanitized['title']}' ," . "description = '{$sanitized['description']}',";
        $fields .= "datestart = '{$sanitized['start']}',";
        $fields .= "dateend = '{$sanitized['end']}',";
        $fields .= "location = '{$sanitized['location']}',";
        $fields .= "allday = '$this->_allday'";
        $sql = "update {$_TABLES[$table]} set {$fields} where eid = {$sanitized['eid']}";
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
        $this->update_to_database($P['modify_eid'], 'c2events');
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
        $this->update_to_database($P['modify_eid'], 'cv2submission');
    }
    
    /**
    *
    * Deletes an event based on his EID
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
        $this->_title = $event['title'];
        $this->_start = new DateTime('@' . $event['datestart']);     
        $this->_end =  new DateTime('@' . $event['dateend']);
        $this->_location = $event['location'];
        $this->_description = $event['description'];
        $this->_allday = $event['allday'];
        $this->_eid = $eid;
        $this->_calendar_id = $event['cid'];
    }

    /**
    *
    * Fils and moderation event with information from database based on an eid.
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
        $this->_title = $event['title'];
        $this->_start = new DateTime('@' . $event['datestart']);     
        $this->_end = new DateTime('@' . $event['dateend']);
        $this->_location = $event['location'];
        $this->_description = $event['description'];
        $this->_allday = $event['allday'];
        $this->_eid = $eid;
        $this->_calendar_id = $event['cid'];
    }  
            

    /**
    *
    * Returns a string with all the information needed for an event
    *
    */     

    public function get_details()
    {
        $A['title'] = $this->_title;
        $A['datestart'] = $this->_start->format('U');
        $A['dateend'] = $this->_end->format('U');
        $A['location'] = $this->_location;
        $A['description'] = $this->_description;
        $A['allday'] = $this->_allday;
        $A['eid'] = $this->_eid;
        $A['cid'] = $this->_calendar_id;
        return $A;
    }

    // Sanitizes the data
    private function getSanitized()
    {
        $elements = array();
        // The data that needs to be sanitized
        $elements['eid'] = addslashes($this->_eid);
        $elements['title'] = addslashes($this->_title);
        $elements['description'] = addslashes($this->_description);
        $elements['location'] = addslashes($this->_location);
        $elements['start'] = $this->_start->format("U");
        $elements['end'] = $this->_end->format("U");
        return $elements; 
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
    * populates an array of events. It querys the datebase betwen 2 moments of time
    */
    public function getElements(DateTime $date_start, DateTime $date_end, $cid) {
        global $_TABLES;
        $sql = "select * from {$_TABLES['c2events']} where {$date_start->format('U')}";
        $sql .= "<= datestart AND dateend < {$date_end->format('U')} AND cid = '$cid'";
        $result = DB_query($sql);
        $i = 0;
        while($event = DB_fetchArray($result)) {
            $this->_events[] = new Event();
            if($event['eid'] != NULL) {
                $this->_events[$i]->load_event_from_DB_array($event, 'c2events');
                $i++;
            }
        }
    }
    
    /**
    * adds an event to the array of events
    */ 
    public function addEvent($event) {
            $this->_events[$this->getNumEvents()] = $event;
    }
    
    public function getNumEvents() {
        return count($this->_events);
    }
}


class Revent extends Event {
    private $_recurring_ends;
    private $_ends_never;
    private $_year;
    private $_week = array();
    private $_month;
    private $_reid;
    public function __construct($A) {
        $this->_ends_never = $A['recurring_ends_never'];
        if ($this->_ends_never == 'on') {
            $this->_recurring_ends = $A['recurring_ends'];
            $this->_recurring_ends = 1;
        }
        else {
            $this->_recurring_ends = DateTime::createFromFormat('m/d/Y', $A['recurring_ends']);
            $this->_recurring_ends = $this->_recurring_ends->format('U');
        }
        // every day recurrence
        $recurring_type = intval($A['recurring_type']);
        switch ($recurring_type) {
            case 2:
                $this->parse_every_day($A);
                break;
            case 3:
                $this->parse_every_week($A);
                break; 
            case 4:
                $this->parse_every_month($A);
                break;
            case 5:
                $this->parse_every_year($A);
                break;
            default:
                throw new Exception('Something is wrong with the recurring type value');
        }
    }

    private  function save_recurring_events($A) {
        global $_TABLES;
        //Sanitize
        $start = $this->_start->format('U');
        $end = $this->_end->format('U');
        $this->load_event_from_array($A);
        $fields = 'reid,' . 'title,' . 'description,'. 'datestart,'. 'dateend,'. 'location,'. 'allday,' . 'recurring_ends';
        $values = "'$this->_reid'," . "'$this->_title'," . "'$this->_description'," . 
                    "'$start' ," . "'$end'," ."'$this->_location'," . "'$this->_allday'," . "'$this->_recurring_ends'";
        DB_save($_TABLES['recurring_events'], $fields, $values); 
    }

    private function parse_every_day($A) {
        global $_TABLES;
        $day = $A['recurring_every_day'];
        $this->_reid = COM_makeSid();
        $this->save_recurring_events($A);
        $fields = 'preid,' . 'day_period';
        $values = "'$this->_reid'," . "'$day'"; 
        DB_save($_TABLES['recurring_specification'], $fields, $values);
    }
        
    private function parse_every_week($A) {
        for ($i = 1; $i <= 7; $i++) {
            if ($A["day_recurring_$i"] == 'on') {
                $this->_week[$i] = true;
            }
        }
    }
    
    private function parse_every_month($A) {
        global $_TABLES;
        $this->_month = $A['recurring_month'];
        $this->_reid = COM_makeSid();
        $this->save_recurring_events($A);
        $fields = 'preid, month_period';
        $values = "'$this->_reid'," . "'1'";
        DB_save($_TABLES['recurring_specification'], $fields, $values);
    }
    
    private function parse_every_year($A) {
        global $_TABLES;
        $this->_year = $A['recurring_year'];
        $this->_reid = COM_makeSid();
        $this->save_recurring_events($A);
        $fields = 'preid, year_period';
        $values = "'$this->_reid'," . "'1'";
        DB_save($_TABLES['recurring_specification'], $fields, $values);
    }

}

?>
     
