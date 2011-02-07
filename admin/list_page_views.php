<?php

$no_header = 1;
require_once( '../_header.inc' );

if( $_SESSION[ 'admin' ] == 1 ) {
    
    $start = isset( $_POST[ 'start' ] )
	   ? $db->real_escape_string( $_POST[ 'start' ] )
	   : 0;
    $amount = isset( $_POST[ 'amount' ] )
	   ? $db->real_escape_string( $_POST[ 'amount' ] )
	   : 25;

    //unset( $post_page );

    $page_view_query = 'select p.id, p.page, p.get_string, p.datetime, '
    	. 'p.referrer, p.ip, '
    	. 's.id as student_id, s.first, s.middle, s.last '
    	. 'from page_views as p, students as s '
    	. 'where p.student = s.id ';
    if( isset( $_POST[ 'student' ] ) and $_POST[ 'student' ] != 0 ) {
        $student_id = $db->real_escape_string( $_POST[ 'student' ] );
    	$page_view_query .= "and s.id = $student_id ";

        $student_query = 'select first, middle, last from students '
            . "where id = $student_id";
        $student_result = $db->query( $student_query );
        $student_row = $student_result->fetch_assoc( );
        $student_name = name( $student_row );

    } else if( isset( $_POST[ 'page' ] ) ) {
        $post_page = $db->real_escape_string( $_POST[ 'page' ] );
    	$page_view_query .= "and page like \"%$post_page\" ";
    }

    $page_view_query .= 'order by p.datetime desc, s.last, s.first, '
    	. 's.middle '
    	. "limit $start, $amount";
    
    print "<div id=\"clear_link\" style=\"padding: 1em; text-align: center;\"></div>\n";
        
    $page_view_result = $db->query( $page_view_query );
    $row_number = $start;
    print "<div class=\"twenty_five_rows\" style=\"display: none\">\n";
    while( $row = $page_view_result->fetch_assoc( ) ) {
    	unset( $section );
    	$page = preg_replace( '|^/~[A-Za-z0-9]+/(.*)|',
    			      "$1", $row[ 'page' ] );
    	if( preg_match( '/section=([0-9]+)/', $row[ 'get_string' ],
    			$matches ) ) {
    	    $section_query = 'select c.dept, c.course, s.section '
        		. 'from courses as c, sections as s '
        		. 'where s.course = c.id '
        		. "and s.id = {$matches[ 1 ]}";
    	    $section_result = $db->query( $section_query );
    	    $section_row = $section_result->fetch_assoc( );
    	    $section = $section_row[ 'dept' ] . ' '
    		  . $section_row[ 'course' ] . ' '
    		  . $section_row[ 'section' ];
    	} else if( preg_match( '/slug=(.*)/', $row[ 'get_string' ],
    			       $matches ) ) {
    	    $section = ucwords( preg_replace( '/_/', ' ', $matches[ 1 ] ) );
    	} else if( isset( $row[ 'get_string' ] ) ) {
    	    $section = $row[ 'get_string' ];
    	}
    	$name = ucwords( $row[ 'last' ] ) . ', '
    	    . strtoupper( substr( $row[ 'first' ], 0, 1 ) ) . '.';
    	$full_name = name( $row );
    
    	print "<div id=\"$row_number\" class=\"page_view\">\n";
    	print "  <span class=\"time\" style=\"font-size: 0.8em;\">"
    	    . date( 'D n/d H:i', strtotime( $row[ 'datetime' ] ) )
    	    . "</span>\n";
    	print "  <span class=\"student\" id=\"{$row[ 'student_id' ]}\" "
    	    . "style=\"font-weight: normal\">\n";
    	print "<a href=\"javascript:void(0)\" class=\"student\" "
    	    . "id=\"{$row[ 'student_id' ]}\">$full_name</a>\n";
    	print "</span>\n";
    
    	print "<div class=\"page_details\" "
    	    . "style=\"padding-left: 2em; font-size: 0.8em;\">"
    	    . "Viewed \n";
    	print "<a href=\"javascript:void(0)\" class=\"page\" "
    	    . "id=\"" . urlencode( $page ) . "\">$page</a>\n";
    	if( $section != '' ) 
    	    print "($section)\n";
    	print "<span class=\"ip\">from {$row[ 'ip' ]}</span>\n";
    	print "(<a href=\"javascript:void(0)\" class=\"more\" "
    	    . "id=\"$row_number\">More</a>)";
    	print "</div> <!-- div.page_details -->\n";
    
    	print "<div class=\"referrer\" id=\"$row_number\" "
    	    . "style=\"padding-left: 2em; font-size: 0.8em; display: none\">";
    	print "Refered from {$row[ 'referrer' ]}";
    	print "</div> <!-- div.referrer -->\n";
    
    	print "</div> <!-- div.page_view#$row_number -->\n\n";
    
    	++$row_number;
    }
    print "</div> <!-- twenty_five_rows -->\n";

    $remaining_view_query = 'select p.id '
        . 'from page_views as p, students as s '
        . 'where p.student = s.id ';
    if( isset( $_POST[ 'student' ] ) and $_POST[ 'student' ] != 0 ) {
        $remaining_view_query .= "and s.id = $student_id ";
    } else if( isset( $_POST[ 'page' ] ) and $_POST[ 'page' ] != 0 ) {
        $remaining_view_query .= 'and page like "%'
           . $db->real_escape_string( $_POST[ 'page' ] )
           . '" ';
    }
    $next = $start + 25;
    $remaining_view_query .= "limit $next, $amount";
    $remaining_view_result = $db->query( $remaining_view_query );
    $remaining_view_count = $remaining_view_result->num_rows;
    if( $remaining_view_count > 0 ) {
        print "<p style=\"text-align: center\">\n";
        print "<button id=\"more\">Load Next "
            . ( $remaining_view_count >= 25 ? '25' : $remaining_view_count )
            . "</button>\n";
        print "</p>\n";
    }
?>

<script type="text/javascript">

$(document).ready(function(){

    var start = <?php echo $start; ?>;
    var clear = "<a href=\"javascript:void(0)\" id=\"clear\">Clear Filter</a>";
    var orig_h1 = $('h1').html();
    
    var student = "<?php echo isset( $student_name ) ? $student_name : ''; ?>";
    if( student != '' && start == 0 ) {
        var name = " :: " + student;
        $('h1').html( $('h1').html( ) + name );
        $(document).attr('title', $(document).attr('title') + name );
    }

    var post_page = "<?php echo isset( $post_page ) ? $post_page : ''; ?>";
    if( post_page != '' && start == 0 ) {
        $('h1').html( $('h1').html( ) + ' :: ' + post_page );
        $(document).attr('title', $(document).attr('title') + ' :: ' + post_page );
    }

    $('a.student').click(function(){
        var id = $(this).attr('id');
        $.post('list_page_views.php',
            { student: id },
            function(data){
                $('div#page_views').html(data);
                $('div.twenty_five_rows:first').show();
                $('div#clear_link').html(clear).slideDown();
            }
        );
    })
    
    $('a.page').click(function(){
        var page = $(this).html();
        $.post('list_page_views.php',
            { page: page },
            function(data){
                $('div#page_views').html(data);
                $('div.twenty_five_rows:first').show();
                $('div#clear_link').html(clear).slideDown();
            }
        )
    })
    
    $('a#clear').click(function(){
        $.post('list_page_views.php',
            function(data){
                $('div#page_views').html(data);
                $('div.twenty_five_rows:first').show();
                $('div#clear_link').slideUp().html('');
                $('h1').html(orig_h1);
                $(document).attr('title',orig_h1);
            }
        )
    })

    $('button#more').click(function(){
        var last_row = $('div#page_views div.page_view:last').attr('id');
        var next_row = ( last_row * 1 ) + 1;
        var student = <?php echo isset( $student_id ) ? $student_id : 0; ?>;
        var page = "<?php echo isset( $post_page ) ? $post_page : 0; ?>";
        
        $(this).hide();
        
        $.post( 'list_page_views.php',
            { start: next_row, end: 25, student: student, page: page },
            function(data){
                $('div#page_views').append(data);
                $('div.twenty_five_rows:last').slideDown(1500);
            }
        );
    });
})

</script>

<?php
}

?>
