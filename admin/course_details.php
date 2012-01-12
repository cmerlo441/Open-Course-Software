<?php

$no_header = 1;
require_once( '../_header.inc' );

if( $_SESSION[ 'admin' ] == 1 ) {
	$course_id = $db->real_escape_string( $_REQUEST[ 'course_id' ] );
	
    $update_query = 'update courses set ';
    $count = 0;
    
    foreach( explode( ' ', 'dept course long_name short_name credits prereq catalog outline' ) as $field ) {
        if( isset( $_POST[ $field ] ) ) {
            if( $count > 0 )
                $update_query .= ', ';
            $update_query .= "$field = \"" . $db->real_escape_string( $_POST[ $field ] ) . "\"";
            $count ++;
        }
    }
    if( $count > 0 ) {
        $update_query .= " where id = $course_id";
        $db->query( $update_query );
    }
    
    if( isset( $_POST[ 'add_book' ] ) ) {
        $id = $db->real_escape_string( $_POST[ 'add_book' ] );
        $db->query( 'insert into course_x_textbook( id, course, textbook, required ) '
            . "values( null, $course_id, $id, 1 )" );
    }
    
    if( isset( $_POST[ 'delete_textbook' ] ) ) {
        $id = $db->real_escape_string( $_POST[ 'delete_textbook' ] );
        $db->query( "delete from course_x_textbook where id = $id" );
    }
    
	$course_query = "select * from courses where id = $course_id";
	$course_result = $db->query( $course_query );
	$course = $course_result->fetch_object( );
	
	print "<h2>$course->dept $course->course</h2>";
	
	print "<div id=\"items\">\n";
	print "<div>Department:<br />"
		. "<input class=\"enable_buttons\" type=\"text\" size=\"5\" id=\"dept\" value=\"$course->dept\" /></div>\n";

	print "<div>Course:<br />"
		. "<input class=\"enable_buttons\" type=\"text\" size=\"5\" id=\"course\" value=\"$course->course\" /></div>\n";

	print "<div>Long name:<br />"
		. "<input class=\"enable_buttons\" type=\"text\" size=\"50\" id=\"long_name\" value=\"$course->long_name\" /></div>\n";

	print "<div>Short name:<br />"
		. "<input class=\"enable_buttons\" type=\"text\" id=\"short_name\" value=\"$course->short_name\" /></div>\n";

	print "<div>Credits:<br />"
		. "<input class=\"enable_buttons\" type=\"text\" size=\"5\" id=\"credits\" value=\"$course->credits\" /></div>\n";

	print "<div>Prerequisite:<br />"
		. "<input class=\"enable_buttons\" type=\"text\" size=\"50\" id=\"prereq\" value=\"$course->prereq\" /></div>\n";

	print "<div>Catalog description: (<a href=\"javascript:void(0)\" id=\"catalog\">Show</a>)<br />"
		. "<textarea class=\"enable_buttons\" style=\"display: none;\" id=\"catalog\" rows=\"10\" cols=\"60\">$course->catalog</textarea></div>\n";

	print "<div>Course outline: (<a href=\"javascript:void(0)\" id=\"outline\">Show</a>)<br />"
		. "<div id=\"outlinediv\" style=\"display: none\">"
		. "<textarea class=\"enable_buttons\" id=\"outline\" rows=\"15\" cols=\"60\">$course->outline</textarea></div></div>\n";

	print "<div>Grade weights: (<a href=\"javascript:void(0)\" id=\"weights\">Show</a>)<br />"
		. "<div id=\"weightsdiv\" style=\"display:none\"></div></div>\n";

    print "<div>Textbooks:<br />";
    $textbooks_query = 'select t.id, t.title, t.edition, t.year, t.isbn, x.id as xid '
        . 'from textbooks as t, course_x_textbook as x '
        . "where x.course = $course_id "
        . 'and x.textbook = t.id '
        . 'order by t.title, t.edition, t.year';
    $textbooks_result = $db->query( $textbooks_query );
    print "<div id=\"textbook_list\">\n";
    if( $textbooks_result->num_rows == 0 ) {
        print 'There are no textbooks assigned to this course.';
    } else {
        print "<ul>\n";
        $books = array( );
        while( $textbook = $textbooks_result->fetch_object( ) ) {
            $books[ ] = $textbook->id;
            $author_query = 'select a.first, a.last '
                . 'from authors as a, textbook_x_author as x '
                . "where x.textbook = $textbook->id "
                . 'and x.author = a.id '
                . 'order by x.sequence';
            $author_result = $db->query( $author_query );
            if( $author_result->num_rows == 1 ) {
                $author = $author_result->fetch_object( );
                $author_string = "$author->first $author->last.  ";
            } else if( $author_result->num_rows == 2 ) {
                $author1 = $author_result->fetch_object( );
                $author2 = $author_result->fetch_object( );
                $author_string = "$author1->last and $author2->last.  ";
            }
            else {
                $author = $author_result->fetch_object( );
                $author_string = "$author->first $author->last et al.  ";
            }

            print "<li>";
            print "<a href=\"javascript:void(0)\" class=\"delete_textbook\" id=\"$textbook->xid\">";
            print "<img src=\"$docroot/images/silk_icons/cross.png\" height=\"16\" width=\"16\" "
                . "title=\"Remove $textbook->title From $course->dept $course->course\" /></a>\n";
            print "$textbook->title.  $author_string";
            print number_suffix( $textbook->edition ) . " edition, $textbook->year.</li>\n";
        }
        print "</ul>\n";
    }
    print "</div>\n";
    
    $all_textbooks_query = 'select id, title, edition, year '
        . 'from textbooks '
        . 'order by title, edition, year';
    $all_textbooks_result = $db->query( $all_textbooks_query );
    print "<select id=\"add_a_book\">\n";
    print "<option value=\"0\">Add a textbook to this class</option>\n";
    while( $book = $all_textbooks_result->fetch_object( ) ) {
        if( ! in_array( $book->id, $books ) ) {
            print "<option value=\"$book->id\">$book->title.  ";
            $author_query = 'select a.first, a.last '
                . 'from authors as a, textbook_x_author as x '
                . "where x.textbook = $book->id "
                . 'and x.author = a.id '
                . 'order by x.sequence';
            $author_result = $db->query( $author_query );
            if( $author_result->num_rows == 1 ) {
                $author = $author_result->fetch_object( );
                print "$author->first $author->last.  ";
            } else if( $author_result->num_rows == 2 ) {
                $author1 = $author_result->fetch_object( );
                $author2 = $author_result->fetch_object( );
                print "$author1->last and $author2->last.  ";
            }
            else {
                $author = $author_result->fetch_object( );
                print "$author->first $author->last et al.  ";
            }
            print number_suffix( $book->edition ) . " edition, $book->year.</option>\n";
        }
    }
    print "</select>\n";
    
    print "</div>\n";


	print "</div>  <!-- div#items -->\n";
	
	print "<div style=\"padding: 0.5em; background-color: #8f8b88; text-align: center;\">\n";
	print "<input type=\"submit\" disabled=\"disabled\" value=\"Save Changes\" id=\"save\"> "
		. "<input type=\"submit\" disabled=\"disabled\" value=\"Cancel Changes\" id=\"cancel\">"
		. "<input type=\"submit\" value=\"Delete This Course\" id=\"delete\" style=\"color: red\"></div>\n";
        
    print "<div id=\"delete_dialog\" title=\"Really Delete $course->dept $course->course?\" style=\"display: none\">\n";
    print "<p><span class=\"ui-icon ui-icon-alert\" style=\"float:left; margin:0 7px 20px 0;\"></span>\n";
    print "This class, and any current sections of it, will be permanently deleted!  Are you sure?</p>\n";
    print "</div>\n";
}

