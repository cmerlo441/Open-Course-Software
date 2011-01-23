<?php

$no_header = 1;
require_once( '../_header.inc' );

if( $_SESSION[ 'admin' ] == 1 ) {
    $id = $db->real_escape_string( $_REQUEST[ 'id' ] );

    $ref_query = 'select * from reference '
	. "where id = $id";
    $ref_result = $db->query( $ref_query );
    $ref_object = $ref_result->fetch_object( );

    print "<div id=\"filesize\"$ref_object->size bytes</div>\n";
    print "<div id=\"uploaded\">Uploaded "
	. date( 'l, F j, Y \a\t g:i a', strtotime( $ref_object->uploaded ) )
	. "</div>\n";
    print "<div id=\"available\">"
	. "<span class=\"available\" id=\"$id\">"
	. ( $ref_object->available == 1 ? 'A' : 'Una' )
	. 'vailable for download</span>.</div>';

    $download_query = 'select s.id, s.first, s.middle, s.last, d.datetime '
	. 'from reference_downloads as d, students as s '
	. "where d.reference = $id "
	. 'and d.student = s.id '
	. 'order by d.datetime desc, s.last, s.first, s.middle';
    $download_result = $db->query( $download_query );
    if( $download_result->num_rows == 0 ) {
	print 'Never downloaded.';
    } else {
	$num = $download_result->num_rows;
	print "<div id=\"downloads\">\n";
	print "<br />\n";
	print "<span style=\"text-align: center; padding-top: 0.5em;\">"
	    . "$num Download" . ( $num == 1 ? '' : 's' ) . "</span>\n";
	print "<ul id=\"download_list\" "
	    . "style=\"max-height: 6em; overflow: auto;\">\n";
	while( $dl = $download_result->fetch_object( ) ) {
	    print "<li id=\"$dl->id\"><span style=\"font-weight: bold;\">"
		. ucwords( $dl->first ) . ' ' . ucwords( $dl->last )
		. '</span> '
		. date( 'D, M j g:i a', strtotime( $dl->datetime ) )
		. "</li>\n";
	}
	print "</ul>\n";
	print "</div> <!-- div#downloads -->\n";
    }

}

?>