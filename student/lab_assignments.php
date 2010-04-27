<?php

$title_stub = 'Lab Assignments';
require_once( '../_header.inc' );

if( $_SESSION[ 'student' ] > 0 ) {
    
    $section = $db->real_escape_string( $_GET[ 'section' ] );
    $sequence = 1;
    
    print "<h2>Past Assignments</h2>\n";
    
    $past_query = 'select a.id, a.grade_type, a.posted_date, a.due_date, '
        . 'a.title, a.description '
        . 'from assignments as a, grade_types as g '
        . 'where g.grade_type = "Lab Assignment" '
        . 'and a.grade_type = g.id '
        . "and a.section = $section "
        . 'and a.due_date < "' . date( 'Y-m-d H:i:s' ) . '" '
        . 'order by due_date';
    $past_result = $db->query( $past_query );
    if( $past_result->num_rows == 0 ) {
        print 'None.';
    } else {
        print "<div class=\"assignments\" id=\"past\">\n";
        while( $lab = $past_result->fetch_assoc( ) ) {
            print "<h3><a href=\"#\">Lab Assignment #{$sequence}</a></h3>\n";
            print "<div class=\"lab_assignment\" id=\"{$lab[ 'id' ]}\">\n\n";

            if( isset( $lab[ 'title' ] ) ) {
                print "<div class=\"title\">{$lab[ 'title' ]}</div>\n\n";
            }
            
            print "<div class=\"due_date\">\n";
            print "Due \n"
                . date( 'l, F j \a\t g:i a', strtotime( $lab[ 'due_date' ] ) )
                . "</div>\n\n";
            
            if( isset( $lab[ 'description' ] ) ) {
                print "<div class=\"description\">Assignment: {$lab[ 'description' ]}</div>\n\n";
            }
            
            $docs_query = 'select * from assignment_documents '
                . "where assignment = {$lab[ 'id' ]} "
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
            
            // Are lab assignments collected in this class?
            $collected_query = 'select w.collected '
                . 'from sections as s, grade_weights as w '
                . 'where w.course = s.course '
                . "and s.id = $section "
                . "and w.grade_type = {$lab[ 'grade_type' ]}";
            $collected_result = $db->query( $collected_query );
            $collected_row = $collected_result->fetch_assoc( );
            if( $collected_row[ 'collected' ] ) {
            
                print "<div class=\"submission_details\">\n";
                $sub_query = 'select id, time, submission from assignment_submissions '
                    . "where assignment = {$lab[ 'id' ]} "
                    . "and student = {$_SESSION[ 'student' ]}";
                $sub_result = $db->query( $sub_query );
                if( $sub_result->num_rows == 0 ) {
                    print 'You did not submit this assignment.';
                } else {
                    $sub_row = $sub_result->fetch_assoc( );
                    print 'Your submission was accepted on '
                        . date( 'l, F j \a\t g:i a', strtotime( $sub_row[ 'time' ] ) )
                        . ":<br />\n";
                    if( trim( $sub_row[ 'submission' ] ) != '' ) {
                        print "<p class=\"submission\">"
                            . stripslashes( $sub_row[ 'submission' ] ) . "</p>\n";
                    }
                }
                print "</div>  <!-- div.submission_details -->\n\n";
            
                $grade_event_query = 'select id from grade_events '
                    . "where assignment = {$lab[ 'id' ]}";
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
                    print "<div class=\"grade\">Your grade: $grade</div>";
                }
            } // if homeworks are collected

            print "</div>  <!-- div.lab_assignment#{$lab[ 'id' ]} -->\n";
            $sequence++;
        }
        print "</div>  <!-- div#past -->\n";
    }

    print "<h2>Future Assignments</h2>\n";
    
    $future_query = 'select a.id, a.grade_type, a.posted_date, a.due_date, a.title, a.description '
        . 'from assignments as a, grade_types as g '
        . 'where g.grade_type = "Lab Assignment" '
        . 'and a.grade_type = g.id '
        . "and a.section = $section "
        . 'and a.due_date >= "' . date( 'Y-m-d H:i:s' ) . '" '
        . 'order by due_date';
    $future_result = $db->query( $future_query );
    if( $future_result->num_rows == 0 ) {
        print 'None.';
    } else {
        print "<div class=\"assignments\" id=\"future\">\n";
        while( $lab = $future_result->fetch_assoc( ) ) {
            print "<h3><a href=\"#\">Lab Assignment #{$sequence}</a></h3>\n";
            print "<div class=\"lab_assignment\" id=\"{$lab[ 'id' ]}\">\n\n";

            if( isset( $lab[ 'title' ] ) ) {
                print "<div class=\"title\">{$lab[ 'title' ]}</div>\n\n";
            }
            
            print "<div class=\"due_date\">\n";
            print "Due \n"
                . date( 'l, F j \a\t g:i a', strtotime( $lab[ 'due_date' ] ) )
                . "</div>\n\n";
            
            if( isset( $lab[ 'description' ] ) ) {
                print "<div class=\"description\">Assignment: {$lab[ 'description' ]}</div>\n\n";
            }
            
            $docs_query = 'select * from assignment_documents '
                . "where assignment = {$lab[ 'id' ]} "
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
            
            // Are lab assignments collected in this class?
            $collected_query = 'select w.collected '
                . 'from sections as s, grade_weights as w '
                . 'where w.course = s.course '
                . "and s.id = $section "
                . "and w.grade_type = {$lab[ 'grade_type' ]}";
            $collected_result = $db->query( $collected_query );
            $collected_row = $collected_result->fetch_assoc( );
            if( $collected_row[ 'collected' ] ) {
                print "<div class=\"submission_details\">\n";
                $sub_query = 'select id, time, submission from assignment_submissions '
                    . "where assignment = {$lab[ 'id' ]} "
                    . "and student = {$_SESSION[ 'student' ]}";
                $sub_result = $db->query( $sub_query );
                if( $sub_result->num_rows == 0 ) {
                    print "<div class=\"submission\" id=\"{$lab[ 'id' ]}\">You did not "
                        . "submit this assignment.  Click here to submit now; click away when you're done.</div>";
                } else {
                    $sub_row = $sub_result->fetch_assoc( );
                    print 'Your submission was accepted on '
                        . date( 'l, F j \a\t g:i a', strtotime( $sub_row[ 'time' ] ) )
                        . ".  Click on it to edit it; click away when done.<br />\n";
                    if( trim( $sub_row[ 'submission' ] ) != '' ) {
                        print "<div class=\"submission\" "
                            . "id=\"{$lab[ 'id' ]}\">"
                            . stripslashes( $sub_row[ 'submission' ] )
                            . "</div>\n";
                    }
                }
                
                // Do something here about file uploads!
                
                print "</div> <!-- div.submission_details -->\n\n";
            } // if homework is collected in this class

            print "</div>  <!-- div.lab_assignment#{$lab[ 'id' ]} -->\n";
            $sequence++;
        }
        print "</div>  <!-- div#future -->\n";
    }

    
?>

<script type="text/javascript">
$(document).ready(function(){
    $('div.assignments').accordion({
        active: false,
        autoHeight: false,
        collapsible: true
    });
    
    $('div#future div.submission').editInPlace({
        url: 'submit_lab_assignment.php',
        params: 'ajax=yes',
        field_type: "textarea",
		textarea_rows: "10",
		textarea_cols: "50",
        saving_image: "<?php echo $docroot; ?>/images/ajax-loader.gif"
    })

})
</script>

<?php
        
}
   
$lastmod = filemtime( $_SERVER[ 'SCRIPT_FILENAME' ] );
include( "$fileroot/_footer.inc" );

?>
