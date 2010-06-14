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
    var $_creation_date;
    var $day_number = 0;
    var $matrix = array();
 
    /**
    *
    * Constructor
    *
    * Initializes calendar object
    *
    */

    public function __construct() {
        $this->_creation_date = getdate(time());
    }

    /**
    * Returns a matrix with days of a month
    * 
    * @param    int     $month  the month number
    * @param    int     $year   the year number
    * @return   array   
    */ 
     public function c2_generateMatrix($month, $year) {
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
}

?>
    
