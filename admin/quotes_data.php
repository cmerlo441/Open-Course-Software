<?php

$no_header = 1;
require_once( '../_header.inc' );

// Get the pencil icon from silk icons

if( $_SESSION[ 'admin' ] == 1 ) {
    
    $edit = $db->real_escape_string( $_POST[ 'edit' ] );
    if( $edit != '' ) {
        $quote = $db->real_escape_string( $_POST[ 'quote' ] );
        $attribution = $db->real_escape_string( $_POST[ 'attribution' ] );
        $edit_query = 'update quotes '
            . "set quote = \"$quote\", "
            . "attribution = \"$attribution\" "
            . "where id = $edit";
        $db->query( $edit_query );
    }

    $search = $db->real_escape_string( $_REQUEST[ 'search' ] );
    $quotes_query = 'select * from quotes ';
    if( $search != '' ) {
    	$quotes_query .= "where quote like \"%$search%\" "
    	    . "or attribution like \"%$search%\" ";
    }
    $quotes_query .= 'order by id';
    $quotes_result = $db->query( $quotes_query );

    if( $quotes_result->num_rows == 0 )
	print 'No quotes found.';

    $count = 0;
    while( $quote = $quotes_result->fetch_object( ) ) {
    	print "<div id=\"quote_$quote->id\" class=\""
    	    . ( ++$count % 2 == 0 ? "even" : "odd" )
    	    . "\">\n";
        
        // Icons
        print "<div class=\"icons\">\n";
        
        print "<a href=\"javascript:void(0)\" class=\"delete\" id=\"delete_$quote->id\">\n";
        print "<img src=\"$docroot/images/silk_icons/cross.png\" width=\"16\" "
            . "height=\"16\" alt=\"Delete this quote\" /></a>\n";
            
        print "<a href=\"javascript:void(0)\" class=\"edit\" id=\"edit_$quote->id\">\n";
        print "<img src=\"$docroot/images/silk_icons/pencil.png\" width=\"16\" "
            . "height = \"16\" alt=\"Edit this quote\" />\n";
    
        print "</div> <!-- div.icons -->\n";
    
        // Quote
    	print "<div class=\"quote\">$quote->quote\n";
        if( $quote->attribution != '' ) {
    	    print "<br /><span class=\"attribution\">$quote->attribution</span>\n";
        }
        print "</div> <!-- div.quote -->\n";
    	print "</div>  <!-- div#quote_$quote->id -->\n";
    }

}

?>