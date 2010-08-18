 <?php

/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | Geeklog 1.6                                                               |
// +---------------------------------------------------------------------------+
// | aeventsv2.class.php                                                       |
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

// This file contains the description of the aevents class that will be used in 
// the new calendar plugin. Developed During GSoC 2010 

class Aevents implements arrayaccess, iterator {
    private $_events = array();
    private $_position;

    // Implement iterator abstract methods
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
    *
    * @param    object  $date_start The start of the timespan
    * @param    object  $date_end   The end of the timespan
    * @param    integer $cid    The calendar id
    *
    */
    public function getElements(DateTime $date_start, DateTime $date_end, $cid) {
        global $_TABLES;           
        $sql = "select * from {$_TABLES['c2_events']} where {$date_start->format('U')}";
        $sql .= "<= datestart AND datestart < {$date_end->format('U')} AND cid = '$cid'";
        $result = DB_query($sql);
        $i = 0;
        while($event = DB_fetchArray($result)) {
            $this->_events[] = new Event();
            if($event['eid'] != NULL) {
                $this->_events[$i]->load_event_from_DB_array($event, 'c2_events');
                $i++;
            }
        }
    }
    
    /**
    * adds an event to the array of events
    * 
    * @param    object  $event  An event
    */ 
    public function addEvent($event) {
            $this->_events[$this->getNumEvents()] = $event;
    }
    /**
    *
    * Get the Number of events
    *
    */
    public function getNumEvents() {
        return count($this->_events);
    }
    /**
    *
    * Gets an event with a specific id
    *
    * @param integer    $eid    The id
    *
    */
    
    public function getEvent($eid) {
        foreach ($this->_events as $event) {
            if ($event->getEid() == $eid) {
                return $event;
            }
        }
        return NULL;
    }

    /**
    *
    * Gets the events from a calendar. This function should be improved to look
    * if the user has rights to look for a certain event?
    *
    */
    public function getEvents($cid) 
    {
        global $_TABLES, $_USER;
        $cid = COM_applyFilter($cid, true);
        if (COM_isAnonUser()) {
            $_USER['uid'] = 1;
        }
        // Check to see if the user has access to the calendar in the first place
        if (calendarv2_checkCalendar($cid, $_USER['uid'], 2)) {
            $sql = "select * from {$_TABLES['c2_events']} where cid = $cid";
            $result = DB_query($sql);
            $access = SEC_hasAccess($result['owner_id'], $result['group_id'], $result['perm_owner'],
                            $result['perm_group'], $result['perm_members'], $result['perm_anon']);
            // More than, or read rights.
            if ($access >= 2) { 
                $i = 0;
                while($event = DB_fetchArray($result)) {
                    $this->_events[] = new Event();
                    if($event['eid'] != NULL) {
                        $this->_events[$i]->load_event_from_DB_array($event, 'c2_events');
                        $i++;
                    }
                }
            }
        }
    }
}
?>
