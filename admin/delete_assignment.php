<?php

$no_header = 1;
require_once( '../_header.inc' );

if( $_SESSION[ 'admin' ] == 1 ) {

    $assignment = $db->real_escape_string( $_REQUEST[ 'id' ] );
    
    // Are there any upload requirements for this assignment?
    
    $upload_req_query = 'select * from assignment_upload_requirements '
        . "where assignment = $assignment";
    $upload_req_result = $db->query( $upload_req_query );
    while( $req = $upload_req_result->fetch_object( ) ) {
	$delete_uploads_query = 'delete from assignment_uploads '
            . "where assignment_upload_requirement = $req->id";
        $db->query( $delete_uploads_query );
	$delete_upload_reqs_query = 'delete from assignment_upload_requirements '
	    ."where id = $req->id";
        $db->query( $delete_upload_reqs_query );
    }

    $sub_query = 'select * from assignment_submissions '
        . "where assignment = $assignment";
    $sub_result = $db->query( $sub_query );
    while( $sub = $sub_result->fetch_object( ) ) {
	$delete_comments_query = 'delete from submission_comments '
            . "where submission_id = $sub->id";
        $db->query( $delete_comments_query );
	$delete_submissions_query = "delete from assignment_submissions where id = $sub->id";
	$db->query( $delete_submissions_query );
    }
    
    $dox_query = 'select * from assignment_documents '
        . "where assignment = $assignment";
    $dox_result = $db->query( $dox_query );
    while( $doc = $dox_result->fetch_object( ) ) {
	$delete_documents_query = "delete from assignment_documents where id = $doc->id";
	$db->query( $delete_documents_query );
    }
    
    $events_query = 'select * from grade_events '
        . "where assignment = $assignment";
    $events_result = $db->query( $events_query );
    while( $event = $events_result->fetch_object( ) ) {
	$delete_grades_query = "delete from grades where grade_event = $event->id";
	$db->query( $delete_grades_query );
	$delete_events_query = "delete from grade_events where id = $event->id";
	$db->query( $delete_events_query );
    }
    
    $delete_assignments_query = "delete from assignments where id = $assignment";
    $db->query( $delete_assignments_query );
}

?>
