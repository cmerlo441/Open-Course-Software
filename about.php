<?php

$title_stub = 'About OCSW';
require_once( './_header.inc' );
   
?>

<p>OCSW was written by
<?php print_link( 'http://www.matcmp.ncc.edu/~cmerlo/',
                  'Prof. Christopher R. Merlo' ); ?> at
<?php print_link( 'http://www.ncc.edu/', 'Nassau Community College' ); ?>, using
the following technologies:</p>

<ul>
    <li><?php print_link( 'http://www.php.net/', 'PHP' ); ?>, an open-source
    programming language</li>
    <li><?php print_link( 'http://www.jquery.com/', 'jQuery' ); ?>, an open-source
    JavaScript library</li>
    <li><?php print_link( 'http://www.aptana.com/', 'Aptana Studio' ); ?>, an
    open-source web app IDE</li>
    <li><?php print_link( 'http://alexgorbatchev.com/wiki/SyntaxHighlighter',
                          'SyntaxHighlighter' ); ?>, a JavaScript code syntax
    highlighter</li>
    <li><?php print_link( 'http://markitup.jaysalvat.com/home/', 'markItUp!' ); ?>,
    a markup editor in a jQuery plugin</li>
    <li><?php print_link( 'http://code.google.com/p/jquery-in-place-editor/',
                            'Another In-Place Editor' ); ?>, a jQuery plugin that
    turns an HTML element into an in-place editor</li>
</ul>

<p>OCSW can be downloaded from
<?php print_link( 'http://www.ocsw.net/', 'the OCSW site' ); ?>.</p>

<?php

$lastmod = filemtime( $_SERVER[ 'SCRIPT_FILENAME' ] );
include( "$fileroot/_footer.inc" );

?>