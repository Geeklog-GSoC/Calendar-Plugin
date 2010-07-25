 <?php

/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | Geeklog 1.6                                                               |
// +---------------------------------------------------------------------------+
// | aeventsv2.class.php                                                         |
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
    
    public function getEvent($eid) {
        foreach ($this->_events as $event) {
            $test = $event->getEid();
            if ($event->getEid() == $eid) {
                return $event;
            }
        }
        return NULL;
    }

    public function getEvents($cid) 
    {
        global $_TABLES;
        $cid = COM_applyFilter($cid, true);
        $sql = "select * from {$_TABLES['c2events']} where cid = $cid";
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
        
    
    

}
