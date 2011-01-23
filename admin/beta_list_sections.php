<?php 

$no_header = 1;
require_once( '../_header.inc' );

if( $_SESSION[ 'admin' ] == 1 ) {
    $sections_query = 'select s.id, c.dept, c.course, s.section, s.banner, s.day '
        . 'from courses as c, sections as s '
        . 'where s.course = c.id '
        . 'order by c.dept, c.course, s.day desc, s.section ';
    $sections_result = $db->query( $sections_query );
    if( $sections_result->num_rows == 0 ) {
        print 'There are no current sections.';
    } else {
        print "<ul>\n";
        while( $section = $sections_result->fetch_object( ) ) {
            print "<li id=\"$section->id\">";
            print "<a href=\"javascript:void(0)\" id=\"$section->id\" class=\"remove_section\">";
            print "<img src=\"$docroot/images/silk_icons/delete.png\" "
                . "alt=\"Delete this section\" title=\"Delete this section\" "
                . "width=\"16\" height=\"16\" /></a>\n";
            print "<a href=\"javascript:void(0)\" id=\"$section->id\" class=\"section_info\" "
                . "title=\"Section meetings\">";
            print "$section->dept $section->course $section->section</a>";
            print "</li>\n";
        }
        print "</ul>\n";
    }
?>
<div class="dialog" id="meetings_dialog"></div>
<script type="text/javascript">

$(document).ready(function(){

	$('a.section_info').click(function(){
		var id = $(this).attr('id');
	
		$.post( 'beta_section_info.php',
			{ id: id },
		    function(data){
    		    $('div#meetings_dialog').html(data).dialog({
    		    	autoOpen: true,
                    hide: 'puff',
                    modal: true,
                    title: $('a.section_info[id=' + id + ']' ).html(),
                    width: 500,
                    buttons: {
    		    	    'Cancel': function(){
                            $('div#meetings_dialog').dialog('destroy');
                        }
    		    	}
                })
    		}
        )	    
	})
	
})

</script>

<?php
}

?>