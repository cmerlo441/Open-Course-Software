<?php

$title_stub = 'Page View History';
require_once( '../_header.inc' );

if( $_SESSION[ 'admin' ] == 1 ) {

    $student_id = isset( $_GET[ 'student' ] )
	? $db->real_escape_string( $_GET[ 'student' ] )
	: 0;
    $page = isset( $_GET[ 'page' ] )
	? $db->real_escape_string( $_GET[ 'page' ] )
	: 0;

    unset( $student );
    if( $student_id > 0 ) {
	$student_query = 'select first, middle, last from students '
	    . "where id = $student_id";
	$student_result = $db->query( $student_query );
	$student_row = $student_result->fetch_assoc( );
	$student = name( $student_row );
    }

    print "<div id=\"page_views\"></div>\n";

    print "<p style=\"text-align: center\">\n";
    print "<button id=\"more\">Load Next 25</button>\n";
    print "</p>\n";

?>

<script type="text/javascript">
$(document).ready(function(){

    $.post( 'list_page_views.php',

        {
            student: "<?php echo $student_id; ?>",
            page: "<?php echo $page; ?>"
	},

        function(data) {
            $('div#page_views').html(data);
	    $('div.twenty_five_rows').slideDown();
	    $('a.more').click(function(){
                var id = $(this).attr('id');
		$(this).html() == 'More'
		    ? $(this).html('Less')
		    : $(this).html('More');
		$('div.referrer[id=' + id + ']' ).slideToggle();
            })
	}
    );

    var student = "<?php echo isset( $student ) ? $student : ''; ?>";
    if( student != '' ) {
	var name = " :: " + student;
	$('h1').html( $('h1').html( ) + name );
	$(document).attr('title', $(document).attr('title') + name );
    }

    var page = "<?php echo $_GET[ 'page' ]; ?>";
    if( page != '' ) {
	$('h1').html( $('h1').html( ) + ' :: ' + page );
	$(document).attr('title', $(document).attr('title') + ' :: ' + page );
    }

    $('button#more').click(function(){
        var last_row = $('div#page_views div.page_view:last').attr('id');
	var next_row = ( last_row * 1 ) + 1;
	$.post( 'list_page_views.php',
            { start: next_row, end: 25 },
            function(data){
                $('div#page_views').append(data);
		$('div.twenty_five_rows:last').slideDown(1500);
            }
	);
    });

})
</script>

<?php

} else {
    print $no_admin;
}
   
$lastmod = filemtime( $_SERVER[ 'SCRIPT_FILENAME' ] );
include( "$fileroot/_footer.inc" );

?>