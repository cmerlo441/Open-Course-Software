</div> <!-- content -->

</div> <!-- main -->
</div> <!-- middle -->

<div id="push">
</div> <!-- push -->

</div> <!-- wrapper_content -->

</div> <!-- wrapper -->

<div id="bottom">
<?php

print "<div id=\"last_edited\">This page was last edited on "
    . date( 'l, F j, Y \\a\\t g:i a', $lastmod )
    . ".</div>\n";
print '<div id="current_time">This page was printed on ' . date( 'l, F j, Y \\a\\t g:i a' );
if( $_SESSION[ 'student' ] > 0 ) {
    print " by {$_SESSION[ 'first' ]} "
        . ( $_SESSION[ 'middle' ] == '' ? '' : $_SESSION[ 'middle' ] . ' ' )
        . "{$_SESSION[ 'last' ]}";
}
print ".</div>\n";
print "<div id=\"qotd\">\n</div> <!-- div#qotd -->\n";

print "<div id=\"version\">OCSW Version {$ocsw_version}.  &copy; 2002-"
    . date( 'Y' ) . ".  Some rights reserved.</div>\n";

$db->close( );

?>
</div> <!-- bottom -->

<script type="text/javascript">
$(document).ready(function(){
    $.post( "<?php echo $docroot; ?>/qotd.php",
        function(data){
            $("div#bottom div#qotd").html(data);
        }
    )
})
</script>

</body>
</html>