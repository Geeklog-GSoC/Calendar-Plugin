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
    //Geeklog Permissions Variables
    protected $_owner;
    protected $_group;
    protected $_group_id;
    protected $_perm_owner;
    protected $_perm_group;
    protected $_perm_members;
    protected $_perm_anon; 

    // Normal Variables
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
    
    // Some setters.
    public function setCid($cid) {
        $this->_cid = $cid;
    }

    public function setEvents(Aevents $events) {
        $this->_events = $events;
    } 

    // Some getters.
    public function getCid() {
        return $this->_cid;
    }

    public function getTitle() {
        return $this->_title;
    }
    
    public function getEvents() {
        return $this->_events;
    }
    
    public function getEvent($eid) {
        return $this->_events->getEvent($eid);
    }

    /**
    * Returns a matrix with days of a month first day of the month is set 
    * to default 1 (Sun)
    * 
    * @param    int     $month  the month number
    * @param    int     $year   the year number
    * @param    int     $first_day_config the first day of the week for display.
    * @return   array   
    */ 
     public function generateMatrix($month, $year, $first_day_config = 1) {
        $start_date = mktime(0,0,0, $month, 1, $year);
        $days_in_month = date('t', $start_date);
        $first_day = date('w', $start_date);
        $j = $first_day - $first_day_config + 1;  // Trick to set the first day corectly
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

    /**
    *   Add an event to the current list of events for this specific calendar object
    *
    * @param    object  $event
    *
    */ 
    public function addEvent(Event $event) 
    {
        $cid = $event->getCid();
        if (is_null($cid)) {
            $event->setCid($this->_cid);
        }
        $this->_events->addEvent($event);
    }

    /**
    * populates an array of events. It querys the datebase betwen 2 moments of time
    *
    * @param    object  $date_start The start of the timespan
    * @param    object  $date_end   The end of the timespan
    * @param    integer $cid    The calendar id
    *
    */ 
    public function getEventsSpan(DateTime $datestart, DateTime $dateend, $debug = NULL) {
        $this->_events->getElements($datestart, $dateend, $this->_cid, $debug);
    }

    /** 
    * Gets the events from a certain day
    *
    * @param    object  $date   A DateTime object   for the current day
    *
    */
    public function getTodayEvents(Datetime $date) {
        $a = new Aevents();
        $day_end = clone($date);
        $day_end->modify('+1 day');
        $a->getElements($date, $day_end, $this->_cid);
        return $a;
    }

    /** 
    * Loads a calendar from a POST array
    *
    * @param    object  $date   A DateTime object   for the current day
    *
    */ 
    public function loadFromArray($A) {
        $this->_title = $A['title'];
        list($this->_perm_owner, $this->_perm_group, $this->_perm_members, $this->_perm_anon) = 
                array($A['perm_owner'], $A['perm_groups'] , $A['perm_members'], $A['perm_anon']); 
        $this->_cid = $A['cid'];
    }

    /**
    * Saves an event to the database
    *
    */
    public function saveEvents() {
        foreach ($this->_events as $event) {
            $event->save_to_database();
        }
    }

    /** 
    * Gets details for a calendar, The id and the events
    *
    */
    public function getCalendar($cid) {
        // I should also get the title
        $this->_cid = $cid;
        $this->_events->getEvents($cid);
    }
}

class Acalendarv2 implements arrayaccess, iterator {
    private $_position;
    private $_calendars = array(); 
    // Implement iterator abastract methods
    public function __construct() {
        $this->_position = 0;
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
    
    /** 
    * Get the calendars where the user has certain rights.
    * The values of $rights variable are those used through geeklog
    * 3 for writing, 2 for reading
    *
    * @param    $rights 
    *
    */  
    public function getCalendars($rights) {
        global $_TABLES, $_USER;
        $sql = "select * from {$_TABLES['c2_calendars']}" ;
        $sql .= COM_getPermSQL('where', $_USER['uid'], $rights);
        $result = DB_query($sql);
        $i = 0;
        while ($array = DB_fetchArray($result)) {
            $this->_calendars[] = new Calendarv2();
            $this[$i]->loadFromArray($array);
            $i++;
        }
    }

    /** 
    * Get the number of calendars.
    *
    */ 
    public function getNum() {
        return count($this->_calendars);
    }
}

?>
    
