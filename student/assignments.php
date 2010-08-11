<?php

$title_stub = '';
require_once( '../_header.inc' );

if( $_SESSION[ 'student' ] > 0 ) {
    
    $type = 'homework';
    if( isset( $_POST[ 'type' ] ) ) {
        $type = $db->real_escape_string( $_POST[ 'type' ] );
    } else if( isset( $_GET[ 'type' ] ) ) {
        $type = $db->real_escape_string( $_GET[ 'type' ] );
    }
    
    $l_type = $type;
    $type = ucwords( str_replace( '_', ' ', $type ) );
    
    $type_id_query = 'select id from grade_types '
        . "where grade_type like \"%$type%\" or plural like \"%$type%\"";
    $type_id_result = $db->query( $type_id_query );
    $type_id_row = $type_id_result->fetch_assoc( );
    $type_id = $type_id_row[ 'id' ];
    
    $section_id = $db->real_escape_string( $_GET[ 'section' ] );
    $sections_query = 'select s.id, c.id as course_id, c.dept, c.course, '
	. 's.section '
        . 'from courses as c, sections as s, student_x_section as x '
        . "where x.student = {$_SESSION[ 'student' ]} "
        . "and x.section = $section_id "
        . 'and x.section = s.id '
        . 'and s.course = c.id '
        . 'and x.active = 1 '
        . 'order by c.dept, c.course';
    print "<pre>$sections_query;</pre>\n";
    $sections_result = $db->query( $sections_query );
    $section_row = $sections_result->fetch_assoc( );
    $section = $section_row[ 'dept' ] . ' ' . $section_row[ 'course' ]
	. ' ' . $section_row[ 'section' ];
    
    $sequence = 1;
    
    print "<h2>Past Assignments</h2>\n";
    
    $past_query = 'select id, grade_type, posted_date, due_date, '
        . 'title, description '
        . 'from assignments '
        . "where grade_type = $type_id "
        . "and section = $section_id "
        . 'and due_date < "' . date( 'Y-m-d H:i:s' ) . '" '
        . 'order by due_date';
    $past_result = $db->query( $past_query );
    if( $past_result->num_rows == 0 ) {
        print 'None.';
    } else {
        print "<div class=\"assignments\" id=\"past\">\n";
        while( $assignment = $past_result->fetch_assoc( ) ) {
            print "<h3><a href=\"#\">$type #{$sequence}</a></h3>\n";
            print "<div class=\"$l_type\" id=\"{$assignment[ 'id' ]}\">\n\n";

            if( isset( $assignment[ 'title' ] ) ) {
                print "<div class=\"title\">{$assignment[ 'title' ]}"
		    . "</div>\n\n";
            }
            
            print "<div class=\"due_date\">\n";
            print "Due \n"
                . date( 'l, F j \a\t g:i a',
			strtotime( $assignment[ 'due_date' ] ) )
                . "</div>\n\n";
            
            if( isset( $assignment[ 'description' ] ) ) {
                print "<h2>Assignment</h2>\n";
                print stripslashes( nl2br(  "<div class=\"description\">"
					    . "{$assignment[ 'description' ]}"
					    . "</div>\n\n" ) );
            }
            
            $docs_query = 'select * from assignment_documents '
                . "where assignment = {$assignment[ 'id' ]} "
                . 'order by name';
            $docs_result = $db->query( $docs_query );
            if( $docs_result->num_rows > 0 ) {
                print "<div class=\"documents\">\n";
                print "Related Files:\n";
                print "<ul>\n";
                while( $doc = $docs_result->fetch_assoc( ) ) {
                    print "<li>"
			. "<a href=\"$docroot/download_assignment_document.php"
			. "?id={$doc[ 'id' ]}\">"
                        . "{$doc[ 'name' ]} ({$doc[ 'size' ]} bytes)"
			. "</a></li>\n";
                }
                print "</ul></div>\n\n";
            }
            
            // Are these collected in this class?
            $collected_query = 'select w.collected '
                . 'from sections as s, grade_weights as w '
                . 'where w.course = s.course '
                . "and s.id = $section_id "
                . "and w.grade_type = {$assignment[ 'grade_type' ]}";
            $collected_result = $db->query( $collected_query );
            $collected_row = $collected_result->fetch_assoc( );
            $sub_row = 0;
            if( $collected_row[ 'collected' ] ) {
            
                print "<div class=\"submission_details\">\n";
                $sub_query = 'select id, time, submission '
		    . 'from assignment_submissions '
                    . "where assignment = {$assignment[ 'id' ]} "
                    . "and student = {$_SESSION[ 'student' ]}";

                $sub_result = $db->query( $sub_query );
                if( $sub_result->num_rows == 0 ) {
                    print 'You did not submit this assignment.';
                } else {
                    print "<h2>Your Submission</h2>\n";
                    $sub_row = $sub_result->fetch_assoc( );
                    print 'Your submission was accepted on '
                        . date( 'l, F j \a\t g:i a',
				strtotime( $sub_row[ 'time' ] ) )
                        . ":<br />\n";
                    if( trim( $sub_row[ 'submission' ] ) != '' ) {
                        print "<p class=\"submission\">"
                            . stripslashes( $sub_row[ 'submission' ] )
			    . "</p>\n";
                    }
                }
                print "</div>  <!-- div.submission_details -->\n\n";
            
                $grade_event_query = 'select id from grade_events '
                    . "where assignment = {$assignment[ 'id' ]}";
                $grade_event_result = $db->query( $grade_event_query );
                $grade_event_row = $grade_event_result->fetch_assoc( );
                $grade_event = $grade_event_row[ 'id' ];
                
                $grades_query = 'select * from grades '
                    . "where grade_event = $grade_event";
                $grades_result = $db->query( $grades_query );
                if( $grades_result->num_rows == 0 ) {
                    print 'Not graded yet.';
                } else {
                    $grade_query = 'select grade from grades '
                        . "where grade_event = $grade_event "
                        . "and student = {$_SESSION[ 'student' ]}";
                    $grade_result = $db->query( $grade_query );
                    if( $grade_result->num_rows == 1 ) {
                        $grade_row = $grade_result->fetch_assoc( );
                        $grade = $grade_row[ 'grade' ];
                    } else {
                        $grade = 0;
                    }
                    print "<h2>Your Grade</h2>\n";
                    print "<div class=\"grade\">$grade</div>";
                }
                
                // Comments

                print "<div class=\"comments\" "
		    . "id=\"{$sub_row[ 'sub_id' ]}\">\n";
                print "<h2>Comments</h2>\n";

                print "<div id=\"current_comments\">\n";                
                $comments_query = 'select * from submission_comments '
                    . "where `submission_id` = {$sub_row[ 'id' ]} "
                    . 'order by `when`';
                $comments_result = $db->query( $comments_query );
                if( $comments_result->num_rows == 0 ) {
                    print "None.\n";
                } else {
                    while( $comment_row = $comments_result->fetch_assoc( ) ) {
                        $whose_comment = $comment_row[ 'who' ] == 0 ?
			    'prof' :
			    'student';
                        print "<div class=\"{$whose_comment}_comment\" "
			    . "id=\"{$comment_row[ 'id' ]}\">";
                        print "<div class=\"who\">By "
                            . ( $whose_comment == 'prof' ?
				"Prof. {$prof[ 'last' ]}" :
				$_SESSION[ 'name' ] )
                            . "</div>\n";
                        print "<div class=\"when\">On "
                            . date( 'l, F j \a\t g:i a',
				    strtotime( $comment_row[ 'when' ] ) )
                            . "</div>\n";
                        print "<div class=\"comment\">"
                            . wordwrap
			    ( nl2br( stripslashes
				     ( $comment_row[ 'comment' ] ) ) )
                            . "</div>  "
			    . "<!-- div.{$whose_comment}_comment -->\n";
                        print "</div>\n";
                    }
                }
                print "</div>  <!-- div#current_comments -->\n";
                print "<p>Add a comment:</p>\n";
                print "<p><textarea class=\"comment\" "
		    . "id=\"{$sub_row[ 'id' ]}\" "
                    . "cols=\"40\" rows=\"5\">"
                    . "</textarea></p>\n";
                print "<input type=\"submit\" class=\"submit_comment\" "
                    . "value=\"Enter Comment\" "
		    . "id=\"{$sub_row[ 'id' ]}\" />\n";
                
                print "</div>  "
		    . "<!-- div.comments#{$sub_row[ 'student_id' ]} -->\n";

            } // if these assignments are collected

            print "</div>  <!-- div.$l_type#{$assignment[ 'id' ]} -->\n";
            $sequence++;
        }
        print "</div>  <!-- div#past -->\n";
    }

    print "<h2>Future Assignments</h2>\n";
    
    $future_query = 'select a.id, a.grade_type, a.posted_date, a.due_date, '
	. 'a.title, a.description '
        . 'from assignments as a, grade_types as g '
        . 'where g.grade_type = "Homework" '
        . 'and a.grade_type = g.id '
        . "and a.section = $section_id "
        . 'and a.due_date >= "' . date( 'Y-m-d H:i:s' ) . '" '
        . 'order by due_date';
    $future_result = $db->query( $future_query );
    if( $future_result->num_rows == 0 ) {
        print 'None.';
    } else {
        print "<div class=\"assignments\" id=\"future\">\n";
        while( $assignment = $future_result->fetch_assoc( ) ) {
            print "<h3><a href=\"#\">Homework #{$sequence}</a></h3>\n";
            print "<div class=\"homework\" id=\"{$assignment[ 'id' ]}\">\n\n";

            if( isset( $assignment[ 'title' ] ) ) {
                print "<div class=\"title\">{$assignment[ 'title' ]}"
		    . "</div>\n\n";
            }
            
            print "<div class=\"due_date\">\n";
            print "Due \n"
                . date( 'l, F j \a\t g:i a',
			strtotime( $assignment[ 'due_date' ] ) )
                . "</div>\n\n";
            
            if( isset( $assignment[ 'description' ] ) ) {
                print stripslashes( nl2br( "<div class=\"description\">"
					   . "Assignment: "
					   . "{$assignment[ 'description' ]}"
					   . "</div>\n\n" ) );
            }
            
            $docs_query = 'select * from assignment_documents '
                . "where assignment = {$assignment[ 'id' ]} "
                . 'order by name';
            $docs_result = $db->query( $docs_query );
            if( $docs_result->num_rows > 0 ) {
                print "<div class=\"documents\">\n";
                print "Related Files:\n";
                print "<ul>\n";
                while( $doc = $docs_result->fetch_assoc( ) ) {
                    print "<li>"
			. "<a href=\"$docroot/download_assignment_document.php"
			. "?id={$doc[ 'id' ]}\">"
                        . "{$doc[ 'name' ]} "
			. "({$doc[ 'size' ]} bytes)</a></li>\n";
                }
                print "</ul></div>\n\n";
            }
            
            // Are homework collected in this class?
            $collected_query = 'select w.collected '
                . 'from sections as s, grade_weights as w '
                . 'where w.course = s.course '
                . "and s.id = $section_id "
                . "and w.grade_type = {$assignment[ 'grade_type' ]}";
            $collected_result = $db->query( $collected_query );
            $collected_row = $collected_result->fetch_assoc( );
            if( $collected_row[ 'collected' ] ) {
                print "<div class=\"submission_details\">\n";
                $sub_query = 'select id, time, submission '
		    . 'from assignment_submissions '
                    . "where assignment = {$assignment[ 'id' ]} "
                    . "and student = {$_SESSION[ 'student' ]}";
                $sub_result = $db->query( $sub_query );
                if( $sub_result->num_rows == 0 ) {
                    print "<div class=\"submission\" "
			. "id=\"{$assignment[ 'id' ]}\">You did not "
                        . "submit this assignment.  Click here to submit now; "
			. "click away when you're done.</div>";
                } else {
                    $sub_row = $sub_result->fetch_assoc( );
                    print 'Your submission was accepted on '
                        . date( 'l, F j \a\t g:i a',
				strtotime( $sub_row[ 'time' ] ) )
                        . ".  Click on it to edit it; click away when done."
			. "<br />\n";
                    if( trim( $sub_row[ 'submission' ] ) != '' ) {
                        print "<div class=\"submission\" "
                            . "id=\"{$assignment[ 'id' ]}\">"
                            . stripslashes( $sub_row[ 'submission' ] )
                            . "</div>\n";
                    }
                }
                
                // Do something here about file uploads!
                
                print "</div> <!-- div.submission_details -->\n\n";
            } // if homework is collected in this class

            print "</div>  <!-- div.homework#{$assignment[ 'id' ]} -->\n";
            $sequence++;
        }
        print "</div>  <!-- div#future -->\n";
    }

    
?>

<script type="text/javascript">
$(document).ready(function(){
    var section_name = " <?php echo $type; ?> :: <?php echo $section; ?>";
    var section_id = "<?php echo $section_row[ 'id' ]; ?>";
    var student_id = "<?php echo $_SESSION[ 'student' ]; ?>";
    
    var passing_color = "<?php echo $passing_color; ?>";
    var failing_color = "<?php echo $failing_color; ?>";

    $('h1').html( $('h1').html( ) + section_name );
    $(document).attr('title', $(document).attr('title') + section_name );
    
    $('div.assignments').accordion({
        active: false,
        autoHeight: false,
        collapsible: true
    });
    
    $('div#future div.submission').editInPlace({
        url: 'submit_homework.php',
        params: 'ajax=yes',
        field_type: "textarea",
		textarea_rows: "5",
		textarea_cols: "40",
        saving_image: "<?php echo $docroot; ?>/images/ajax-loader.gif"
    })

    $('input.submit_comment').click(function(){
        var id = $(this).attr('id');
        var comment = $('textarea.comment[id=' + id + ']').val();
        $.post( 'add_comment.php',
            { comment: comment, submission: id },
            function(data){
                $('div#current_comments').html(data);
                $('textarea.comment[id=' + id + ']').val('');
            }
        )
    })
})
</script>

<?php
        
}
   
$lastmod = filemtime( $_SERVER[ 'SCRIPT_FILENAME' ] );
include( "$fileroot/_footer.inc" );

?>
