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
    protected $_cid;
    protected $_creation_date;
    protected $_title;
    protected $_start;
    protected $_end;
    protected $_pid;
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

    public function __construct($A = NULL) {
        if ($A != NULL) {
            $this->load_event_from_array($A);
        }
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
        
    public function getPid() {
        return $this->_pid;
    }
    
    public function getCid() {
        return $this->_cid;
    }

    public function getDescription() {
        return $this->_description;
    }
    
    public function getLocation() {
        return $this->_location;
    }
    public function getAllday() {
        return $this->_allday;
    }

    // And some setters
    public function setEid($eid)
    {
        $this->_eid = $eid;
    }
    
    public function setPid($pid)
    {
        $this->_pid = $pid;
    }
    
    public function setCid($cid)
    {
        $this->_cid = $cid;
    }
    
    public function setTitle($title) 
    {
        $this->_title = $title;
    }
    
    public function setStartDate(DateTime $date) 
    {
        $this->_start = $date;
    }
    
    public function setEndDate(DateTime $date) 
    {
        $this->_end = $date;
    }
    
    public function setTimezone(DateTimeZone $timezone) {
        $this->_start->setTimezone($timezone);
        $this->_end->setTimezone($timezone);
    }

    public function setLocation($location) {
        $this->_location = $location;
    }

    public function setDescription($description) {
        $this->_description = $description;
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
        $this->_group = COM_applyFilter($A['group_id'] , true);
        if (is_array($A['group_id']) or is_array($A['perm_owner']) or is_array($A['perm_group']) or is_array($A['perm_anon'])) {
                $permission = SEC_getPermissionValues($A['perm_owner'], $A['perm_group'] , $A['perm_members'], $A['perm_anon']);
        }
        list($this->_perm_owner, $this->_perm_group, $this->_perm_members, $this->_perm_anon) = $permission;

        $timezone = TimeZoneConfig::getUserTimeZone();
        $timezone = new DateTimeZone($timezone); 
        $start = $A['start_date'] . $A['start_time'];
        try { 
            $this->_start = new DateTime($start, $timezone);
        } catch (Exception $e) {
            throw new Exception('DateTime failed' , $e);
        }

        $end = $A['end_date'] . $A['end_time'];
        try {
            $this->_end = new DateTime($end, $timezone);
        } catch (Exception $e) {
            throw new Exception('DateTime failed' , $e);
        }

        $this->_description = $A['event_description'];
        $this->_location = $A['event_location'];
        if (!isset($this->_cid)) {
            $this->_cid = intval($A['calendar_cid']);
        }
        $this->_allday = 0;                                
        if ($A['all_day'] == 'on') {
            $this->_allday = 1;
        }

        // Deal breakers

        // All events should have titles
        if (empty($this->_title)) {
            throw new Exception("Event must have a title");
        }
        // An events must start before it ends
        if ($this->_end->format('U') - $this->_start->format('U') < 0) {
            throw new Exception("Event must start before it ends");
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
        $timezone = TimeZoneConfig::getUserTimeZone();
        $timezone = new DateTimeZone($timezone);
        $this->_start = new DateTime('@' . $A['datestart']);
        $this->_start->setTimezone($timezone);
        $this->_end =  new DateTime('@' . $A['dateend']);
        $this->_end->setTimezone($timezone); 
        $this->_location = stripslashes($A['location']);
        $this->_description = stripslashes($A['description']);
        $this->_allday = $A['allday'];  
        $this->_eid = $A['eid'];
        $this->_recurring = $A['recurring'];
        $this->_cid =$A['cid'];
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
                  'dateend,'. 'location,'. 'allday,' . 'owner_id,' . 'cid,' . 'pid,' . 'perm_owner, ' .
                  'perm_members,' . 'perm_group,' . 'perm_anon';
        $sanitized = $this->getSanitized();   
        $elements = "'{$sanitized['eid']}' ," . "'{$sanitized['title']}' ," . "'{$sanitized['description']}' ,"  
                    . "'{$sanitized['start']}'," . "'{$sanitized['end']}'," 
                    . "'{$sanitized['location']}'," . "'$this->_allday'," . "'$this->_owner',"
                    . "'$this->_cid'," . "'$this->_pid'," . "'$this->_perm_owner'," 
                    . "'$this->_perm_members'," . "'$this->_perm_group'," . "'$this->_perm_anon'";

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
        $this->_eid = COM_applyFilter($P['modify_eid'], true);
        $this->_cid = COM_applyFilter($P['modify_cid'], true);
        $this->load_event_from_array($P);
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
        var_dump($this->_eid);
        $this->_cid = $P['modify_cid'];
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

    public function get_event($eid, $table) 
    {
        global $_TABLES;
        //Eid comes from $_POST so it must be verified
        $eid = addslashes($eid);
        $sql = "select * from {$_TABLES[$table]} where eid = {$eid}";
        $result = DB_query($sql);
        $event = DB_fetchArray($result);
        $this->_cid = $event['cid']; 
        $this->_title = $event['title'];
        $timezone = TimeZoneConfig::getUserTimeZone();
        $timezone = new DateTimeZone($timezone);
        $this->_start = new DateTime('@' . $event['datestart']);
        $this->_start->setTimezone($timezone);     
        $this->_end =  new DateTime('@' . $event['dateend']);
        $this->_end->setTimezone($timezone);
        $this->_location = $event['location'];
        $this->_description = $event['description'];
        $this->_allday = $event['allday'];
        $this->_eid = $eid;
        $this->_pid = $event['pid'];
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
        $A['cid'] = $this->_cid;
        $A['pid'] = $this->_pid;
        return $A;
    }

    // Sanitizes the data
    protected function getSanitized()
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


?>
