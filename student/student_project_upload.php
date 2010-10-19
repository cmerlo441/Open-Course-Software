<?php

$no_header = 1;
require_once( '../_header.inc' );

if( $_FILES[ 'file' ][ 'error' ] == 0 ) {

    $student = $db->real_escape_string( $_POST[ 'student' ] );
    $assignment = $db->real_escape_string( $_POST[ 'assignment' ] );
    
    /* Look at the assignment.  For now, projects are hard-coded acceptable
     * up to five days after the deadline.  Also, check the file name to see if
     * the project is expecting it.
     */
    
    $assignment_query = 'select a.id '
        . 'from assignments as a, grade_types as t '
        . "where a.id = $assignment "
        . 'and a.due_date >= "'
        . date( 'Y-m-d H:i:s', mktime( 23, 59, 0, date( 'n' ), date( 'j' ) - 5 ) ) . '" '
        . 'and a.grade_type = t.id '
        . 'and t.grade_type = "Project"';

    $assignment_result = $db->query( $assignment_query );
    if( $assignment_result->num_rows == 1 ) {
        
        // Make sure we want a file with this name
        
        $reqs_query = 'select id from assignment_upload_requirements '
            . "where assignment = $assignment "
            . "and filename = \"{$_FILES[ 'file' ][ 'name' ]}\"";
        $reqs_result = $db->query( $reqs_query );
        if( $reqs_result->num_rows == 1 ) {

	    $req_row = $reqs_result->fetch_assoc( );
	    $req_id = $req_row[ 'id' ];
    
            $fh = fopen( $_FILES[ 'file' ][ 'tmp_name' ], 'r' );
            $line = fgets( $fh );
            
            while( $line ) {
                $file .= $line;
                $line = fgets( $fh );
            }
            
            $file = $db->real_escape_string( $file );

            // Insert or update?
            $file_query = 'select * from assignment_uploads '
                . "where student = $student "
                . "and assignment_upload_requirement = $req_id "
                . "and filename = \"{$_FILES[ 'file' ][ 'name' ]}\"";
            $file_result = $db->query( $file_query );
            if( $file_result->num_rows == 1 ) {
                // Update
                $file_row = $file_result->fetch_assoc( );
                $update_query = 'update assignment_uploads '
                    . "set datetime = \"" . date( 'Y-m-d H:i:s' ) . '", '
                    . "filesize = \"{$_FILES[ 'file' ][ 'size' ]}\", "
                    . "file = \"$file\" "
                    . "where id = {$file_row[ 'id' ]}";
                $update_result = $db->query( $update_query );
            } else {
                $insert_query = 'insert into assignment_uploads '
                    . '( id, student, assignment_upload_requirement, '
		    . 'filename, filesize, datetime, file ) '
                    . "values( null, $student, $req_id, "
		    . "\"{$_FILES[ 'file' ][ 'name' ]}\", "
                    . "\"{$_FILES[ 'file' ][ 'size' ]}\", \"" . date( 'Y-m-d H:i:s' ) . "\", \"$file\" )";
                $insert_result = $db->query( $insert_query );
                print $insert_query;
            }
        } else {
            print 'Unexpected filename';
        }

    } else {
        print 'Project not found or too late';
    }
} else {
    print 'Error.';
}

?>