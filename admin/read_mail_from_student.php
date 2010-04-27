<?php

$no_header = 1;
require_once( '../_header.inc' );

if( $_SESSION[ 'admin' ] == 1 ) {
    $message_id = $db->real_escape_string( $_POST[ 'id' ] );
    $message_query = 'select c.dept, c.course, sections.section, '
        . 's.first, s.middle, s.last, m.subject, m.id, m.message, m.sent_time '
        . 'from courses as c, sections, students as s, mail_from_students as m, '
        . 'student_x_section as x '
        . 'where x.student = s.id '
        . 'and x.section = sections.id '
        . 'and sections.course = c.id '
        . 'and m.student_x_section = x.id '
        . "and m.id = $message_id";
    $message_result = $db->query( $message_query );
    $message_row = $message_result->fetch_assoc( );
    print "<h2>{$message_row[ 'dept' ]} {$message_row[ 'course' ]} "
        . "{$message_row[ 'section' ]}: {$message_row[ 'subject' ]}</h2>\n";
    print "<p>From <b>{$message_row[ 'first' ]}";
    if( $message_row[ 'middle' ] != '' ) {
        print ' ' . $message_row[ 'middle' ];
    }
    print " {$message_row[ 'last' ]}</b> on "
        . date( 'l, F j, Y \a\t g:i a', strtotime( $message_row[ 'sent_time' ] ) )
        . "</p>\n";
    print '<p class="email">'
        . stripslashes( wordwrap( nl2br( $message_row[ 'message' ] ) ) ) . "</p>\n";
}

?>