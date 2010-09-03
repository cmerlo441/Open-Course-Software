<?php

$no_header = 1;
require_once( '../_header.inc' );

if( $_SESSION[ 'admin' ] == 1 ) {

  // Does this professor have a Twitter account?
  $twitter = null;
  $twitter_enabled_query = 'select twitter_username as u, '
    . 'twitter_password as p '
    . 'from prof';
  $twitter_enabled_result = $db->query( $twitter_enabled_query );
  if( $twitter_enabled_result->num_rows > 0 ) {
    include_once( '../twitter/class.twitter.php' );
    $twitter_creds_row = $twitter_enabled_result->fetch_assoc( );
    $u = $twitter_creds_row[ 'u' ];
    $p = $twitter_creds_row[ 'p' ];
    $twitter = new twitter( );
    $twitter->username = $u;
    $twitter->password = $p;
  }

    $grade_type = $db->real_escape_string( $_POST[ 'assignment_type' ] );
    $section = $db->real_escape_string( $_POST[ 'section' ] );
    $posted_date = date( 'Y-m-d H:i:s' );
    $due_date = date( 'Y-m-d H:i:s',
        strtotime( $db->real_escape_string( trim( $_POST[ 'due_date' ] ) )
		   . ' '
		   . $db->real_escape_string( trim( $_POST[ 'due_time' ] ) ) ) );
    $title = $db->real_escape_string( trim( $_POST[ 'title' ] ) );
    $description = $db->real_escape_string( trim( $_POST[ 'description' ] ) );

    $db->query( 'lock tables assignments, grade_events' );
    
    $assignment_query = 'insert into assignments '
        . '( id, grade_type, section, posted_date, due_date, title, description, '
        . 'grade_summary_tweeted ) '
        . "values( null, $grade_type, $section, \"$posted_date\", \"$due_date\", "
        . "\"$title\", \"$description\", 0 )";

    print $assignment_query;

    $assignment_result = $db->query( $assignment_query );
    
    $grade_event_query = 'insert into grade_events '
        . '( id, section, grade_type, date, assignment ) values '
        . "( null, $section, $grade_type, "
        . '"' . date( 'Y-m-d', strtotime( $due_date ) ) . '", '
        . $db->insert_id . ' )';
    $grade_event_result = $db->query( $grade_event_query );
    
    $db->query( 'unlock tables' );

    // Tweet it!

    if( $twitter != null ) {

      // Get the course and section

      $section_query = 'select c.id as course_id, c.dept, c.course, s.section '
	. 'from courses as c, sections as s '
	. 'where s.course = c.id '
	. "and s.id = $section";

      print $section_query;

      $section_result = $db->query( $section_query );
      $section_row = $section_result->fetch_assoc( );
      $section = $section_row[ 'dept' ] . ' ' . $section_row[ 'course' ]
	. ' ' . $section_row[ 'section' ];

      // Get the assignment type

      $grade_type_query = 'select t.grade_type as t, w.collected as w '
	. 'from grade_types as t, grade_weights as w '
	. "where t.id = $grade_type "
	. 'and w.grade_type = t.id '
	. "and w.course = {$section_row[ 'course_id' ]}";

      $grade_type_result = $db->query( $grade_type_query );
      $grade_type_row = $grade_type_result->fetch_assoc( );
      $type = $grade_type_row[ 't' ];

      // Only tweet things that will be collected...

      if( $grade_type_row[ 'w' ] == 1 ) {

	// Build the string

	$update_string = 'New ' . strtolower( $type ) . ' ';
	if( $title != '' ) {
	  $update_string .= "\"$title\" ";
	}
	$update_string .= " posted for $section.  Due "
	  . date( 'l, F jS', strtotime( $due_date ) )
	  . '.';

	// Tweet it!

	$twitter->update( $update_string );
      }

      // ... or exams that will happen in the future

      else if( preg_match( '/[Ee]xam/', $type ) == 1 and
		 date( 'Y-m-d H:i' ) < date( 'Y-m-d H:i',
					     strtotime( $due_date ) ) ) {

	// Build the string
	$update_string = "$type scheduled for $section on "
	  . date( 'l, F jS', strtotime( $due_date ) );

	// Tweet it!

	$twitter->update( $update_string );

      }

      // ... or HW for math classes
      else if( preg_match( '/^MAT/', $section ) and
	       date( 'Y-m-d H:i' ) < date( 'Y-m-d H:i',
					   strtotime( $due_date ) ) and
	       preg_match( '/Homework/i', $type ) ) {
	// Build the string
	       
	$update_string = 'New ' . strtolower( $type ) . ' ';
	if( $title != '' ) {
	  $update_string .= "\"$title\" ";
	}
	$update_string .= " posted for $section.  Due "
	  . date( 'l, F jS', strtotime( $due_date ) )
	  . '.';

	// Tweet it!

	$twitter->update( $update_string );
      }
    }
 }

?>
