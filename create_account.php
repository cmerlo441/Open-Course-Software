<?php

$no_header = 1;
require_once( '_header.inc' );

$first = $db->real_escape_string( trim( $_POST[ 'first' ] ) );
$middle = $db->real_escape_string( trim( $_POST[ 'middle' ] ) );
$last = $db->real_escape_string( trim( $_POST[ 'last' ] ) );
$email = $db->real_escape_string( trim( $_POST[ 'email' ] ) );
$banner = $db->real_escape_string( trim( strtoupper( $_POST[ 'banner' ] ) ) );
$sections = preg_replace( '/(.*),/', '$1', $_POST[ 'sections' ] );

/*
$words_query = 'select v from ocsw where k = "words"';
$words_result = $db->query( $words_query );
if( $words_result->num_rows == 1 ) {
    $words_row = $words_result->fetch_assoc( );
    $words_file = $words_row[ 'v' ];
} else {
    unset( $words_file );
}

if( isset( $words_file ) ) {
    $words = array();
    $fh = fopen( $words_file, 'r' );
    $skip = fread( $fh, 80 );  // In case of a leading blank line
    while( $word = fread( $fh, 80 ) ) {
        if( strlen( $word ) >= 6 && strlen( $word ) <= 8 ) {
            $words[ ] = $word;
        }
    }
    $keys = array_rand( $words, 2 );
    $code = $words[ $keys[ 0 ] ] . mt_rand( 1, 999 ) . $words[ $keys[ 1 ] ];
} else {
*/
    for( $i = 0; $i < 5; $i++ ) {
        $code .= mt_rand( 1, 999 );
    }
/*
}
*/

// Check for already used Banner ID, and disallow if it's been used

// Must print "good"/"bad" to let the AJAX call know if this worked or not

$well_formed_banner = preg_match( '/[Nn]00[0-9]{6}/', $banner );
if( $well_formed_banner == 0 ) {
    print 'Malformed Banner';
} else {

    $used_banner_query = 'select * from students '
        . "where banner = \"$banner\"";
    $used_banner_result = $db->query( $used_banner_query );
    if( $used_banner_result->num_rows != 0 ) {
        print 'Used Banner';
    } else {
    
        $db->query( 'lock table students' );
        
        $student_query = 'insert into students( id, first, middle, last, email, banner, password, verified ) '
            . 'values( null, "' . $first . '", "' . $middle . '", '
            . '"' . $last . '", "' . $email . '", "' . $banner . '", null, 0 )';
        $student_result = $db->query( $student_query );
        $student_id = $db->insert_id;
        
        if( $student_id == 0 ) {
            print "Can't Insert";
        }
        
        $db->query( 'unlock tables' );
        
        $v_query = 'insert into student_x_verification '
            . '( id, student, code, creation_time ) '
            . "values( null, $student_id, $code, \"" . date( 'Y-m-d H:i:s' ) . "\" )";
        $v_result = $db->query( $v_query );
        
        foreach( explode( ',', $sections ) as $section ) {
            $section_query = 'insert into student_x_section '
                . '( id, student, section, active, incomplete ) '
                . "values( null, $student_id, $section, 0, 0 )";
            $section_result = $db->query( $section_query );
        }
        
        $student_name = $first . ' ';
        if( $middle != '' ) {
            $student_name .= $middle . ' ';
        }
        $student_name .= $last;
        
        $message = <<<MESSAGE
Hello, $first.  Thank you for choosing to create an account on my website.
Your account is not completed yet; you need to visit the link below and choose
a password.

If you did not choose to create an account on my website, you can safely ignore
this message.

Visit this hyperlink in your favorite web browser to continue creating your
account:

http://www.matcmp.ncc.edu{$docroot}/student/set_password.php?code=$code

Thank you.
MESSAGE;
        
        $headers = "From: {$prof[ 'name' ]} <{$prof[ 'email' ]}>\n";
        
        mail( "$student_name <$email>", 'Confirm Your Account Creation',
              $message, $headers );
              
        print 'Good';
    }
}
?>