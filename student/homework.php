<?php

$title_stub = 'Homework';
require_once( '../_header.inc' );

if( $_SESSION[ 'student' ] > 0 ) {
    
    $section = $db->real_escape_string( $_GET[ 'section' ] );
    $sequence = 1;

    $section_name_query = 'select c.dept, c.course, s.section '
      . 'from courses as c, sections as s '
      . 'where s.course = c.id '
      . "and s.id = $section";
    // print "<pre>$section_name_query;</pre>\n";
    $section_name_result = $db->query( $section_name_query );
    $section_name_row = $section_name_result->fetch_object( );
    $section_name = "$section_name_row->dept $section_name_row->course $section_name_row->section";
    
    print "<h2>Past Assignments</h2>\n";
    
    $past_query = 'select a.id, a.grade_type, a.posted_date, a.due_date, '
        . 'a.title, a.description '
        . 'from assignments as a, grade_types as g '
        . 'where g.grade_type = "Homework" '
        . 'and a.grade_type = g.id '
        . "and a.section = $section "
        . 'and a.due_date < "' . date( 'Y-m-d H:i:s' ) . '" '
        . 'order by due_date';
    $past_result = $db->query( $past_query );
    if( $past_result->num_rows == 0 ) {
        print 'None.';
    } else {
        print "<div class=\"assignments\" id=\"past\">\n";
        while( $hw = $past_result->fetch_assoc( ) ) {
            print "<h3><a href=\"#\">Homework #{$sequence}</a></h3>\n";
            print "<div class=\"homework\" id=\"{$hw[ 'id' ]}\">\n\n";

            if( isset( $hw[ 'title' ] ) ) {
                print "<div class=\"title\">{$hw[ 'title' ]}</div>\n\n";
            }
            
            print "<div class=\"due_date\">\n";
            print "Due \n"
                . date( 'l, F j \a\t g:i a', strtotime( $hw[ 'due_date' ] ) )
                . "</div>\n\n";
            
            if( isset( $hw[ 'description' ] ) ) {
                print "<h2>Assignment</h2>\n";
                print stripslashes( nl2br(  "<div class=\"description\">{$hw[ 'description' ]}</div>\n\n" ) );
            }
            
            $docs_query = 'select * from assignment_documents '
                . "where assignment = {$hw[ 'id' ]} "
                . 'order by name';
            $docs_result = $db->query( $docs_query );
            if( $docs_result->num_rows > 0 ) {
                print "<div class=\"documents\">\n";
                print "Related Files:\n";
                print "<ul>\n";
                while( $doc = $docs_result->fetch_assoc( ) ) {
                    print "<li><a href=\"$docroot/download_assignment_document.php?id={$doc[ 'id' ]}\">"
                        . "{$doc[ 'name' ]} ({$doc[ 'size' ]} bytes)</a></li>\n";
                }
                print "</ul></div>\n\n";
            }
            
            // Are homework collected in this class?
            $collected_query = 'select w.collected '
                . 'from sections as s, grade_weights as w '
                . 'where w.course = s.course '
                . "and s.id = $section "
                . "and w.grade_type = {$hw[ 'grade_type' ]}";
            $collected_result = $db->query( $collected_query );
            $collected_row = $collected_result->fetch_assoc( );
            $sub_row = 0;
            if( $collected_row[ 'collected' ] ) {
            
                print "<div class=\"submission_details\">\n";
                $sub_query = 'select id, time, submission from assignment_submissions '
                    . "where assignment = {$hw[ 'id' ]} "
                    . "and student = {$_SESSION[ 'student' ]}";

                $sub_result = $db->query( $sub_query );
                if( $sub_result->num_rows == 0 ) {
                    print 'You did not submit this assignment.';
                } else {
                    print "<h2>Your Submission</h2>\n";
                    $sub_row = $sub_result->fetch_assoc( );
                    print 'Your submission was accepted on '
                        . date( 'l, F j \a\t g:i a', strtotime( $sub_row[ 'time' ] ) )
                        . ":<br />\n";
                    if( trim( $sub_row[ 'submission' ] ) != '' ) {
                        print "<p class=\"submission\">"
                            . stripslashes( $sub_row[ 'submission' ] ) . "</p>\n";
                    }
                }

				// Was a file upload required?
				
				$upload_required_query = 'select id, filename '
					. 'from assignment_upload_requirements '
				    . "where assignment = {$hw[ 'id' ]} "
					. 'order by filename';
				$upload_required_result = $db->query( $upload_required_query );
				if( $upload_required_result->num_rows > 0 ) {
					// Yes, upload(s) was/were required
					print "<div id=\"uploads\">\n";
					print "<h2>Your Uploaded Files</h2>\n";
					print "<ul>\n";
					while( $requirement = $upload_required_result->fetch_object( ) ) {
						print "<li>$requirement->filename: ";
						$upload_query = 'select id, filename, filesize, datetime '
							. 'from assignment_uploads '
							. "where assignment_upload_requirement = $requirement->id "
							. "and student = {$_SESSION[ 'student' ]} "
							. 'order by datetime desc '
							. 'limit 1';
						// print "<pre>$upload_query;</pre>\n";
						$upload_result = $db->query( $upload_query );
						if( $upload_result->num_rows == 0 ) {
							print 'You did not upload this file.';
						} else {
							$upload = $upload_result->fetch_object();
							print "You uploaded $upload->filename ($upload->filesize bytes) at "
								. date( 'g:i a \o\n l, F j', strtotime( $upload->datetime ) ) . ".  ";
							print_link( "download_homework_submission.php?id=$upload->id", 'Download this file now' );
							print '.';
						}
						print "</li>\n";
					}
					print "</ul>\n";
					print "</div>\n";
				}

                print "</div>  <!-- div.submission_details -->\n\n";
            
                $grade_event_query = 'select id from grade_events '
                    . "where assignment = {$hw[ 'id' ]}";
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

                print "<div class=\"comments\" id=\"{$sub_row[ 'sub_id' ]}\">\n";
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
                        $whose_comment = $comment_row[ 'who' ] == 0 ? 'prof' : 'student';
                        print "<div class=\"{$whose_comment}_comment\" id=\"{$comment_row[ 'id' ]}\">";
                        print "<div class=\"who\">By "
                            . ( $whose_comment == 'prof' ? "Prof. {$prof[ 'last' ]}" : $_SESSION[ 'name' ] )
                            . "</div>\n";
                        print "<div class=\"when\">On "
                            . date( 'l, F j \a\t g:i a', strtotime( $comment_row[ 'when' ] ) )
                            . "</div>\n";
                        print "<div class=\"comment\">"
                            . wordwrap( nl2br( stripslashes( $comment_row[ 'comment' ] ) ) )
                            . "</div>  <!-- div.{$whose_comment}_comment -->\n";
                        print "</div>\n";
                    }
                }
                print "</div>  <!-- div#current_comments -->\n";
                print "<p>Add a comment:</p>\n";
                print "<p><textarea class=\"comment\" id=\"{$sub_row[ 'id' ]}\" "
                    . "cols=\"40\" rows=\"5\">"
                    . "</textarea></p>\n";
                print "<input type=\"submit\" class=\"submit_comment\" "
                    . "value=\"Enter Comment\" id=\"{$sub_row[ 'id' ]}\" />\n";
                
                print "</div>  <!-- div.comments#{$sub_row[ 'student_id' ]} -->\n";

            } // if homeworks are collected

            print "</div>  <!-- div.homework#{$hw[ 'id' ]} -->\n";
            $sequence++;
        }
        print "</div>  <!-- div#past -->\n";
    }

    print "<h2>Future Assignments</h2>\n";
    
    $future_query = 'select a.id, a.grade_type, a.posted_date, a.due_date, a.title, a.description '
        . 'from assignments as a, grade_types as g '
        . 'where g.grade_type = "Homework" '
        . 'and a.grade_type = g.id '
        . "and a.section = $section "
        . 'and a.due_date >= "' . date( 'Y-m-d H:i:s' ) . '" '
        . 'order by due_date';
    $future_result = $db->query( $future_query );
    if( $future_result->num_rows == 0 ) {
        print 'None.';
    } else {
        print "<div class=\"assignments\" id=\"future\">\n";
        while( $hw = $future_result->fetch_assoc( ) ) {
            print "<h3><a href=\"#\">Homework #{$sequence}</a></h3>\n";
            print "<div class=\"homework\" id=\"{$hw[ 'id' ]}\">\n\n";

            if( isset( $hw[ 'title' ] ) ) {
                print "<div class=\"title\">{$hw[ 'title' ]}</div>\n\n";
            }
            
            print "<div class=\"due_date\">\n";
            print "Due \n"
                . date( 'l, F j \a\t g:i a', strtotime( $hw[ 'due_date' ] ) )
                . "</div>\n\n";
            
            if( isset( $hw[ 'description' ] ) ) {
                print stripslashes( nl2br( "<div class=\"description\">Assignment: {$hw[ 'description' ]}</div>\n\n" ) );
            }
            
            $docs_query = 'select * from assignment_documents '
                . "where assignment = {$hw[ 'id' ]} "
                . 'order by name';
            $docs_result = $db->query( $docs_query );
            if( $docs_result->num_rows > 0 ) {
                print "<div class=\"documents\">\n";
                print "Related Files:\n";
                print "<ul>\n";
                while( $doc = $docs_result->fetch_assoc( ) ) {
                    print "<li><a href=\"$docroot/download_assignment_document.php?id={$doc[ 'id' ]}\">"
                        . "{$doc[ 'name' ]} ({$doc[ 'size' ]} bytes)</a></li>\n";
                }
                print "</ul></div>\n\n";
            }
            
            // Are homework collected in this class?
            $collected_query = 'select w.collected '
                . 'from sections as s, grade_weights as w '
                . 'where w.course = s.course '
                . "and s.id = $section "
                . "and w.grade_type = {$hw[ 'grade_type' ]}";
            $collected_result = $db->query( $collected_query );
            $collected_row = $collected_result->fetch_assoc( );
            if( $collected_row[ 'collected' ] ) {
                print "<div class=\"submission_details\">\n";
                $sub_query = 'select id, time, submission from assignment_submissions '
                    . "where assignment = {$hw[ 'id' ]} "
                    . "and student = {$_SESSION[ 'student' ]}";
                $sub_result = $db->query( $sub_query );
                if( $sub_result->num_rows == 0 ) {
                    print "<div class=\"submission\" id=\"{$hw[ 'id' ]}\">You did not "
                        . "submit this assignment.  Click here to submit now; click away when you're done.</div>";
                } else {
                    $sub_row = $sub_result->fetch_assoc( );
                    print 'Your submission was accepted on '
                        . date( 'l, F j \a\t g:i a', strtotime( $sub_row[ 'time' ] ) )
                        . ".  Click on it to edit it; click away when done.<br />\n";
                    if( trim( $sub_row[ 'submission' ] ) != '' ) {
                        print "<div class=\"submission\" "
                            . "id=\"{$hw[ 'id' ]}\">"
                            . stripslashes( $sub_row[ 'submission' ] )
                            . "</div>\n";
                    }
                }
                                
                print "</div> <!-- div.submission_details -->\n\n";

		// If a file upload is desired/required, ask for it here

		$upload_query = 'select * '
		    . 'from assignment_upload_requirements '
		    . "where assignment = {$hw[ 'id' ]} "
		    . 'order by filename';
		$upload_result = $db->query( $upload_query );
		$amount = $upload_result->num_rows;
		if( $amount > 0 ) {
		    print '<p>Please upload the following ';
		    if( $amount > 1 ) {
			print $amount . ' ';
		    }
		    print 'file';
		    if( $amount > 1 ) {
			print 's';
		    }
		    print ":</p>\n";
		    print "<ul>\n";
		    while( $upload_row = $upload_result->fetch_assoc( ) ) {
			print "<li class=\"requirement\">{$upload_row[ 'filename' ]}: ";
                        print "<span class=\"upload_details\" "
                            . "id=\"upload_details{$upload_row[ 'id' ]}\">\n";

                        $files_query = 'select id, filename, filesize, datetime '
			    . 'from assignment_uploads '
                            . "where assignment_upload_requirement = {$upload_row[ 'id' ]} "
                            . "and student = {$_SESSION[ 'student' ]} "
			    . "order by datetime desc limit 1";
                        $files_result = $db->query( $files_query );
                        if( $files_result->num_rows == 0 ) {
                            print 'You have not uploaded this file.';
                        } else {
			    $file_row = $files_result->fetch_object( );
			    print "You uploaded $file_row->filename "
				. "($file_row->filesize bytes) on "
				. date( 'l, F j, Y \a\t g:i a',
					strtotime( $file_row->datetime ) )
				. ".";
			    print_link( "download_homework_submission.php?id=$file_row->id", 'Download this file now' );
			}
                            
                        print "</span>  <!-- span.upload_details#upload_details{$upload_row['id']} -->\n";

			print "<div class=\"upload_container\" "
			    . "id=\"{$upload_row[ 'id' ]}\">\n";
			print "<div id=\"fileUpload{$upload_row[ 'id' ]}\"></div> "
			    . "<!-- fileUpload{$upload_row[ 'id' ]} -->\n";

			print "</div>  <!-- "
			    . "upload_container#{$upload_row[ 'id' ]} -->\n";
			print "</li>\n";
		    }
		    print "</ul>\n";

		} // if file uploads are required

            } // if homework is collected in this class

            print "</div>  <!-- div.homework#{$hw[ 'id' ]} -->\n";
            $sequence++;
        }
        print "</div>  <!-- div#future -->\n";
    }

    
