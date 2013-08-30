<?php

$no_header = 1;
require_once( '../_header.inc' );

if( $_SESSION[ 'admin' ] == 1 ) {

    // From http://www.somacon.com/p113.php
    function uuid() {
      
       // The field names refer to RFC 4122 section 4.1.2
    
       return sprintf('urn:uuid:%04x%04x-%04x-%03x4-%04x-%04x%04x%04x',
           mt_rand(0, 65535), mt_rand(0, 65535), // 32 bits for "time_low"
           mt_rand(0, 65535), // 16 bits for "time_mid"
           mt_rand(0, 4095),  // 12 bits before the 0100 of (version) 4 for "time_hi_and_version"
           bindec(substr_replace(sprintf('%016b', mt_rand(0, 65535)), '01', 6, 2)),
               // 8 bits, the last two of which (positions 6 and 7) are 01, for "clk_seq_hi_res"
               // (hence, the 2nd hex digit after the 3rd hyphen can only be 1, 5, 9 or d)
               // 8 bits for "clk_seq_low"
           mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535) // 48 bits for "node" 
       ); 
    }

    $grade_type = $db->real_escape_string( $_POST[ 'assignment_type' ] );
    $section = $db->real_escape_string( $_POST[ 'section' ] );
    $posted_date = date( 'Y-m-d H:i:s' );
    $due_date = date( 'Y-m-d H:i:s',
        strtotime( $db->real_escape_string( trim( $_POST[ 'due_date' ] ) )
		   . ' '
		   . $db->real_escape_string( trim( $_POST[ 'due_time' ] ) ) ) );
    $title = $db->real_escape_string( trim( $_POST[ 'title' ] ) );
    $description = $db->real_escape_string( trim( $_POST[ 'description' ] ) );

    $db->query( 'lock tables assignments, grade_events' );
    
    $assignment_query = 'insert into assignments '
        . '( id, grade_type, section, posted_date, due_date, title, description, '
        . 'grade_summary_tweeted ) '
        . "values( null, $grade_type, $section, \"$posted_date\", \"$due_date\", "
        . "\"$title\", \"$description\", 0 )";

    //print $assignment_query;

    $assignment_result = $db->query( $assignment_query );
    
    $grade_event_query = 'insert into grade_events '
        . '( id, section, grade_type, date, assignment ) values '
        . "( null, $section, $grade_type, "
        . '"' . date( 'Y-m-d', strtotime( $due_date ) ) . '", '
        . $db->insert_id . ' )';
    $grade_event_result = $db->query( $grade_event_query );
    
    $db->query( 'unlock tables' );

    // Add to Atom database

    // Get the course and section

    $section_query = 'select c.id as course_id, c.dept, c.course, s.section '
        . 'from courses as c, sections as s '
        . 'where s.course = c.id '
        . "and s.id = $section";

    //print $section_query;

    $section_result = $db->query( $section_query );
    $section_row = $section_result->fetch_assoc( );
    $section = $section_row[ 'dept' ] . ' ' . $section_row[ 'course' ]
        . ' ' . $section_row[ 'section' ];
    $section_result->free();

    // Get the assignment type

    $grade_type_query = 'select t.grade_type as t, w.collected as w '
        . 'from grade_types as t, grade_weights as w '
        . "where t.id = $grade_type "
        . 'and w.grade_type = t.id '
        . "and w.course = {$section_row[ 'course_id' ]}";

    $grade_type_result = $db->query( $grade_type_query );
    $grade_type_row = $grade_type_result->fetch_assoc( );
    $type = $grade_type_row[ 't' ];
    $grade_type_result->free();

    // Only add things to the Atom database that will be collected...

    if( $grade_type_row[ 'w' ] == 1 ) {
    	$atom_title = "$section: New " . strtolower( $type ) . ' posted';
        $atom_subtitle = '';
        if( $title != '' )
            $atom_subtitle = $title;
        $atom_content = "$atom_title.  Due " . date( 'l, F jS', strtotime( $due_date ) ) . '.';
        $atom_url = "http://{$_SERVER[ 'SERVER_NAME' ]}$docroot/";
        $atom_query = 'insert into atom( id, title, subtitle, content, url, uuid, posted ) '
            . "values( null, \"$atom_title\", \"$atom_subtitle\", \"$atom_content\", \"$atom_url\", \"" . uuid() . '", "'
            . date( 'c' ) . "\" )";
        print "<pre>$atom_query;</pre>\n";
        $atom_result = $db->query( $atom_query );
        $atom_result->free();    
    }

    // ... or exams that will happen in the future

    else if( preg_match( '/[Ee]xam/', $type ) == 1 and date( 'Y-m-d H:i' ) < date( 'Y-m-d H:i', strtotime( $due_date ) ) ) {

        $atom_title = "$section: $type Scheduled for " . date( "D, M j", strtotime( $due_date ) );
        $atom_subtitle = '';
        if( $title != '' )
            $atom_subtitle = $title;
        $atom_content = $atom_title;
        if( $description != '' ) {
            $atom_content .= '.  ' . $description;
            if( strlen( $atom_content ) > 140 ) {
                $atom_content = substr( $atom_content, 0, 137 ) . '...';
            }
        }
        $atom_url = "http://{$_SERVER[ 'SERVER_NAME' ]}$docroot/";
        $atom_query = 'insert into atom( id, title, subtitle, content, url, uuid, posted ) '
            . "values( null, \"$atom_title\", \"$atom_subtitle\", \"$atom_content\", \"$atom_url\", \"" . uuid() . '", "'
            . date( 'c' ) . "\" )";
        $atom_result = $db->query( $atom_query );
        $atom_result->free();    
    }

    // ... or HW, whether it's accepted or not
    else if( date( 'Y-m-d H:i' ) < date( 'Y-m-d H:i', strtotime( $due_date ) ) and preg_match( '/Homework/i', $type ) ) {
        $atom_title = "$section: New " . strtolower( $type ) . ' posted';
        $atom_subtitle = '';
        if( $title != '' )
            $atom_subtitle = $title;
        $atom_content = "$atom_title.  Due " . date( 'l, F jS', strtotime( $due_date ) ) . '.';
        $atom_url = "http://{$_SERVER[ 'SERVER_NAME' ]}$docroot/";
        $atom_query = 'insert into atom( id, title, subtitle, content, url, uuid, posted ) '
            . "values( null, \"$atom_title\", \"$atom_subtitle\", \"$atom_content\", \"$atom_url\", \"" . uuid() . '", "'
            . date( 'c' ) . "\" )";
        $atom_result = $db->query( $atom_query );
        $atom_result->free();    
    }
}

?>
