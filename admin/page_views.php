<?php

$title_stub = 'Student Page Views';
require_once( '../_header.inc' );

if( $_SESSION[ 'admin' ] == 1 ) {
    
    print "<h2 id=\"page_views_description\">All Student Data</h2>\n";
    print "<div id=\"page_views\" student=\"0\" page_name=\"0\"></div>\n";
    
?>

<script type="text/javascript">

$(document).ready(function(){
    $.post('list_page_views.php',
        function(data){
            $('div#page_views').html(data);
        }
    )

    $(window).scroll(function(){
        if( $(window).scrollTop() == $(document).height() - $(window).height() ) {
            var start = $('div#page_views tr:last').attr('id');
            var student = $('div#page_views').attr('student');
            var page = $('div#page_views').attr('page_name');
            var last_id = $('div#page_views > div:last').attr('id');
            $.post('list_page_views.php',
                {
                     start: start,
                     student: student,
                     page_name: page,
                     last_id: last_id
                },
                function(data){
                    $('div#page_views').append(data);
                }
            )
        };
        return false;
    })
})

</script>

<?php

} else {
    print $no_admin;
}

$lastmod = filemtime( $_SERVER[ 'SCRIPT_FILENAME' ] );
include( "$fileroot/_footer.inc" );

