<?php

$title_stub = 'Reference Materials';
require_once( '../_header.inc' );

$dir_names = array( );
$directory = '.';
$dh = opendir( $directory );
while( $next = readdir( $dh ) ) {
  if( is_file( $next ) and substr( $next, 0, 1 ) != '.'  and substr( $next, -4, 4 ) != '.php' ) {
    $dir_names[ ] = $next;
  }
 }

print "<ul>\n";
natsort( $dir_names );
foreach( $dir_names as $dir ) {
  print "<li>";
  print_link( htmlentities( $dir ), $dir );
  print "</li>\n";
}
print "</ul>\n";

$lastmod = filemtime( $_SERVER[ 'SCRIPT_FILENAME' ] );
include_once( '../_footer.inc' );

?>
