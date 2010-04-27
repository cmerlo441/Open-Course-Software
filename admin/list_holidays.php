<?php

require_once( '/home/cmerlo/.htpasswd' );
session_start( );

if( $_SESSION[ 'admin' ] == 1 ) {
    $holidays_query = 'select * from holidays order by date, description';
    $holidays_result = $db->query( $holidays_query );
    
    if( $holidays_result->num_rows == 0 ) {
        print "<p>No holidays defined.</p>\n";
    } else {
    
        print "<table class=\"tablesorter\" id=\"holidays_table\">\n";
        print "<thead>\n";
        print "<tr>\n";
        print "  <td>Date</td>\n";
        print "  <td>Description</td>\n";
        print "  <td>Day Clases Cancelled?</td>\n";
        print "  <td>Evening Classes Cancelled?</td>\n";
        print "</tr>\n";
        print "</thead>\n\n";
        
        print "<tbody>\n";
        while( $row = $holidays_result->fetch_assoc( ) ) {
            print "<tr>\n";
            print "  <td class=\"date\" id=\"{$row[ 'id' ]}\">{$row[ 'date' ]}</td>\n";
            print "  <td class=\"description\" id=\"{$row[ 'id' ]}\">{$row[ 'description' ]}</td>\n";
            print "  <td class=\"day\" id=\"{$row[ 'id' ]}\"><input type=\"checkbox\" "
                . ( $row[ 'day' ] == 1 ? ' checked' : '' )
                . "/></td>\n";
            print "  <td class=\"evening\" id=\"{$row[ 'id' ]}\"><input type=\"checkbox\" "
                . ( $row[ 'evening' ] == 1 ? ' checked' : '' )
                . "/></td>\n";
            print "</tr>\n";
        }
        print "</tbody>\n</table>\n";
    }
}
?>