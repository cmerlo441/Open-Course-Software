<?php
   
$no_header = 1;
require_once( '../_header.inc' );

if( $_SESSION[ 'student' ] > 0 ) {
    
    $update_value = $db->real_escape_string( trim( convert_smart_quotes( nl2br( $_POST[ 'update_value' ] ) ) ) );
    $original_html = $db->real_escape_string( trim( nl2br( convert_smart_quotes( $_POST[ 'original_html' ] ) ) ) );
    
    // This is the ID # of the assignment
    $id = $db->real_escape_string( trim( $_POST[ 'element_id' ] ) );
    
	if( $update_value != $original_html and trim( $update_value ) != '' ) {
        // Let's make sure it's still before the deadline.
        
        $deadline_query = 'select due_date from assignments '
            . "where id = $id";
        $deadline_result = $db->query( $deadline_query );
        $deadline_row = $deadline_result->fetch_assoc( );
        $deadline = $deadline_row[ 'due_date' ];
        
        if( date( 'Y-m-d H:i:s' ) <= $deadline ) {
            
            /* This could be an edit of a new submission.  If so, don't
             * insert a new row; edit the current one
             */
            
            $existing_query = 'select * from assignment_submissions '
                . "where student = {$_SESSION[ 'student' ]} "
                . "and assignment = $id";
            $existing_result = $db->query( $existing_query );
            if( $existing_result->num_rows == 0 ) {
        
                $db->query( 'lock tables assignment_submissions' );
                
                $insert_query = 'insert into assignment_submissions '
                    . '( id, assignment, student, time, submission ) '
                    . "values( null, $id, {$_SESSION[ 'student' ]}, "
                    . '"' . date( 'Y-m-d H:i:s' ) . '", '
                    . "\"$update_value\" )";
                $insert_result = $db->query( $insert_query );
                $new_submission_id = $db->insert_id;
                $check_query = 'select submission from assignment_submissions '
                    . "where id = $new_submission_id";
                $check_result = $db->query( $check_query );
                $check_row = $check_result->fetch_assoc( );
                print stripslashes( $check_row[ 'submission' ]  );
                
                $db->query( 'unlock tables' );
            }
            
            /* Or, this is a new submission */
           
            else {
                $update_query = 'update assignment_submissions '
                    . "set submission = \"$update_value\", "
                    . 'time = "' . date( 'Y-m-d H:i:s' ) . '" '
                    . "where assignment = \"$id\" "
                    . "and student = {$_SESSION[ 'student' ]}";
                $update_result = $db->query( $update_query );
                
                $check_query = 'select submission from assignment_submissions '
                    . "where assignment = \"$id\" "
                    . "and student = \"{$_SESSION[ 'student' ]}\"";
                $check_result = $db->query( $check_query );
                $check_row = $check_result->fetch_assoc( );
                
                print stripslashes( $check_row[ 'submission' ] );
            }
        } // if it's still before the deadline
    } else {
        print stripslashes( $original_html );
    }
} else {
    print $no_student;
}

?>
