<?php

$no_header = 1;
require_once( './_header.inc' );

$admin_query = 'select * from prof '
  . 'where username = "' . $db->real_escape_string( $_POST[ 'username' ] ) . '" '
  . 'and password = md5( "' . $db->real_escape_string( $_POST[ 'password' ] )
  . '" )';
$admin_result = $db->query( $admin_query );

$student_query = 'select * from students '
  . 'where banner = "'
      . strtoupper( $db->real_escape_string( $_POST[ 'username' ] ) ) . '" '
  . 'and password = md5( "' . $db->real_escape_string( $_POST[ 'password' ] )
  . '" )';
$student_result = $db->query( $student_query );

if( $student_result->num_rows == 1 ) {
    $student_row = $student_result->fetch_assoc( );

    // If student is not active in any classes, don't log him/her in

    $x_query = 'select * from student_x_section '
      . "where student = {$student_row[ 'id' ]} "
      . 'and active = 1';
    $x_result = $db->query( $x_query );
    if( $x_result->num_rows == 0 ) {
      print 'inactive';
      die( );
    }

    $_SESSION[ 'student' ] = $student_row[ 'id' ];
    foreach( explode( ' ', 'first last email banner' ) as $field ) {
        $_SESSION[ $field ] = $student_row[ $field ];
    }
    if( $student_row[ 'middle' ] != '' ) {
        $_SESSION[ 'middle' ] = $student_row[ 'middle' ];
        $_SESSION[ 'name' ] = $student_row[ 'first' ] . ' '
            . $student_row[ 'middle' ] . ' ' . $student_row[ 'last' ];
    } else {
        $_SESSION[ 'name' ] = $student_row[ 'first' ] . ' '
            . $student_row[ 'last' ];
    }
    
    $login_query = 'insert into logins( id, student, datetime, address, browser ) '
        . "values( null, {$student_row[ 'id' ]}, \""
        . date( 'Y-m-d H:i:s' ) . "\", \"{$_SERVER[ 'REMOTE_ADDR' ]}\", "
        . "\"{$_SERVER[ 'HTTP_USER_AGENT' ]}\" )";
    $login_result = $db->query( $login_query );
    
    print 'student';
    die( );
}

if( $admin_result->num_rows == 1 ) {
	$_SESSION[ 'admin' ] = 1;
	print 'admin';
    die( );
}

// else if ...

else {
    print 'none';
}
