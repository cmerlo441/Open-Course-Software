<?php

$title_stub = 'Remove Student Data';
require_once( '../_header.inc' );

if( $_SESSION[ 'admin' ] == 1 ) {
    print wordwrap( '<p>Use this tool if a student has started trying to create '
        . 'an account on your site, but entered an invalid e-mail address '
        . 'or otherwise is incapable of receiving e-mail at the address '
        . 'he or she provided.</p>' ) . "\n";
    print wordwrap( '<p><span style="font-weight: bold">Please note</span>: '
        . 'This tool will delete all data about the student whose ID number '
        . 'you enter, so be sure you have the right student.</p>' ) . "\n";
        
    print "<p>Student's ID number: "
        . "<input type=\"text\" id=\"banner\" title=\"Student's ID number\" />\n";
    print "<input type=\"submit\" id=\"search\" value=\"Find student\" /></p>\n";
    
    print "<div id=\"student_to_remove\" style=\"text-align: center; display: none;\"></div>\n";
?>

<script type="text/javascript">

$(document).ready(function(){

    $('input#banner').focus();
	
	$('input#search').click(function(){
		var id = $('input#banner').val().trim();
		
		$.post( 'find_student_to_remove.php',
			{ id: id },
			function(data){
				$('div#student_to_remove')
				    .css('padding', '1em')
			        .css('border', '1px solid white')
			        .html(data)
			        .fadeIn();
		        if( data.indexOf( 'Has Been Set' ) != -1 ) {
                    $('div#student_to_remove').css('background-color','#f22');
			        $.pnotify({
				        pnotify_title: 'Password Has Been Set',
				        pnotify_text: 'You may not want to delete this student\'s data.',
				        pnotify_shadow: true,
				        pnotify_type: 'error'
			        });
		        } else {
                    $('div#student_to_remove').css('background-color','#5d562c')
                }
			}
		)
	})
})

</script>

<?php

}

?>