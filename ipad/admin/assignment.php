<?php

$title_stub = 'Assignment Details';
require_once( '../_header.inc' );

if( $_SESSION[ 'admin' ] == 1 ) {
    
    $assignment = $db->real_escape_string( $_GET[ 'assignment' ] );
    $assignment_query = 'select * from assignments '
        . 'where id = ' . $assignment;
    $assignment_result = $db->query( $assignment_query );
    $assignment = $assignment_result->fetch_object( );
    
    // What class is this?
    
    $course_query = 'select c.id as course_id, c.dept, c.course, s.section, s.id as sid '
        . 'from courses as c, sections as s '
        . 'where s.course = c.id '
        . "and s.id = \"$assignment->section\"";
    $course_result = $db->query( $course_query );
    $course = $course_result->fetch_object( );
    $course_name = "$course->dept $course->course $course->section";
    
    // What kind of assignment is this?
    
    $grade_types_query = 'select t.grade_type, t.plural, t.id, '
        . 'w.collected, w.grade_weight as w ' 
        . 'from grade_types as t, grade_weights as w, sections as s '
        . "where t.id = $assignment->grade_type "
        . "and s.id = $course->sid "
        . 'and s.course = w.course '
        . 'and w.grade_type = t.id '
        . 'order by t.grade_type';
    $grade_types_result = $db->query( $grade_types_query );
    $grade_type = $grade_types_result->fetch_object( );

    // How many have there been?
    $count_query = 'select count( id ) as count from assignments '
        . "where section = \"$assignment->section\" "
        . "and grade_type = \"$assignment->grade_type\"";
    $count_result = $db->query( $count_query );
    $count_row = $count_result->fetch_assoc( );
    $count = $count_row[ 'count' ];
    
    // Which # assignment is this?
        
    $sequence_query = 'select count( id ) as amount from assignments '
        . "where section = \"$assignment->section\" "
        . "and grade_type = \"$assignment->grade_type\" "
        . "and due_date <= \"$assignment->due_date\"";
    $sequence_result = $db->query( $sequence_query );
    $sequence_row = $sequence_result->fetch_assoc( );
    $sequence = $sequence_row[ 'amount' ];
    
    $assignment_string = $grade_type->grade_type;
    if( $count > 1 ) {
        $assignment_string .= " #{$sequence}";
    }
    
    print "<div data-role=\"header\" data-inset=\"true\">\n";
    print "<h1>$course_name $assignment_string</h1>\n";
    print "</div>\n";
    
    if( $assignment->title != '' ) {
        print "<div data-role=\"header\" data-theme=\"c\">\n";
        print "<h1>$assignment->title</h1>\n";
        print "</div>\n";
    }
    
    print "<ul data-role=\"listview\" data-theme=\"c\" data-inset=\"true\">\n";

    if( $assignment->description != '' ) {
        print "<li data-role=\"list-divider\">Details</li>\n";
        print "<li><h3>" . nl2br( $assignment->description ) . "</h3></li>\n";
    }
    
    if( $grade_type->w > 0 and $assignment->due_date < date( 'Y-m-d G:i:s' ) ) {
        print "<li data-role=\"list-divider\">Grades</li>\n";
        
        $students_query = 'select s.id, s.first, s.last '
            . 'from students as s, student_x_section as x '
            . 'where x.student = s.id '
            . "and x.section = $course->sid "
            . 'and x.active = 1 '
            . 'order by s.last, s.first';
        $students_result = $db->query( $students_query );
        while( $student = $students_result->fetch_object( ) ) {
            $grades_query = 'select g.grade, g.id, e.id as e '
                . 'from grades as g, grade_events as e '
                . 'where g.grade_event = e.id '
                . "and e.assignment = $assignment->id "    
                . "and g.student = $student->id";
            $grades_result = $db->query( $grades_query );
            $grade = $grades_result->fetch_object( );
            print "<li><div data-role=\"fieldcontain\" student=\"$student->id\" grade_event=\"$grade->e\">\n"
                . "<label for=\"slider-$grade->id\">"
                . ucwords( "$student->first $student->last" ) . "</label>\n";
            print "<input type=\"range\" name=\"slider\" id=\"slider-$grade->id\" "
                . "value=\"$grade->grade\" min=\"0\" max=\"100\" data-highlight=\"true\" ";
            if( $grade->grade == '' )
                print "data-theme=\"e\"";
            print "/>\n";
            print "</div></li>\n";
        }
    }
    
    print "</ul>\n";
    
} else {
    print 'You are not authorized to view this page.';
}

require_once( '../_footer.inc' );
?>