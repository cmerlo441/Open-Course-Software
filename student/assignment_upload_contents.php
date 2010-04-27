<?php

$no_header = 1;
require_once( '../_header.inc' );

if( $_SESSION[ 'student' ] > 0 ) {
    
    $id = $db->real_escape_string( $_POST[ 'id' ] );
    
    // The id is a key on assignment_upload_requirements
    
    $reqs_query = 'select a.due_date, aur.assignment, aur.filename '
        . 'from assignments as a, assignment_upload_requirements as aur '
        . "where aur.id = $id "
        . 'and aur.assignment = a.id';
    $reqs_result = $db->query( $reqs_query );
    if( $reqs_result->num_rows == 1 ) {
        $req = $reqs_result->fetch_assoc( );
        $upload_query = 'select datetime, file from assignment_uploads '
            . "where student = {$_SESSION[ 'student' ]} "
            . "and assignment = {$req[ 'assignment' ]} "
            . "and filename = \"{$req[ 'filename' ]}\"";
        $upload_result = $db->query( $upload_query );
        if( $upload_result->num_rows == 1 ) {
            $upload = $upload_result->fetch_assoc( );
            print "<p>Uploaded "
                . date( 'l, F j, Y \a\t g:i a', strtotime( $upload[ 'datetime' ] ) ) . '.';
            if( $upload[ 'datetime' ] > $req[ 'due_date' ] ) {
                $diff = strtotime( $upload[ 'datetime' ] ) - strtotime( $req[ 'due_date' ] );
                $seconds_in_a_day = 60 * 60 * 24;
                $days_late = ceil( $diff / $seconds_in_a_day );
                print "<div class=\"late\">$days_late day"
                    . ( $days_late == 1 ? '' : 's' ) . " late.</div>\n";
            }
            print "</p>\n";
            if( substr( $req[ 'filename' ], -5, 5 ) == '.java' ) {
                print "<pre class=\"brush:java\">";
            } else if( substr( $req[ 'filename' ], -3, 3 ) == '.js' ) {
                print "<pre class=\"brush:js\">";
            } else if( substr( $req[ 'filename' ], -4, 4 ) == '.php' ) {
                print "<pre class=\"brush:php\">";
            } else if( substr( $req[ 'filename' ], -2, 2 ) == '.c' ) {
                print "<pre class=\"brush:c\">";
            } else if( substr( $req[ 'filename' ], -4, 4 ) == '.cpp' ) {
                print "<pre class=\"brush:cpp\">";
            } else {
                print "<pre class=\"brush:plain\">";
            }
            print htmlentities( $upload[ 'file' ] ) . "</pre>\n";
        } else {
            print 'No solution uploaded.';
        }
    } else {
        print 'That file is not associated with this project.';
    }
}

?>