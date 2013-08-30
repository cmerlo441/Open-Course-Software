<?php

$no_header = 1;
require_once( './_header.inc' );

$updated_query = 'select posted from atom order by posted desc limit 1';
$updated_result = $db->query( $updated_query );
//print $updated_query;
$updated = $updated_result->fetch_object();
$updated_time = date( 'c', strtotime( $updated->posted ) );
//print $updated_time;

print "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
print "<feed xml:lang=\"en-US\" xmlns=\"http://www.w3.org/2005/Atom\">\n";
print "  <title>Prof. {$prof[ 'last' ]}'s OCSW News Feed</title>\n";
print "  <link href=\"http://{$_SERVER[ 'SERVER_NAME' ]}$docroot/\" />\n";
print "  <link href=\"http://{$_SERVER[ 'SERVER_NAME' ]}$docroot/atom.php\" rel=\"self\" />\n";
print "  <updated>$updated_time</updated>\n";
print "  <author>\n";
print "    <name>{$prof[ 'name' ]}</name>\n";
print "  </author>\n";
print "  <id>urn:uuid:01939AF8-11A0-11E3-B185-70D96088709B</id>\n\n";

$items_query = 'select * from atom '
    . 'order by posted';
$items_result = $db->query( $items_query );
while( $item = $items_result->fetch_object( ) ) {
    print "  <entry>\n";
    print "    <title>$item->title</title>\n";
    print "    <id>$item->uuid</id>\n";
    print "    <link href=\"$item->url\" />\n";
    print "    <updated>" . date( 'c', strtotime( $item->posted ) )
	. "</updated>\n";
    print "    <author>\n";
    print "      <name>{$prof[ 'name' ]}</name>\n";
    print "    </author>\n";
    print "    <content type=\"xhtml\" xml:lang=\"en\">$item->content</content>\n";
    print "  </entry>\n\n";
}
print "</feed>\n";
?>
