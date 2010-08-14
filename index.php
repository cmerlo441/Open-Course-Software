<?php

require_once( './_header.inc' );

print wordwrap( "<p>Welcome to my web site.  Students, be sure to create an "
		. "account if you haven&apos;t done so already.  You will be "
		. "using the tools on this site to submit assignments and "
		. "stay in contact with me outside of class.  Make sure to "
		. "keep your personal information up to date.</p>\n" );

print "<p>View this semester's ";
print_link( "$docroot/graphs/", "login statistics" );
print ".</p>\n";

$pages_query = 'select * from pages order by title';
$pages_result = $db->query( $pages_query );
if( $pages_result->num_rows > 0 ) {

    print "<h2>Important Information</h2>\n";
    
    print "<ul>\n";
    while( $row = $pages_result->fetch_assoc( ) ) {
        print '<li>';
        print_link( "pages/{$row[ 'slug' ]}",
		    stripslashes( $row[ 'title' ] ) );
        print "</li>\n";
    }
    print "</ul>\n";
}

// Does this professor have a Twitter account?

$twitter_enabled_query = 'select count( * ) as c from prof '
    . 'where twitter_username is not null';
$twitter_enabled_result = $db->query( $twitter_enabled_query );
$twitter_enabled = $twitter_enabled_result->fetch_assoc( );
if( $twitter_enabled[ 'c' ] == 1 ) {
    $twitter_username_query = 'select twitter_username as u '
	. 'from prof';
    $twitter_username_result = $db->query( $twitter_username_query );
    $twitter_username_row = $twitter_username_result->fetch_assoc( );
    $u = $twitter_username_row[ 'u' ];

    if( $u != '' and $u != 'NULL' ) {

	print "<h2>Prof. {$prof[ 'last' ]}'s Twitter Feed</h2>\n";
	print "<div id=\"twitter\" style=\"padding: 1em;\">\n";
    }

    ?>
<script src="http://widgets.twimg.com/j/2/widget.js"></script>
<script>
new TWTR.Widget({
    version: 2,
    type: 'profile',
    rpp: 4,
    interval: 6000,
    width: 'auto',
    height: 250,
    theme: {
        shell: {
            background: '#5c552c',
            color: '#ffffff'
        },
        tweets: {
            background: '#1e273e',
            color: '#ffffff',
            links: '#c7c6c1'
        }
    },
    features: {
        scrollbar: true,
        loop: false,
        live: true,
        hashtags: true,
        timestamp: true,
        avatars: true,
        behavior: 'all'
    }
}).render().setUser("<?php echo $u; ?>").start();
</script>
<?php
      print "</div>\n";
}

$lastmod = filemtime( $_SERVER[ 'SCRIPT_FILENAME' ] );
require_once( './_footer.inc' );

?>
