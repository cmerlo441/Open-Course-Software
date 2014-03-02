<?php

class Calendar {
    var $month;                // 1 = January
    var $year;                 // Four digits
    var $first_day;            // What day first of month falls on
    var $num_days;             // How many days in this month
    var $events = array( );    // What the cal. should link to
    var $holidays = array( );  // This month's holidays
    var $login_type;           // Who's logged in?
    var $login_section;        // Student's section ID
    var $docroot;              // Website's document root
    
    function Calendar( $m, $y, $docroot, $db, $attendance = 0 ) {
        $this->month = $m;
        $this->year = $y;
        $this->first_day = date( "w", mktime( 0, 0, 0, $m, 1, $y ) );
        $this->num_days = date( "t", mktime( 0, 0, 0, $m, 1, $y ) );
        $this->docroot = $docroot;

	/*
	 * This code used to generate lots of error messages,
	 * because $login_type and $login_section aren't
	 * set anywhere.  I think the goal was to tailor
	 * the Calendars to who was logged in, but
	 * in developing the new version, this won't be
	 * necessary.  More stuff commented out below because of this.

        $this->login_type = $login_type;
        $this->login_section = $login_section;

	*/

        if( $attendance != 0 ) {
            $this->attendance = $attendance;
        }
        
        // Find holidays
        $holiday_query = "select * from holidays where date >= \""
            . date( "Y-m-d", mktime( 0, 0, 0, $m, 1, $y ) )
            . "\" and date <= \""
            . date( "Y-m-d", mktime( 0, 0, 0, $m, $this->num_days, $y ) ) . "\"";
        
        // print "<pre>$holiday_query;</pre>\n";
        
        $holiday_result = $db->query( $holiday_query );
        while( $row = $holiday_result->fetch_assoc( ) ) {
            $this->holidays[ date( "j", strtotime( $row[ 'date' ] ) ) ]
		[ 'description' ] = $row[ 'description' ];
	    $this->holidays[ date( "j", strtotime( $row[ 'date' ] ) ) ]
		[ 'day' ] = $row[ 'day' ];
	    $this->holidays[ date( "j", strtotime( $row[ 'date' ] ) ) ]
		[ 'evening' ] = $row[ 'evening' ];
            
            
            /* DEBUG */
        }
    }
    
    /* disp
    * Displays a Calendar object to the screen
    */
    
    function disp( ) {
        $date = 1;
        $col = 0;
        
        $id = $this->year
            . ( $this->month < 10 ? ( '0' . $this->month ) : $this->month );
        
        print "<!-- Calendar for $id -->\n\n";
        
        print "<div class=\"calendar\" id=\"$id" . "\">\n";
        print "<table>\n";
        print "<tr class=\"head\">\n";
        print "  <td colspan=\"7\">"
            . date( "F", mktime( 0, 0, 0, $this->month, 1, $this->year ) )
            . " "
            . date( "Y", mktime( 0, 0, 0, $this->month, 1, $this->year ) )
            . "</td>\n";
        print "</tr>\n";
        
        print "<tr class=\"cal_row\">\n";
        for( $col = 0; $col < $this->first_day; $col++ ) {
            print "  <td class=\"" . ( $col == 0 ? "weekend" : "date" )
                . "\">&nbsp;</td>\n";
        }
        
        while( $date <= $this->num_days ) {
            if( $col % 7 == 0 ) {
                print "</tr>\n\n<tr class=\"cal_row\">\n";
            }
            
            print "  <td id=\"$date\" ";
    
            if( $this->year == date( "Y" ) && $this->month == date( "m" ) &&
                $date == date( "j" ) && ! isset( $this->attendance ) ) {
                print "class=\"today\">";
            } else if( $col % 7 == 0 || $col % 7 == 6 ) {
                print "class=\"weekend\">";
            } else if( isset( $this->attendance[ $date ] ) &&
		       $this->attendance[ $date ] == 'Present' ) {
                print "class=\"present\">";
            } else if( isset( $this->attendance[ $date ]) &&
		       $this->attendance[ $date ] == 'Absent' ) {
                print "class=\"absent\">";
            } else if( isset( $this->attendance[ $date ]) &&
		       $this->attendance[ $date ] == 'Excused' ) {
                print "class=\"excused\">";
            } else if( isset( $this->holidays[ $date ] ) &&
		       $this->holidays[ $date ] != '' ) {
                print "class=\"holiday\" title=\""
		    . $this->holidays[ $date ][ 'description' ]
		    . '.  ';
		if( $this->holidays[ $date ][ 'day' ] == 1 ) {
		    print 'Day ';
		    if( $this->holidays[ $date ][ 'evening' ] == 1 ) {
			print 'and evening ';
		    }
		} else {
		    print 'Evening ';
		}
		print "classes canceled.\">";
            } else {
                print "class=\"date\">";
            }
            
            if( isset( $this->events[ $date ] ) ) {
                $text = "";
                for( $i = 0; $i < count( $this->events[ $date ] ); $i++ ) {
                    if( $i > 0 ) {
                        $text .= "; ";
                    }
                    $text .= $this->events[ $date ][ $i ];
                }
                print "<a href=\"{$this->docroot}/cal_events.php?date={$this->year}-";
            
                print "{$this->month}-";
            
                if( $date < 10 ) {
                    print "0";
                }
                print "$date\"\n";
                print "title=\"$text\">$date</a>";
            } else {
                print "$date";
                
                if( isset( $this->attendance ) ) {
                    if( isset( $this->attendance[ $date ] ) ) {
                        print "<br />"
                        . substr( $this->attendance[ $date ], 0, 1 );
                    }
                }
            
            }
            print "</td>\n";
            $date++;
            $col++;
        }

        while( $col % 7 != 0 ) {
            print "  <td class=\"" . ( $col % 7 == 6 ? "weekend" : "date" )
                . "\">&nbsp;</td>\n";
            $col++;
        }
        
        print "</tr>\n</table>\n</div>\n";
    } // disp( )
    
