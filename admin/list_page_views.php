<?php

$no_header = 1;
require_once( '../_header.inc' );

if( $_SESSION[ 'admin' ] == 1 ) {
    unset( $student_id );
    unset( $page_name );
    
    if( isset( $_REQUEST[ 'student' ] ) and $_REQUEST[ 'student' ] != 0 ) {
        $student_id = $db->real_escape_string( $_REQUEST[ 'student' ] );
    } else if( isset( $_REQUEST[ 'page_name' ] ) and $_REQUEST[ 'page_name' ] != '0' ) {
        $page_name = urldecode( $db->real_escape_string( $_REQUEST[ 'page_name' ] ) );
    }
    
    if( isset( $_REQUEST[ 'last_id' ] ) ) {
        $last_id = $db->real_escape_string( $_REQUEST[ 'last_id' ] );
    }
    
    $views_query = 'select v.id, v.student, v.page, v.get_string, v.datetime, v.referrer, v.ip, s.first, s.last '
        . 'from page_views as v, students as s '
        . 'where s.id = v.student ';
    if( isset( $student_id ) ) {
        $views_query .= "and v.student = $student_id ";
    } else if( isset( $page_name ) ) {
        $views_query .= "and page like \"%$page_name\" ";
    }
    if( isset( $last_id ) ) {
        $views_query .= " and v.id < $last_id ";
    }
    $views_query .= 'order by v.datetime desc '
        . 'limit 30';
    $views_result = $db->query( $views_query );
    
    $even = 'even';
    while( $view = $views_result->fetch_object( ) ) {
        print "<div class=\"$even\" id=\"$view->id\">\n";

        print "<div class=\"datetime\">" . date( 'n/j g:i a', strtotime( $view->datetime ) ) ."</div>\n";
        
        print "<div class=\"data\">\n";
        print "<span class=\"name\">"
            . "<a href=\"javascript:void(0)\" class=\"student\" data=\"$view->student\">" . ucwords( "$view->first $view->last" )
            . "</a></span> viewed ";
        $page = preg_replace( '|/~[^/]+/(.*)|', "$1", $view->page );
        print "<span class=\"page\">"
            . "<a href=\"javascript:void(0)\" class=\"page\" data=\"" . urlencode( $page ) ."\">$page</a>";
        if( preg_match( '/section=([0-9]+)/', $view->get_string, $matches ) ) {
            $section_query = 'select c.dept, c.course, s.section '
                . 'from courses as c, sections as s '
                . "where s.id = {$matches[ 1 ]} "
                . 'and s.course = c.id';
            $section_result = $db->query( $section_query );
            $section = $section_result->fetch_object();
            print " ($section->dept&nbsp;$section->course&nbsp;$section->section)";
        }
        print "</span>\n";
        
        /* Add a "More" link somehow (hoopefully somehow really cool) to show
         * referring page and IP address
         */

        print "</div>  <!-- div.data -->\n";
        print "</div> <!-- div.page_view#$view->id -->\n\n";
                 
        $even = ( $even == 'even' ? 'odd' : 'even' );
    }
}

?>

<script type="text/javascript">
$(document).ready(function(){
    $('a.student').click(function(){
        var student_name = $(this).html();
        var student_id = $(this).attr('data');
        $.post('list_page_views.php',
            { student: student_id },
            function(data){
                $('div#page_views').html(data).attr('student',student_id).attr('page_name',0);
                $('h2#page_views_description').html(student_name + " (<a href=\"javascript:void(0)\" class=\"reset\">Reset</a>)");
                $('a.reset').click(function(){
                    $.post('list_page_views.php',
                        function(data){
                            $('div#page_views').html(data);
                            $('h2#page_views_description').html('All Student Data');
                        }
                    )
                })
            }
        )
    })
    
    $('a.page').click(function(){
        var page = $(this).html();
        var page_name = $(this).attr('data');
        
        console.log( 'page=' + page + ' page_name=' + page_name );
        
        $.post('list_page_views.php',
            { page_name: page_name },
            function(data){
                $('div#page_views').html(data).attr('student',0).attr('page_name',page_name);
                $('h2#page_views_description').html(page + " (<a href=\"javascript:void(0)\" class=\"reset\">Reset</a>)");
                $('a.reset').click(function(){
                    $.post('list_page_views.php',
                        function(data){
                            $('div#page_views').html(data).attr('student',0).attr('page_name',0);
                            $('h2#page_views_description').html('All Student Data');
                        }
                    )
                })
            }
        )
    })
    
})
</script>