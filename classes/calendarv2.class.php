 <?php

/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | Geeklog 1.6                                                               |
// +---------------------------------------------------------------------------+
// | calendarv2.class.php                                                      |
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

// This file contains the description of the calendar class that will be used in 
// the new calendar plugin. Developed During GSoC 2010

class Calendarv2 {
    private $_creation_date;
    private $_events = array();
    private $_cid;
    private $_title;
 
    /**
    *
    * Constructor
    *
    * Initializes calendar object
    *
    */

    public function __construct() {
        $this->_creation_date = getdate(time());
        $this->_events = new Aevents;
    }
    
    public function setCid($cid) {
        $this->_cid = $cid;
    }

    public function getCid() {
        return $this->_cid;
    }

    public function getTitle() {
        return $this->_title;
    }

    /**
    * Returns a matrix with days of a month
    * 
    * @param    int     $month  the month number
    * @param    int     $year   the year number
    * @return   array   
    */ 
     public function generateMatrix($month, $year) {
        $start_date = mktime(0,0,0, $month, 1, $year);
        $days_in_month = date('t', $start_date);
        $first_day = date('w', $start_date);
        $j = $first_day;
        $num_weeks = 1;
        for ($i = 1; $i <= $days_in_month; $i++) {
            $matrix[$num_weeks][$j] = $i;
            $j++;
            if ($j == 7) {
                $j = 0;
                $num_weeks++;
            }
        }
        return $matrix;
    }
        
    public function setEvents(Aevents $events) {
        $this->_events = $events;
    }
    
    public function addEvent(Event $event) {
        $this->_events->addEvent($event);
    }
    
    public function getEventsSpan(DateTime $datestart, DateTime $dateend) {
        $this->_events->getElements($datestart, $dateend, $this->_cid);
    }
    
    public function loadFromArray($A) {
        $this->_title = $A['title'];
        $this->_cid = $A['cid'];
    }
    
}

class Acalendarv2 implements arrayaccess, iterator {
    private $_position;
    private $_calendars = array(); 
    // Implement iterator abastract methods
    public function __construct() {
        $this->_position = 0;
        $this->_calendars[] = new Calendarv2();
    }
    public function rewind() {
        $this->_position = 0;
    }

    public function current() {
        return $this->_calendars[$this->_position];
    }
    
    public function key() {
        return $this->_position;
    }

    public function next() {
        ++$this->_position;
    }
    
    public function valid() {
        return isset($this->_calendars[$this->_position]);
    }

    //Implement array acces abastract methods.
    public function offsetSet($offset, $value) {
        if ($value instanceof Aevents) {
            if ($offset == "") {
                $this->_calendars[] = $value;
            }
            else {
                $this->_calendars[$offset] = $value;
            }
        }
    }
    
    public function offsetExists($offset) {
        return isset($this->_calendars[$offset]);
    }
    
    public function offsetGet($offset) {
        return $this->_calendars[$offset];
    }
    
    public function offsetUnset($offset) {
        unset($this->_calendars[$offset]);
    }
    
    // Gets the calendars a user has
    public function getCalendars($uid) {
        global $_TABLES;
        $i = 0;
        $sql = "select cid, title from {$_TABLES['calendarv2']} where owner_id = {$uid} OR cid = 1";
        $result = DB_query($sql);
        $num_rows = DB_numRows($result);
        $i = 0;
        while($num_rows) {
            $this->_calendars[$i]->loadFromArray(DB_fetchArray($result));
            $i++;
            $num_rows--;
        }
    } 
}

?>
    