?>

<script type="text/javascript">
$(document).ready(function(){
	
	var course_id = "<?php echo $course_id; ?>";
	var course_name = "<?php echo $course->dept . ' ' . $course->course; ?>";
	
	var marked = 0;
	
	$('div#items > div').css('padding','0.5em');
	$('div#items > div:even').css('background-color','#464648');

	$('a#catalog').click(function(){
		$('textarea#catalog').slideToggle();
		var $text = $(this).html();
		if( $text == 'Show' )
			$(this).html( "Hide" );
		else
			$(this).html( "Show" );
	})
	
	$('a#outline').click(function(){
		$('div#outlinediv').slideToggle();
		if( marked == 0 ) {
			$("textarea#outline").markItUp(mySettings);
			marked = 1;
		}
		var $text = $(this).html();
		if( $text == 'Show' )
			$(this).html( "Hide" );
		else
			$(this).html( "Show" );
	})

	$('a#weights').click(function(){
		$('div#weightsdiv').slideToggle();
		
		if( $('div#weightsdiv').is(":visible") ) {
			var id = "<?php echo $course->id; ?>";
			$.post('list_grade_weights.php',
				{ course: id },
				function(data){
					$('div#weightsdiv').html(data);
				}
			)
		}
		var $text = $(this).html();
		if( $text == 'Show' )
			$(this).html( "Hide" );
		else
			$(this).html( "Show" );
	})
	
	$('input.enable_buttons').keyup( function( ) {
		$('input#save').removeAttr( 'disabled' );
		$('input#cancel').removeAttr( 'disabled' );
	})
	
	$('textarea.enable_buttons').keyup( function( ) {
		$('input#save').removeAttr( 'disabled' );
		$('input#cancel').removeAttr( 'disabled' );
	})
	
    var dept_orig = "<?php echo $course->dept; ?>";
    var course_orig = "<?php echo $course->course; ?>";
    var long_orig = "<?php echo $course->long_name; ?>";
    var short_orig = "<?php echo $course->short_name; ?>";
    var credits_orig = "<?php echo $course->credits; ?>";
    var prereq_orig = "<?php echo $course->prereq; ?>";
    var catalog_orig = "<?php urlencode( $course->catalog ); ?>";
    var outline_orig = "<?php urlencode( $course->outline ); ?>";

	$('input#save').click(function(){
		
		var update = new Array;
		update.push( { name: "course_id", value: course_id } );
		
		var dept = $('input#dept').val();
		if( dept != dept_orig )
			update.push( { name: "dept", value: dept } );
		
		var course = $('input#course').val();
		if( course != course_orig )
			update.push( { name: "course", value: course } );
		
		var long_name = $('input#long_name').val();
		if( long_name != long_orig )
			update.push( { name: "long_name", value: long_name } );
		
		var short_name = $('input#short_name').val();
		if( short_name != short_orig )
			update.push( { name: "short_name", value: short_name } );
			
		var credits = $('input#credits').val();
		if( credits != credits_orig )
			update.push( { name: "credits", value: credits } );
		
		var prereq = $('input#prereq').val();
		if( prereq != prereq_orig )
			update.push( { name: "prereq", value: prereq } );
		
		var catalog = $('textarea#catalog').val();
		if( catalog != catalog_orig )
			update.push( { name: "catalog", value: catalog } );
		
		var outline = $('textarea#outline').val();
		if( outline != outline_orig )
			update.push( { name: "outline", value: outline } );

		$.post( 'course_details.php', update,
			function(data){
				$('div#course_details').html(data);
			}
		)
	})
	
	$('input#cancel').click(function(){

        $('input#dept').val(dept_orig);
        $('input#course').val(course_orig);
        $('input#long_name').val(long_orig);
        $('input#short_name').val(short_orig);
        $('input#credits').val(credits_orig);
        $('input#prereq').val(prereq_orig);
        $('textarea#catalog').val(catalog_orig);
        $('textarea#outline').val(outline_orig);
        
        $('input#save').attr('disabled','disabled');
        $('input#cancel').attr('disabled','disabled');
	})
	
	$('input#delete').click(function(){
	    $('div#delete_dialog').dialog({
	        resizable: false,
	        height: 150,
	        width: 500,
	        modal: true,
	        buttons: {
	            "Delete This Course": function(){
	                $.post( 'courses_list.php',
	                   { delete: course_id },
	                   function(data){
	                       $('div#courses_list').html(data);
	                   }
	                )
	                $('div#delete_dialog').dialog('destroy');
	            },
	            "Keep This Course": function(){
                    $('div#delete_dialog').dialog('destroy');
	            }
	        }
	    })
	})
	
	$('select#add_a_book').change(function(){
        var book_id = $('select#add_a_book option:selected').val();
        $.post( 'course_details.php',
            { course_id: course_id, add_book: book_id },
            function(data){
                $('div#course_details').html(data);
            }
        )
	})
	
	$('a.delete_textbook').click(function(){
	    var id = $(this).attr('id');
	    $.post( 'course_details.php',
	       { course_id: course_id, delete_textbook: id },
	       function(data){
	           $('div#course_details').html(data);
	       }
	    )
	})
})
</script>
