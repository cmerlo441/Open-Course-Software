<?php

$no_header = 1;
require_once( '../_header.inc' );

if( $_SESSION[ 'student' ] > 0 ) {
    
    $section = $db->real_escape_string( $_REQUEST[ 'section' ] );

    $ref_query = 'select * from reference '
        . "where section = $section "
        . "and available = 1 "
        . 'group by id '
        . 'order by uploaded desc, filename';
    $ref_result = $db->query( $ref_query );
    
    if( $ref_result->num_rows == 0 ) {
        print 'None.';
    } else {
  
        $count = 0;
        while( $ref = $ref_result->fetch_object( ) ) {
            
            $dl_query = 'select datetime from reference_downloads '
                . "where student = {$_SESSION[ 'student' ]} "
                . "and reference = $ref->id "
                . 'order by datetime desc';
            $dl_result = $db->query( $dl_query );
            $num = $dl_result->num_rows;
            
            print "<div class=\"reference\" style=\"margin: 0.5em; padding: 0.1em 0.25em;";
            if( $count % 2 == 0 ) {
                print 'background-color: #464648;';
            }
            print "\" id=\"$ref->id\">\n";

            print "  <div class=\"details\" style=\"float: right; text-align: right;\"> Uploaded "
                . date( 'D Y-m-d \a\t g:i a', strtotime( $ref->uploaded ) ) . "<br />\n";
            print "  $ref->size bytes<br />\n";
            if( $num == 0 ) {
                print 'You have never downloaded this file.';
            } else {
                $time = $dl_result->fetch_object( );
                print "You have downloaded this file $num time" . ( $num == 1 ? '' : 's' )
                    . ".<br />Last time was "
                    . date( 'D Y-m-d \a\t g:i a', strtotime( $time->datetime ) )
                    . ".\n";
            }
            print "  </div>\n";

            print "  <div class=\"filename\" style=\"font-size: 1.25em;\" \n"
                . "  id=\"filename_$ref->id\" style=\"float: left;\">";
            print_link( "../download_reference_material.php?id=$ref->id", $ref->filename );
            print "  </div>\n";
            
            print "<div style=\"clear: both;\">&nbsp;</div>\n";
            
            print "</div>\n";
            
            ++$count;
        }
    }
}
?>