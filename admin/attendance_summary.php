<?php

$title_stub = 'Attendance Summary';
require_once ('../_header.inc' );

if( $_SESSION[ 'admin' ] == 1 ) {
    
    $sections_query = 'select s.id, c.dept, c.course, s.section '
        . 'from courses as c, sections as s '
        . 'where s.course = c.id '
        . 'order by c.dept, c.course, s.section';
    $section_result = $db->query( $sections_query );
    while( $section_object = $section_result->fetch_object( ) ) {
        $section = $section_object->id;
        $students = array( );
        
        print "<h2>$section_object->dept $section_object->course $section_object->section</h2>\n";

        $students_query = 'select s.first, s.last, s.banner, s.id '
            . 'from students as s, student_x_section as x '
            . "where x.section = $section "
            . 'and x.student = s.id '
            . 'order by s.last, s.first, s.middle, s.banner';
        $students_result = $db->query( $students_query );
        while( $students_row = $students_result->fetch_object( ) ) {
            $students[ $students_row->id ][ 'first' ] = ucwords( $students_row->first );
            $students[ $students_row->id ][ 'last' ] = ucwords( $students_row->last );
            $students[ $students_row->id ][ 'banner' ] = $students_row->banner;
        }
        
        $months_query = 'select start, end from semester';
        $months_result = $db->query( $months_query );
        $months = $months_result->fetch_object( );
        
        $first_month = date( 'm', strtotime( $months->start ) );
        $last_month = date( 'm', strtotime( $months->end ) );
        
        for( $month = $first_month; $month <= $last_month; $month = date( 'm', mktime( 0, 0, 0, ++$month, 1, date( 'Y' ) ) ) ) {
            $dates = array( );
            print "<h3>" . date( 'F', mktime( 0, 0, 0, $month, 1, date( 'Y' ) ) ) . "</h3>\n";
    
            $attendance_query = 'select a.student, a.date, t.type '
                . 'from attendance as a, attendance_types as t '
                . "where a.section = $section "
                . 'and a.presence = t.id '
                . "and date like \"" . date( 'Y' ) . "-$month%\" "
                . 'order by a.date, a.student';
            $attendance_result = $db->query( $attendance_query );
            while( $attendance_row = $attendance_result->fetch_object( ) ) {
                $day = date( 'j', strtotime( $attendance_row->date ) );
                $students[ $attendance_row->student ][ $month ][ $day ] = substr( $attendance_row->type, 0, 1 );
                if( !isset( $dates[ $day ] ) or ( $dates[ $day ] == 0 ) )
                    $dates[ $day ] = 1;
            }
    
    /*        
            print "<pre>";
            print_r( $students );
            print "</pre>\n";
    */
    
            print "<table class=\"tablesorter\" id=\"attendance_summary_$month\">\n";
            print "<thead>\n";
            print "  <th>ID</th>\n";
            print "  <th>First</th>\n";
            print "  <th>Last</th>\n";
    
            foreach( $dates as $key=>$value ) {
                if( $value == 1 )
                    print "  <th>$key</th>\n";
            }
            print "</thead>\n\n";
            
            print "<tbody>\n";
            foreach( $students as $data ) {
                print "  <tr>\n";
                print "    <td>{$data[ 'banner' ]}</td>\n";
                print "    <td>{$data[ 'first' ]}</td>\n";
                print "    <td>{$data[ 'last' ]}</td>\n";
                
                foreach( $dates as $key=>$value ) {
                    if( $value == 1 ) {
                        $output = $data[ $month ][ $key ] == '' ? 'A' : $data[ $month ][ $key ];
                        print "    <td>$output</td>\n";
                    }
                }
                
                print "  </tr>\n";
            }
            print "</tbody>\n";
            print "</table>\n";
        }
    }
    
?>

<script type="text/javascript">

$(document).ready(function(){
    $('table.tablesorter').tablesorter( {
        sortList: [ [2,0], [1,0] ],
        widgets:  [ 'ocsw' ]
    });
})

</script>

<?
    
}
?>
