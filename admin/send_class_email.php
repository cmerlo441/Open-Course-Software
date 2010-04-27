<?php

$no_header = 1;
require_once( '../_header.inc' );

if( $_SESSION[ 'admin' ] == 1 ) {
    $section_list = preg_replace( '/(.*),/', '$1', $_POST[ 'sections' ] );
    $sections = explode( ',', $section_list );
    
    $send[ 'subject' ] = stripslashes( trim( $_POST[ 'subject' ] ) );
    $send[ 'message' ] = stripslashes( trim( $_POST[ 'message' ] ) );
    
    $insert[ 'subject' ] = $db->real_escape_string( $send[ 'subject' ] );
    $insert[ 'message' ] = $db->real_escape_string( $send[ 'message' ] );
    
    foreach( $sections as $section ) {
        $section_query = 'select c.dept, c.course, s.section '
            . 'from courses as c, sections as s '
            . 'where s.course = c.id '
            . "and s.id = $section";
        $section_result = $db->query( $section_query );
        $section_row = $section_result->fetch_assoc( );
        $section_name = "{$section_row[ 'dept' ]} {$section_row[ 'course' ]} "
            . $section_row[ 'section' ];
        
        $students_query = 'select s.first, s.middle, s.last, s.email '
            . 'from students as s, student_x_section as x '
            . 'where x.student = s.id '
            . 'and x.active = 1 '
            . "and x.section = $section "
            . 'order by last, first, middle';
        $students_result = $db->query( $students_query );
        $student = $students_result->fetch_assoc( );
        $student_list = '"' . $student[ 'first' ] . ' ';
        if( $student[ 'middle' ] != ' ' ) {
            $student_list .= $student[ 'middle' ] . ' ';
        }
        $student_list .= $student[ 'last' ] . '" <' . $student[ 'email' ] . '>';
        while( $student = $students_result->fetch_assoc( ) ) {
            $student_list .= ', "' . $student[ 'first' ] . ' ';
            if( $student[ 'middle' ] != ' ' ) {
                $student_list .= $student[ 'middle' ] . ' ';
            }
            $student_list .= $student[ 'last' ] . '" <' . $student[ 'email' ] . '>';
        }
        $headers = "From: {$prof[ 'name' ]} <{$prof[ 'email' ]}>\n"
            . "X-Mailer: PHProf Version {$phprof[ 'version' ]}\n"
            . "Bcc: $student_list\n";

        mail( "$section_name Students <{$prof[ 'email' ]}>", $send[ 'subject' ],
              $send[ 'message' ], $headers );
       
        // Insert mail into the DB
        $insert_query = 'insert into mail_to_classes '
            . '( id, section, subject, message, sent_time ) '
            . "values( null, $section, \"{$insert[ 'subject' ]}\", "
            . "\"{$insert[ 'message' ]}\", \"" . date( 'Y-m-d H:i:s' ) . '" )';
        $insert_result = $db->query( $insert_query );
    }

    print "<h2 style=\"color: #1e273e;\">Mail Sent</h2>\n";
    print "<p>Your mail has been sent, and saved in the database.</p>\n";

}

?>