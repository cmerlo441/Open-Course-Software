<?php

$no_header = 1;
require_once( '../_header.inc' );

function column( $column ) {
    $count = 0;
    $output = '';
    
    while( $column > 0 ) {
        $output .=
            ( chr( ord( 'A' ) +
            ( $column > 26 ? ( ( $column / 26 ) - 1 ) : $column - 1 ) ) );
        $column -= 26;
    }
    return $output;
}

if( $_SESSION[ 'admin' ] == 1 ) {
    
    $section = $db->real_escape_string( $_GET[ 'section' ] );
    
    $grades = array( );
    // Indices: student_id grade_type grade_event
    
    $events = array( );
    
    $type_counts = array( );
    
    $type_query = 'select id, grade_type from grade_types';
    $type_result = $db->query( $type_query );
    while( $type = $type_result->fetch_assoc( ) ) {
        $type_counts[ $type[ 'id' ] ][ 'count' ] = 0;
        $type_counts[ $type[ 'id' ] ][ 'description' ] = $type[ 'grade_type' ];
    }
    
    $course_query = 'select c.id, c.dept, c.course, s.section, s.banner '
        . 'from courses as c, sections as s '
        . 'where s.course = c.id '
        . "and s.id = $section";
    $course_result = $db->query( $course_query );
    $course_row = $course_result->fetch_assoc( );
    $course = $course_row[ 'dept' ] . ' ' . $course_row[ 'course' ] . ' '
        . $course_row[ 'section' ] . ' (' . $course_row[ 'banner' ] . ')';
    
    $weights = array( );
    $weights_query = 'select w.grade_type, w.grade_weight, t.id, t.grade_type as description '
        . 'from grade_weights as w, grade_types as t '
        . "where w.course = {$course_row[ 'id' ]} "
        . 'and w.grade_type = t.id '
        // and weight > 0 ?
        . 'order by w.grade_type';
    $weights_result = $db->query( $weights_query );
    while( $w_row = $weights_result->fetch_assoc( ) ) {
        $weights[ $w_row[ 'grade_type' ] ] = $w_row[ 'grade_weight' ];
    }
    $first_column = array( );  // First column of each type of grade event
    $column = 4;

    $events_query = 'select e.id, e.grade_type, e.date '
        . 'from grade_events as e '
        . "where e.section = $section "
        . 'order by e.grade_type, e.date';
    $events_result = $db->query( $events_query );
    while( $event = $events_result->fetch_assoc( ) ) {
        $type_counts[ $event[ 'grade_type' ] ][ 'count' ]++;
        
        $events[ $event[ 'id' ] ][ 'type' ] = $event[ 'grade_type' ];
        $events[ $event[ 'id' ] ][ 'date' ] =
            date( 'm/d/Y', strtotime( $event[ 'date' ] ) );
        $events[ $event[ 'id' ] ][ 'description' ] =
            $type_counts[ $event[ 'grade_type' ] ][ 'description' ];
        
        // If there are more than one of these events, label each with a #
        // like "Exam 1"; otherwise, just "Exam"
        
        $event_count_query = 'select * from grade_events '
            . "where grade_type = {$event[ 'grade_type' ]} "
            . "and section = $section";
        $event_count_result = $db->query( $event_count_query );
        if( $event_count_result->num_rows > 1 ) {
            $events[ $event[ 'id' ] ][ 'description' ]
                .= ' ' . ( $type_counts[ $event[ 'grade_type' ] ][ 'count' ] );
        }
        $events[ $event[ 'id' ] ][ 'column' ] = column( $column );
        
        // Someday curve logic goes here.  For now:
        $events[ $event[ 'id' ] ][ 'curve' ] = 0;
        
        // Remember the first column with this type of grade in it
        if( $type_counts[ $event[ 'grade_type' ] ][ 'count' ] == 1 ) {
            $first_column[ $event[ 'grade_type' ] ] = $column;
        }
        
        $column++;
    }
    
    $averages = array( );

    // Make this a text download
    header( "Content-type: text/plain" );
    
    // Generate the filename
    if( !isset( $_GET[ 'debug' ] ) ) {
        header( "Content-disposition: attachment; "
            . 'filename='
            . strtolower( $course_row[ 'dept' ] )
            . strtolower( $course_row[ 'course' ] )
            . strtolower( $course_row[ 'section' ] )
            . '.csv' );
    }
    
    // Using a comma as a column separator makes Excel angry
    if( $_GET[ type ] == 'Excel' ) {
        $col_sep = "\t";
    } else {
        $col_sep = ',';
    }

    // Each spreadsheet has its own argument separator (of course)
    if( $_GET[ 'type' ] == 'Excel' or $_GET[ 'type' ] == 'Gnumeric' ) {
        $arg_sep = ',';
    } else if( $_GET[ 'type' ] == 'OOo' ) {
        $arg_sep = ';';
    }


    /* First four lines:
     * Dept Course Section
     * Semester Year
     * Prof. First Last
     * 
     */
    
    print "$course\n$semester\n";
    print "Prof. {$prof[ 'name' ]}\n\n";
    
    // Row 5
    print "{$col_sep}{$col_sep}";  // Skip first two columns
    
    // Individual grade events
    foreach( $events as $key=>$value ) {
        print "{$col_sep}{$events[ $key ][ 'description' ]}";
    }
    
    // Row 5 headers for necessary averages (if there's more than 1 of this type)
    foreach( $type_counts as $key=>$value ) {
        if( $type_counts[ $key ][ 'count' ] > 1 ) {
            print "{$col_sep}{$type_counts[ $key ][ 'description' ]}";
            // Remember where the averages are
            $averages[ $key ][ 'column' ] = column( $column++ );
        }
    }
    
    // Columns for final average and final letter grade
    print "{$col_sep}Final{$col_sep}Final\n";
    
    // Determine Row 6
    // Storing this for later helps a lot later
    $row6 = "Banner ID{$col_sep}Last{$col_sep}First";
    
    // Individual grade events
    foreach( $events as $key=>$value ) {
        $row6 .= "{$col_sep}{$events[ $key ][ 'date' ]}";
    }
    
    // Headers for averages
    foreach( $type_counts as $key=>$value ) {
        if( $type_counts[ $key ][ 'count' ] > 1 ) {
            $row6 .= "{$col_sep}Average";
        }
    }
    
    // Final grade columns
    $row6 .= "{$col_sep}Average{$col_sep}Grade";
    
    print "$row6\n";
    
    // Get all the grades from the DB and put them in an array.
    
    $grades_query = 'select student, grade_event, grade from grades';
    $grades_result = $db->query( $grades_query );
    while( $grade = $grades_result->fetch_assoc( ) ) {
        $grades[ $grade[ 'student' ] ][ $grade[ 'grade_event' ] ] = $grade[ 'grade' ];
    }
    
    $row = 7;
    
    // Start displaying student data
    $students = array( );
    $student_query = 'select s.id, s.first, s.last, s.banner, x.active, x.incomplete '
        . 'from students as s, student_x_section as x '
        . 'where x.student = s.id '
        . "and x.section = $section "
        . 'order by s.last, s.first';
    $student_result = $db->query( $student_query );
    while( $student = $student_result->fetch_assoc( ) ) {
        print ucwords( $student[ 'banner' ] ) . $col_sep . ucwords( $student[ 'last' ] ) . $col_sep . ucwords( $student[ 'first' ] );
        foreach( $events as $key=>$value ) {
            $grade = ( $grades[ $student[ 'id' ] ][ $key ] != '' ?
                $grades[ $student[ 'id' ] ][ $key ] + $events[ $key ][ 'curve' ] : 0 );
            if( $events[ $key ][ 'curve_type' ] == 'Add n' && $grade > 100 ) {
                $grade = 100;
            }
            print "{$col_sep}{$grade}";
        }
        
        // Display averages
        
        foreach( $type_counts as $key=>$value ) {
            
            /* If there's more than one of these, then we left a column
             * for the average (otherwise, we didn't)
             */
            
            if( $type_counts[ $key ][ 'count' ] > 1 ) {
                
                print "{$col_sep}=if(sum(";
                print column( $first_column[ $key ] ) . "$row:"
                    . column( $first_column[ $key ] + $type_counts[ $key ][ 'count' ] - 1 )
                    . "$row)=0{$arg_sep}0{$arg_sep}";
                print "average(" . column( $first_column[ $key ] ) . "$row:"
                    . column( $first_column[ $key ] + $type_counts[ $key ][ 'count' ] - 1 )
                    . "$row))";
            }
        }
        
        // Calculate final average
        
        print "{$col_sep}=(";
        $count = 0;
        foreach( $type_counts as $key=>$value ) {
            if( $type_counts[ $key ][ 'count' ] > 1 ) {
                if( $count > 0 ) {
                    print '+';
                }
                $count++;
                print "{$averages[ $key ][ 'column' ]}$row*{$weights[ $key ]}";
            } else if( $type_counts[ $key ][ 'count' ] == 1 ) {
                if( $count > 0 ) {
                    print '+';
                }
                $count++;
                print column( $first_column[ $key ] ) . "$row*{$weights[ $key ]}";
            }
        }
        print ")/100";
        
        // Calculate letter grade

        /* So cool: substr_count( ) counts the frequency of occurrence of
         * a substring within a string.  Since way up there, the contents
         * of row 6 are stored in a string, we can just count how many
         * column separators (commas or tabs) are in that string.  The
         * result is one less than the amount of columns in this CSV.
         * That second-to-last column stores the student's numerical
         * average, which we can key on to calculate the letter grade.
         * PHP rocks.
         */

        $final_location = column( substr_count( $row6, $col_sep ) ) . $row;
        
        if( $student[ 'active' ] == 0 ) {
            print "{$col_sep}\"W\"";
        } else if( $student[ 'incomplete' ] == 1 ) {
            print "{$col_sep}\"I\"";
        } else {
            print "{$col_sep}=";
            $letter_grades = array( );
            $lg_query = 'select letter, grade from letter_grades '
                . 'order by grade desc';
            $lg_result = $db->query( $lg_query );
            $count = 0;
            while( $lg_row = $lg_result->fetch_assoc( ) ) {
                print "if({$final_location}>={$lg_row[ 'grade' ]}"
                    . $arg_sep
                    . "\"{$lg_row[ 'letter' ]}\""
                    . $arg_sep;
                $count++;
            }
            print '"F"';
            for( ; $count > 0; $count-- ) {
                print ')';
            }
        }
        print "\n";
        $row++;
    }
    
}


?>