    /* event_list
    * Generate array of dates that should have links
    */
    
    function event_list( ) {

	/* We don't need this stuff.
    
        $login_types = array( );
        
        $lt_query = "select * from login_type";
        $lt_result = $db->query( $lt_query );
        while( $row = $lt_result->fetch_assoc( ) ) {
            $login_types[ $row[ 'type' ] ] = $row[ 'id' ];
        }

	*/
        
        $now = date( "Y-m-d H:i:s" );
        $today = date( "Y-m-d" );
        $month_start = date( "Y-m-d H:i:s", mktime( 0, 0, 0, $this->month, 1, $this->year ) );
        $month_end = date( "Y-m-d H:i:s", mktime( 23, 59, 59, $this->month, $this->num_days, $this->year ) );
        
        $holiday_query = "select id, holiday, unix_timestamp( date ) as date "
            . "from holidays "
            . "where date >= \"$month_start\" "
            . "and date <= \"$month_end\" "
            . "order by date";
        $holiday_result = $db->query( $holiday_query );
        while( $holiday_row = $holiday_result->fetch_assoc( ) ) {
            $date = date( "j", $holiday_row[ 'date' ] );
            if( !isset( $this->events[ $date ] ) ) {
                $this->events[ $date ] = array( );
            }
            $count = count( $this->events[ $date ] );
            $this->events[ $date ][ $count ] =
            "No School: {$holiday_row[ 'holiday' ]}";
        }
        
        $days_list = array( "Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday" );
        $re_query = "select id, follow, unix_timestamp( date ) as date "
            . "from rescheduled_days "
            . "where date >= \"$month_start\" "
            . "and date <= \"$month_end\" "
            . "order by date";
        $re_result = $db->query( $re_query );
        while( $re_row = $re_result->fetch_assoc( ) ) {
            $date = date( "j", $re_row[ 'date' ] );
            if( !isset( $this->events[ $date ] ) ) {
                $this->events[ $date ] = array( );
            }
            $count = count( $this->events[ $date ] );
            $string = "Follow " . $days_list[ $re_row[ 'follow' ] % 7 ]
                . " Schedule";
            $this->events[ $date ][ $count ] = $string;
        }
        
        // If no one is logged in, don't show any hw, events, or projects
        
        if( $this->login_type > 0 ) {
        $events_query = "select id, short_description as sd, "
            . "section, unix_timestamp( start ) as start "
            . "from events where ";
        if( $this->login_type == $login_types[ 'student' ] ) {
            $events_query .= "section = $this->login_section and ";
        }
        $events_query .= "start >= \"$month_start\" and start <= \"$month_end\" "
            . "order by start";
        $events_result = $db->_query( $events_query );
        while( $events_row = $events_result->fetch_assoc( ) ) {
            $date = date( "j", $events_row[ 'start' ] );
            if( !isset( $this->events[ $date ] ) ) {
                $this->events[ $date ] = array( );
            }
            $count = count( $this->events[ $date ] );
            $string = "";
            if( ( $this->login_type == $login_types[ 'ta' ] ||
                $this->login_type == $login_types[ 'admin' ] ) &&
                $events_row[ 'section' ] > 0 ) {
                $course_query = "select c.dept as dept, c.course as course, "
                    . "s.section as section "
                    . "from courses as c, sections as s "
                    . "where s.course = c.id "
                    . "and s.id = {$events_row[ 'section' ]}";
                $course_result = $db->_query( $course_query );
                $course_row = $course_result->fetch_assoc( );
                $string = "{$course_row[ 'dept' ]} "
                    . leading_zeros( $course_row[ 'course' ] )
                    . " {$course_row[ 'section' ]}: ";
            }
            $string .= $events_row[ 'sd' ];
            $this->events[ $date ][ $count ] = $string;
        } // for each event
        
        $hw_query = "select id, section, unix_timestamp( due_date ) as date "
            . "from homework "
            . "where due_date >= \"$month_start\" "
            . "and due_date <= \"$month_end\" ";
        if( $this->login_type == $login_types[ 'student' ] ) {
            $hw_query .= "and section = $this->login_section";
        }
        $hw_result = $db->query( $hw_query );
        //      print "<p>$hw_query</p>\n";
        while( $hw_row = $hw_result->fetch_assoc( ) ) {
            $date = date( "j", $hw_row[ 'date' ] );
            if( !isset( $this->events[ $date ] ) ) {
                $this->events[ $date ] = array( );
            }
            $count = count( $this->events[ $date ] );
            $string = "";
            if( ( $this->login_type == $login_types[ 'ta' ] ||
                $this->login_type == $login_types[ 'admin' ] ) ) {
                $course_query = "select c.dept as dept, c.course as course, "
                    . "s.section as section "
                    . "from courses as c, sections as s "
                    . "where s.course = c.id "
                    . "and s.id = {$hw_row[ 'section' ]}";
                $course_result = $db->query( $course_query );
                $course_row = $course_result->fetch_assoc( );
                $string = "{$course_row[ 'dept' ]} "
                    . leading_zeros( $course_row[ 'course' ] )
                    . " {$course_row[ 'section' ]}: ";
                }
            $string .= "Homework Due";
            $this->events[ $date ][ $count ] = $string;
        }
        
        $project_query = "select id, section, "
            . "unix_timestamp( deadline ) as dl, project, description as dc "
            . "from projects "
            . "where deadline >= \"$month_start\" "
            . "and deadline <= \"$month_end\" ";
        if( $this->login_type == $login_types[ 'student' ] ) {
            $project_query .= "and section = $this->login_section ";
        }
        $project_result = $db->query( $project_query );
        while( $project_row = $project_result->fetch_assoc( ) ) {
            $date = date( "j", $project_row[ 'dl' ] );
            if( !isset( $this->events[ $date ] ) ) {
                $this->events[ $date ] = array( );
            }
            $count = count( $this->events[ $date ] );
            $string = "";
            if( ( $this->login_type == $login_types[ 'ta' ] ||
                $this->login_type == $login_types[ 'admin' ] ) ) {
                $course_query = "select c.dept as dept, c.course as course, "
                    . "s.section as section "
                    . "from courses as c, sections as s "
                    . "where s.course = c.id "
                    . "and s.id = {$project_row[ 'section' ]}";
                $course_result = $db->query( $course_query );
                $course_row = $course_result->fetch_assoc( );
                $string = "{$course_row[ 'dept' ]} "
                    . leading_zeros( $course_row[ 'course' ] )
                    . " {$course_row[ 'section' ]}: ";
                }
                $string .= "Project {$project_row[ 'project' ]}: "
                    . "{$project_row[ 'dc' ]} Due";
                $this->events[ $date ][ $count ] = $string;
            } // for each project
        
        } // if someone's logged in
        
    } // function event_list
    
    /* attendance
    * Generate array of dates with a student's attendance record
    */
    
    function attendance( $student_id ) {
    
        foreach( explode( ' ', 'present absent excused' ) as $table ) {
            $table_query = "select date from attend_$table "
                . "where student = \"$student_id\" "
                . "and date >= \""
                . date( "Y-m-d", mktime( 0, 0, 0, $this->month, 1, $this->year ) )
                . "\" and date <= \""
                . date( "Y-m-d", mktime( 0, 0, 0, $this->month, $this->num_days,
                    $this->year ) )
                . "\" order by date";
            $table_result = @mysql_query( $table_query );
            while( $row = @mysql_fetch_assoc( $table_result ) ) {
                $this->attendance[ date( 'j', strtotime( $row[ 'date' ] ) ) ] =
                strtoupper( substr( $table, 0, 1 ) );
            } // while
        } // for each table type
    
    } // function attendance

} // class Calendar
