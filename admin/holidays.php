<?php

$no_header = 1;
require_once( '../_header.inc' );

if( $_SESSION[ 'admin' ] == 1 ) {
    if( isset( $_POST[ 'date' ] ) ) {
        $insert_query = 'insert into holidays( id, date, description, day, evening ) '
            . 'values( null, "'
            . date( 'Y-m-d', strtotime( $_POST[ 'date' ] ) ) . '", '
            . "\"" . $db->real_escape_string( $_POST[ 'description' ] ) . "\", "
            . "{$_POST[ 'day' ]}, {$_POST[ 'evening' ]} )";
        $insert_result = $db->query( $insert_query );
    }
    
    $holidays_query = 'select * from holidays '
        . "where date >= \"" . date( 'Y-m-d', strtotime( $semester_start ) ) . '" '
        . "and date <= \"" . date( 'Y-m-d', strtotime( $semester_end ) ) . '" '
        . 'order by date';
    $holidays_result = $db->query( $holidays_query );
    if( $holidays_result->num_rows == 0 ) {
        print 'None.';
    } else {
        while( $row = $holidays_result->fetch_assoc( ) ) {
            print "<div class=\"holiday\" id=\"{$row[ 'id' ]}\">\n";
            print "<span class=\"remove\" id=\"{$row[ 'id' ]}\"";
            print "<a href=\"javascript:void(0)\" class=\"remove_holiday\" id=\"{$row[ 'id' ]}\" "
                . "title=\"Remove {$row[ 'description' ]}\">";
            print "<img src=\"$docroot/images/silk_icons/cancel.png\" height=\"16\" width=\"16\" /></a></span>\n";
            print "<span class=\"date\" id=\"{$row[ 'id' ]}\">"
                . date( 'l n/j', strtotime( $row[ 'date' ] ) ) . "</span>:\n";
            print "<span class=\"description\" id=\"{$row[ 'id' ]}\">"
                . stripslashes( $row[ 'description' ] ) . "</span>.\n";
            if( $row[ 'day' ] == 1 ) {
                print 'Day ';
                if( $row[ 'evening' ] == 1 ) {
                    print 'and evening ';
                }
            } else if( $row[ 'evening' ] == 1 ) {
                print 'Evening ';
            }
            print 'classes canceled.';
        }
    }
}

?>