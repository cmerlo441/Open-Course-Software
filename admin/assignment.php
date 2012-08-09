<?php

$title_stub = 'Assignment Details';
require_once( '../_header.inc' );

if( $_SESSION[ 'admin' ] == 1 ) {
    
    $assignment = $db->real_escape_string( $_GET[ 'assignment' ] );
    $assignment_query = 'select * from assignments '
        . 'where id = ' . $assignment;
    $assignment_result = $db->query( $assignment_query );
    $assignment_row = $assignment_result->fetch_assoc( );
    
    // What class is this?
    
    $course_query = 'select c.id as course_id, c.dept, c.course, s.section, s.id as sid '
        . 'from courses as c, sections as s '
        . 'where s.course = c.id '
        . "and s.id = {$assignment_row[ 'section' ]}";
    $course_result = $db->query( $course_query );
    $course_row = $course_result->fetch_assoc( );
    $course_name = $course_row[ 'dept' ] . ' ' . $course_row[ 'course' ] . ' '
        . $course_row[ 'section' ];
    
    // What kind of assignment is this?
    
    $type_query = 'select grade_type from grade_types '
        . "where id = {$assignment_row[ 'grade_type' ]}";
    $type_result = $db->query( $type_query );
    $type_row = $type_result->fetch_assoc( );
    $type = $type_row[ 'grade_type' ];

    // How many have there been?
    $count_query = 'select count( id ) as count from assignments '
        . "where section = {$assignment_row[ 'section' ]} "
        . "and grade_type = {$assignment_row[ 'grade_type' ]}";
    $count_result = $db->query( $count_query );
    $count_row = $count_result->fetch_assoc( );
    $count = $count_row[ 'count' ];
    
    // Which # assignment is this?
        
    $sequence_query = 'select count( id ) as amount from assignments '
        . "where section = {$assignment_row[ 'section' ]} "
        . "and grade_type = {$assignment_row[ 'grade_type' ]} "
        . "and due_date <= \"{$assignment_row[ 'due_date' ]}\"";
    $sequence_result = $db->query( $sequence_query );
    $sequence_row = $sequence_result->fetch_assoc( );
    $sequence = $sequence_row[ 'amount' ];
    
    $assignment_string = $type;
    if( $count > 1 ) {
        $assignment_string .= " #{$sequence}";
    }
    
    print "<h2>$assignment_string</h2>\n";

    print "Due <input type=\"text\" id=\"due_date\" value=\""
        . date( 'l, M j, Y', strtotime( $assignment_row[ 'due_date' ] ) )
        . '"></input> at <input type=\"text\" id="due_time" value="'
        . date( 'g:i a', strtotime( $assignment_row[ 'due_date' ] ) )
        . "\"></input>\n";
    print "<input type=\"submit\" id=\"change_time\"value=\"Update Due Date\" />\n";
    
    print "<h3>Assignment Details</h3>\n";
    print "<div id=\"assignment\">\n";
    if( $assignment_row[ 'title' ] == '' )
	   $title = '(No title)';
    else
	   $title = nl2br( $assignment_row[ 'title' ] );
    print "<div class=\"editInPlace\" id=\"title\">$title</div>\n";
    print "<div class=\"editInPlace\" id=\"description\">\n";
    print stripslashes( nl2br( "{$assignment_row[ 'description' ]}</div>\n" ) );
    print "</div>  <!-- div#assignment -->\n";
    
    // Has the due date passed?
    $due_date_passed = date( 'Y-m-d H:i:s' ) > $assignment_row[ 'due_date' ];

    if( ! $due_date_passed ) {
    	print "<h3>File Upload Requirements</h3>\n";
    	print "<div id=\"upload_requirements\">\n";
    	print "</div>  <!-- div#upload_requirements -->\n";
    }

    print "<div id=\"files\">\n";
    print "<h3>Downloadable Files</h3>\n";
    print "<div id=\"existing_files\">\n";
    print "</div>  <!-- div#existing_files -->\n";
    
    print "<div id=\"file_upload\">\n";
    print "Upload a file:  <div id=\"fileUpload\"></div>\n";
    print "</div>  <!-- div#file_upload -->\n";
    
    print "</div>  <!-- div#files -->\n";
    
    $event_query = 'select * from grade_events '
        . "where assignment = $assignment";
    $event_result = $db->query( $event_query );
    $event_row = $event_result->fetch_assoc( );
    $event = $event_row[ 'id' ];
                
    print "<div id=\"stats\"></div>\n";

    print "<h3>Curve</h3>\n";
    print "<div id=\"curve\">\n";
    print "</div>  <!-- div#curve -->\n";
    
    // Do these get collected?
    
    $collected_query = 'select w.collected '
        . 'from grade_weights as w, assignments as a, sections as s '
        . "where a.id = $assignment "
        . 'and a.grade_type = w.grade_type '
        . 'and a.section = s.id '
        . 'and s.course = w.course';
    $collected_result = $db->query( $collected_query );
    $collected_row = $collected_result->fetch_assoc( );
    $collected = $collected_row[ 'collected' ];
    
    if( $collected ) {

        print "<h3>Submission Details</h3>\n";        
        // Is this a project?
        
        if( $type == "Project" ) {
            
            // Get each student who submitted
            
            $student_query = 'select s.first, s.middle, s.last, u.student '
                . 'from assignment_uploads as u, students as s '
                . 'where u.student = s.id '
                . "and u.assignment_upload_requirement in "
        		. "( select id from assignment_upload_requirements "
        		. "where assignment = {$assignment_row[ 'id' ]} ) "
                . 'group by s.id '
                . 'order by s.last, s.first, s.middle';
            $student_result = $db->query( $student_query );
            if( $student_result->num_rows == 0 ) {
                print 'No submissions.';
            } else {
                print "<div class=\"students\">\n";

?>

<table class="tablesorter" id="submissions_table">
    <thead>
        <tr>
            <th>Student</th>
            <th>Submitted</th>
            <th>Grade</th>
        </tr>
    </thead>
    
    <tbody>
<?php

                while( $student = $student_result->fetch_assoc( ) ) {
                    print "        <tr id=\"{$student[ 'student' ]}\">\n";
                    
		    $last_submit_query = 'select u.datetime '
		    . 'from assignment_upload_requirements as r, '
		    . 'assignment_uploads as u '
		    . 'where u.assignment_upload_requirement = r.id '
		    . "and u.student = {$student[ 'student' ]} "
		    . "and r.assignment = {$assignment_row[ 'id' ]} "
		    . 'order by datetime desc limit 1';

                    $last_submit_result = $db->query( $last_submit_query );
                    $last_submit_row = $last_submit_result->fetch_assoc( );
                    $last_submit = $last_submit_row[ 'datetime' ];

                    $name = lastfirst( $student );

                    $submit_date = date( 'm/d H:i', strtotime( $last_submit ) );

                    $lateness = '';
                    if( $last_submit > $assignment_row[ 'due_date' ] ) {
                        $diff = strtotime( $last_submit ) - strtotime( $assignment_row[ 'due_date' ] );
                        $seconds_in_a_day = 60 * 60 * 24;
                        $days_late = ceil( $diff / $seconds_in_a_day );
                        $lateness .= "<span class=\"late\">$days_late day"
                            . ( $days_late == 1 ? '' : 's' ) . " late</span>";
                    }
                    
                    print "            <td>";
                    print_link( "project_submission.php?"
                                . "student={$student[ 'student' ]}&assignment=$assignment",
                                $name );
                    print "</td>\n";
                    print "            <td style=\"text-align: center\">$submit_date";
                    if( $lateness != '' ) {
                        print "<br />$lateness";
                    }
                    print "</td>\n";
                    
                    // See if a grade has been posted
    
                    $grade_query = 'select * from grades '
                        . "where grade_event = $event "
                        . "and student = {$student[ 'student' ]}";
                    $grade_result = $db->query( $grade_query );
                    if( $grade_result->num_rows == 1 ) {
                        $grade_row = $grade_result->fetch_assoc( );
                        $grade = $grade_row[ 'grade' ];
                        $sum += $grade;
                    }
                    
                    print "            <td style=\"text-align: right\">"
                        . "<span class=\"grade\" id=\"{$student[ 'student' ]}\" size=\"4\" "
                        . "id=\"$assignment\">$grade</span></td>\n";
                    print "        </tr>\n";                        
                }
?>
    </tbody>
</table>

<?php                
                print "</div>  <!-- div.students -->\n";
            }
                
        } else {
        
            print "<div id=\"submissions\">\n";
            $submissions_query = 'select s.id as student_id, s.first, s.middle, s.last, '
                . 'sub.id as sub_id, sub.time, sub.submission '
                . 'from students as s, student_x_section as x, '
                . 'assignment_submissions as sub '
                . 'where x.student = s.id '
                . "and x.section = {$assignment_row[ 'section' ]} "
                . 'and ( x.status = ( select id from student_statuses where status = "Grade" ) '
                . 'or x.status = ( select id from student_statuses where status = "Audit" ) '
                . 'or x.status = ( select id from student_statuses where status = "INC" ) ) '
                . 'and sub.student = s.id '
                . "and sub.assignment = {$assignment_row[ 'id' ]} "
                . 'order by s.last, s.first, s.middle';
            $submissions_result = $db->query( $submissions_query );
            if( $submissions_result->num_rows == 0 ) {
                print 'No submissions.';
            } else {
                $count = 0;
                $sum = 0;
                while( $submission = $submissions_result->fetch_assoc( ) ) {
                    unset( $grade );
                    $name = ucwords( $submission[ 'first' ] ) . ' '
                        . ( ucwords( $submission[ 'middle' ] ) == '' ?
                            '' : ucwords( $submission[ 'middle' ] ) ) . ' '
                        . ucwords( $submission[ 'last' ] );
            
                    print "<h3><a href=\"#\">$name: "
                        . date( 'l, M j \a\t g:i a', strtotime( $submission[ 'time' ] ) )
                        . "</a></h3>\n";
                    print "<div class=\"submission\" id=\"{$submission[ 'sub_id' ]}\">\n";
                    print "<h2>Submission</h2>\n";
		    if( preg_match( "/<html>/i", $submission[ 'submission' ] ) == 0 ) {
			print "<p>" . wordwrap( nl2br( stripslashes( $submission[ 'submission' ] ) ) ) . "</p>\n";
		    } else {
			print "<pre>" . htmlentities( str_replace( "<br />", "", $submission[ 'submission' ] ) ) . "</pre>\n";
		    }

		    // See if any files were uploaded

		    $upload_r_query = 'select id, filename '
			. 'from assignment_upload_requirements '
			. "where assignment = {$assignment_row[ 'id' ]} "
			. 'order by filename';
		    $upload_r_result = $db->query( $upload_r_query );
		    if( $upload_r_result->num_rows > 0 ) {
			print "<h2>File Uploads</h2>\n";
			while( $req = $upload_r_result->fetch_assoc( ) ) {
			    print "<div class='upload' "
				. "style='border: 1px solid #5d562c; "
				. "padding: 0.5em 1em;'>\n";
			    print "<h3>{$req[ 'filename' ]}</h3>\n";
			    $upload_query = "select id, filename, filesize, "
				. 'filetype, datetime, file '
				. 'from assignment_uploads '
				. "where student "
				. "= {$submission[ 'student_id' ]} "
				. "and assignment_upload_requirement "
				. "= {$req[ 'id' ]} "
				. "order by datetime desc limit 1";
			    //print "<pre>$upload_query;</pre>\n";
			    $upload_result = $db->query( $upload_query );
			    if( $upload_result->num_rows == 0 )
				print 'No submission';
			    else {
				$row = $upload_result->fetch_assoc( );
				$ext = preg_replace( '/.*\.([^\.]+)$/', "$1",
						     $row[ 'filename' ] );
				if( $ext == 'zip' ) {
				    print_link( 'download_student_upload.php?'
						. "id={$row[ 'id' ]}",
						$row[ 'filename' ] );
				    print ' Uploaded '
					. date( 'l, F j g:i a',
						strtotime( $row[ 'datetime' ] ) );
				    print ".  {$row[ 'filesize' ]} bytes.";
				}
			    }
			    print "</div>  <!-- div.upload -->\n";
			}

		    }

                    
                    // See if a grade has been posted
    
                    $grade_query = 'select * from grades '
                        . "where grade_event = $event "
                        . "and student = {$submission[ 'student_id' ]}";
                    $grade_result = $db->query( $grade_query );
                    if( $grade_result->num_rows == 1 ) {
                        $grade_row = $grade_result->fetch_assoc( );
                        $grade = $grade_row[ 'grade' ];
                        $sum += $grade;
                    }
                    
		    print "<h2>Grade</h2>\n";
		    print "<div class=\"grade\">\n";
                    print "Grade: "
                        . "<span class=\"grade\" id=\"{$submission[ 'student_id' ]}\" size=\"4\" "
                        . "id=\"{$submission[ 'sub_id' ]}\">$grade</span>\n";
		    print "</div>  <!-- div.grade -->\n";
    
                    print "<div class=\"comments\" id=\"{$submission[ 'sub_id' ]}\">\n";
                    print "<h2>Comments</h2>\n";
    
                    print "<div class=\"current_comments\" id=\"{$submission[ 'sub_id' ]}\">\n";                
                    $comments_query = 'select * from submission_comments '
                        . "where `submission_id` = {$submission[ 'sub_id' ]} "
                        . 'order by `when`';
                    $comments_result = $db->query( $comments_query );
                    if( $comments_result->num_rows == 0 ) {
                        print "<div class=\"none\" id=\"{$submission[ 'sub_id' ]}\">None.</div>\n";
                    } else {
                        while( $comment_row = $comments_result->fetch_assoc( ) ) {
                            $whose_comment = $comment_row[ 'who' ] == 0 ? 'prof' : 'student';
                            print "<div class=\"{$whose_comment}_comment\" id=\"{$comment_row[ 'id' ]}\">";
                            print "<div class=\"who\">By "
                                . ( $whose_comment == 'prof' ? "Prof. {$prof[ 'last' ]}" :$name )
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
                    print "</div>  <!-- div.current_comments -->\n";
                    print "<p>Add a comment:</p>\n";
                    print "<p><textarea class=\"comment\" id=\"{$submission[ 'sub_id' ]}\" "
                        . "cols=\"40\" rows=\"5\">"
                        . "</textarea></p>\n";
                    print "<input type=\"submit\" class=\"submit_comment\" "
                        . "value=\"Enter Comment\" id=\"{$submission[ 'sub_id' ]}\" />\n";
                    
                    print "</div>  <!-- div.comments#{$submission[ 'student_id' ]} -->\n";
                    
                    $count++;
                    print "</div>  <!-- div.submission#{$submission[ 'sub_id' ]} -->\n";
                }
            }
            
            print "</div> <!-- div#submissions -->\n";
        }
    }
    
    else {

        print "<h3>Grades</h3>\n";        
        print "<div id=\"grades_table\">\n";

        // These things aren't collected.  Just enter grades for everyone.
        $student_query = 'select s.id as student_id, s.first, s.middle, s.last, '
            . 'e.id as event_id '
            . 'from students as s, grade_events as e, student_x_section as x '
            . "where e.assignment = $assignment "
            . 'and e.section = x.section '
            . 'and x.student = s.id '
            . 'and ( x.status = ( select id from student_statuses where status = "Grade" ) '
            . 'or x.status = ( select id from student_statuses where status = "Audit" ) '
            . 'or x.status = ( select id from student_statuses where status = "INC" ) ) '
            . 'order by s.last, s.first, s.middle';
        $student_result = $db->query( $student_query );
        $count = 0;
        $sum = 0;
        
        print "<table class=\"tablesorter\" id=\"grades\">\n";
        print "<thead>\n";
        print "  <tr>\n";
        print "    <th>Student</th>\n";
        print "    <th>Grade</th>\n";
	print "    <th>Curved grade</td>\n";
        print "  </tr>\n";
        print "</thead>\n\n";
        
        print "<tbody>\n";
        
        while( $student = $student_result->fetch_assoc( ) ) {
            unset( $grade );
	    print "  <tr>\n    <td>" . lastfirst( $student ) . "</td>\n";
            
            // See if a grade has been posted
            $grade_query = 'select * from grades '
                . "where grade_event = {$student[ 'event_id' ]} "
                . "and student = {$student[ 'student_id' ]}";
            $grade_result = $db->query( $grade_query );
            if( $grade_result->num_rows == 1 ) {
                $grade_row = $grade_result->fetch_assoc( );
                $grade = $grade_row[ 'grade' ];
                $sum += $grade;
            }
            
            print "    <td class=\"grade\" id=\"{$student[ 'student_id' ]}\" "
	      . "align=\"right\">";
            print "<span class=\"grade\" "
                . "id=\"{$student[ 'student_id' ]}\" size=\"4\">$grade</span>";
            print "</td>\n";
            $count++;

	    // Is there a curve?

	    $curved_grade = ( isset( $grade ) ? $grade : '--' );
	    $curve_query = "select * from curves "
	      . "where grade_event = {$student[ 'event_id' ]}";
	    $curve_result = $db->query( $curve_query );
	    if( $curve_result->num_rows == 1 and isset( $grade ) ) {
	      $curve_row = $curve_result->fetch_assoc( );
	      if( $curve_row[ 'points' ] > 0 ) {
		$curved_grade += $curve_row[ 'points' ];
	      } else {
		$curved_grade = number_format( $grade * ( 1 + 0.01 * $curve_row[ 'percent' ] ) );
	      }
	    }
	    print "    <td class=\"curved\" id=\"{$student[ 'student_id' ]}\" "
	      . "align=\"right\">$curved_grade</td>\n";

            print "  </tr>\n\n";
        }
        print "</tbody>\n</table>\n";
        print "</div>  <!-- div#grades_table -->\n";
    }
    
    print "</div>  <!-- div#assignment -->\n";

?>

<script type="text/javascript">
$(document).ready(function(){
    var course = " :: <?php echo $course_name; ?>";
    var assignment = "<?php echo $_GET[ 'assignment' ]; ?>";
    var assignment_string = "<?php echo $assignment_string; ?>";
    
    $('h1').html( $('h1').html( ) + course );
    $(document).attr('title', $(document).attr('title') + course + ' :: ' +
        assignment_string );
    
    $('table#grades').tablesorter({
        sortList: [ [ 0, 0 ] ],
        widgets: [ 'ocsw' ]
    });
    
    $('table#submissions_table').tablesorter({
        sortList: [ [ 0, 0 ] ], widgets: [ 'ocsw' ]
    })
    
    $("input#due_date").datepicker( {dateFormat: 'yy-mm-dd'} ).change(function(){
        var assignment_type = "<?php echo $assignment_row[ 'grade_type' ]; ?>"
        var section = "<?php echo $course_row[ 'sid' ]; ?>";
        var date = $('#due_date').val();
        
        $.post( './calculate_due_time.php',
            { assignment_type: assignment_type, section: section, date: date },
            function( data ) {
                $('input#due_time').val( data );
            }
        )
    })
    
    $('input#change_time').click(function(){
        var assignment = <?php echo $assignment; ?>;
        var due_date = $('input#due_date').val();
        var due_time = $('input#due_time').val();
        
        // There's gotta be a better way to do this
        var days = [ 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday',
            'Saturday'];
        var months = [ 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
            'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec' ];
        
        $.post('update_assignment_due_date.php',
            { assignment: assignment, due_date: due_date, due_time: due_time },
            function(data){
                var date = new Date( Date.parse(data) );
                var date_string = days[date.getDay()] + ', ' +
                    months[date.getMonth( )] + ' ' + date.getDate() + ', ' +
                    date.getFullYear();
                $('input#due_date').val(date_string);
                
                var hour = date.getHours( );
                var ampm = hour < 12 ? "am" : "pm";
                if( hour == 0 )
                    hour = "12";
                else if( hour > 12 )
                    hour -= 12;
                
                $('input#due_time').val(hour + ':' + date.getMinutes( ) + ' ' + ampm );
                $.pnotify({
                    pnotify_title: 'Due Date Updated',
                    pnotify_text: 'The due date has been changed to ' + due_date +
                        ' at ' + $('input#due_time').val(),
                    pnotify_shadow: true
                })
            }
        )
    })
    
    $.post( 'list_assignment_documents.php',
        { assignment: assignment },
        function( data ) {
            $('div#existing_files').html(data);
        }
    )
    
    $('div#submissions').accordion({
        active: false,
        autoHeight: false,
        collapsible: true
    });
    
    $('div#grades').accordion({
        active: false,
        autoHeight: false,
        collapsible: true
    });
    
    $.post( 'assignment_statistics.php',
	    {
	        event: "<?php echo $event; ?>",
    		date: "<?php echo $assignment_row[ 'due_date' ]; ?>",
    		type: "<?php echo $type; ?>",
    		sequence: "<?php echo $sequence; ?>",
    		course_name: "<?php echo $course_name; ?>"
	    },
        function( data ) {
            $('div#stats').html(data);
        }
    )
    
    $('span.grade').editInPlace({
        url: 'update_grade.php',
        default_text: '(No grade recorded yet)',
        params: "ajax=yes&assignment_id=<?php echo $assignment; ?>",
        saving_image: "<?php echo $docroot; ?>/images/ajax-loader.gif"
    })
    
    $('input.submit_comment').click(function(){
        var id = $(this).attr('id');
        var comment = $('textarea.comment[id=' + id + ']').val();
        $.post( 'add_comment.php',
            { comment: comment, submission: id },
            function(data){
                if( $('div.none[id=' + id + ']:visible').length == 1 ) {
                	$('div.none[id=' + id + ']:visible').fadeOut('normal',
                        function(){
                		   $('div.current_comments[id=' + id + ']').html(data).children().fadeIn('normal');
                    	}
                	)
                } else {
                    $(data).insertAfter( $('div.current_comments[id=' + id + '] > div:last') ).fadeIn('normal');
                }
                $('textarea.comment[id=' + id + ']').val('');
            }
        )
    })

    $('div#title').editInPlace({
        url: 'update_assignment.php',
        params: 'ajax=yes&id=' + assignment + '&column=title',
        saving_image: "<?php echo $docroot; ?>/images/ajax-loader.gif"
    })
    
    $('div#description').editInPlace({
        url: 'update_assignment.php',
	field_type: 'textarea',
	textarea_rows: "10",
	textarea_columns: "80",
        params: 'ajax=yes&id=' + assignment + '&column=description',
        saving_image: "<?php echo $docroot; ?>/images/ajax-loader.gif"
    })
    
    $('#fileUpload').uploadify({
        'uploader': '../uploadify/uploadify.swf',
        'script': './assignment_document_upload.php',
        'cancelImg': '../uploadify/cancel.png',
        'auto': 'true',
        'folder': './uploads',
        'buttonText': 'Browse',
        'wmode': 'transparent',
        'sizeLimit': '500000',
        'scriptData': {'assignment': assignment},
        'fileDataName': 'file',
        'onComplete': function(a,b,c,d,e){
            $.post( 'list_assignment_documents.php',
                { assignment: assignment },
                function( data ) {
                    $('div#existing_files').html(data);
                }
            )
        },
        'onError': function( a, b, c, d ){
            if( d.info == 404 )
                alert( 'Can not find upload script' );
            else
                alert( 'error ' + d.type + ": " + d.info );
        }
    })
    
    $.post( 'assignment_upload_requirements.php',
        { assignment: assignment },
        function( data ) {
            $('div#upload_requirements').html(data);
        }
    )

    
    $.post( 'curve.php',
      {
	grade_event: "<?php echo $event; ?>"
      },
      function( data ) {
        $('div#curve').html(data);
      }
    )
    
})
</script>

<?php
} else {
    print $no_admin;
}

$lastmod = filemtime( $_SERVER[ 'SCRIPT_FILENAME' ] );
include( "$fileroot/_footer.inc" );

?>