?>

<script type="text/javascript">
$(document).ready(function(){

    document.title = document.title + " :: <?php echo $section_name; ?>";
    $("h1").html( $("h1").html() + " for <?php echo $section_name; ?>" );

    var student = "<?php echo $_SESSION[ 'student' ];?>";

    $('div.assignments').accordion({
        active: false,
        autoHeight: false,
        collapsible: true,
		/*
		changestart: function( event, ui ) {
			var active = $('div.accordion').accordion( 'option', 'active' );
			alert( active );
		}
		*/
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


    $('div.upload_container').each(function(){
        var id = $(this).attr('id');

	$('div#fileUpload' + id).uploadify({
	    'uploader': '../uploadify/uploadify.swf',
	    'script': './assignment_document_upload.php',
            'cancelImg': '../uploadify/cancel.png',
            'auto': true,
            'folder': './uploads',
            'buttonText': 'Browse',
            'wmode': 'transparent',
            'sizeLimit': '5000000',
            'scriptData': {
                'requirement': id,
                'student': student
            },
            'fileDataName': 'file',
            'onComplete': function(event, queueID, fileObj, response, data){
                $('span.upload_details[id=upload_details' + id + ']')
		    .fadeOut().html(response).fadeIn();
                $.pnotify({
                    pnotify_title: 'File Uploaded',
                    pnotify_text: 'Your file ' + fileObj.name + ' has been uploaded.',
                    pnotify_shadow: true
		})
            },

	    /* remove this later */
	    /*
	    'onOpen': function(event,ID,fileObj){
		alert(event + ' ' + ID + ' ' + fileObj.name );
	    },
	    */
	    /* remove that later */

            'onError': function( a, b, c, d ){
                if( d.info == 404 )
                    alert( 'Can not find upload script' );
                else
                    alert( 'error ' + d.type + ": " + d.info );
            }
        })
    })
	
})
</script>

<?php
        
} else {
    print $no_student;
}
   
$lastmod = filemtime( $_SERVER[ 'SCRIPT_FILENAME' ] );
include( "$fileroot/_footer.inc" );

?>
