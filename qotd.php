<?php

$no_header = 1;
require_once( './_header.inc' );
   
$qotd_query = 'select v from phprof where k = "qotd"';
$qotd_result = $db->query( $qotd_query );
$qotd = $qotd_result->fetch_assoc( );
if( $qotd[ 'v' ] == 1 ) {
    $quote_count_query = 'select id from quotes';
    $quote_count_result = $db->query( $quote_count_query );
    $quote_count = $quote_count_result->num_rows;
    $quote_query = 'select quote as q, attribution as a from quotes '
        . 'where id = ' . rand( 1, $quote_count );
    $quote_result = $db->query( $quote_query );
    $quote = $quote_result->fetch_assoc( );
    print "<span id=\"quote\">{$quote[ 'q' ]}</span>";
    if( $quote[ 'a'] != '' ) {
        print "  <span id=\"attribution\">{$quote[ 'a' ]}</span>";
    }
}

?>
