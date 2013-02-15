<?php

// The thing I'm trying to get rid of is div#px-form-1

$title_stub = 'Reference Materials';
require_once( '../_header.inc' );

if( $_SESSION[ 'admin' ] == 1 ) {
    $section = $db->real_escape_string( $_GET[ 'section' ] );

    print "<div class=\"dialog\" id=\"info\"></div>\n";

    // What class is this?
    
    $course_query = 'select c.id as course_id, c.dept, c.course, s.section '
        . 'from courses as c, sections as s '
        . 'where s.course = c.id '
        . "and s.id = $section";
    $course_result = $db->query( $course_query );
    $course_row = $course_result->fetch_assoc( );
    $course_name = $course_row[ 'dept' ] . ' ' . $course_row[ 'course' ] . ' '
        . $course_row[ 'section' ];
    
    print "<h2>Upload New Reference Material</h2>\n";
    print "<div id=\"file_upload\" style=\"text-align: center; padding: 0 auto;\">\n";
?>

	<link href="../file-upload/css/fileUploader.css" rel="stylesheet" type="text/css" />
	<script src="../file-upload/js/jquery.fileUploader.js" type="text/javascript"></script>
	<form action="reference_upload.php?section=<?php echo $section; ?>" method="post" enctype="multipart/form-data">
		<input type="file" name="file" class="fileUpload" width="100%" multiple>
		
		<!-- <button id="px-submit" type="submit">Upload</button>
		<button id="px-clear" type="reset">Clear</button> -->
	</form>
<?php
    
    print "</div>  <!-- div#file_upload -->\n";
    
    print "<h2>Current Reference Materials</h2>\n";
    print "<div id=\"current\"></div>\n";
    
?>

<script type="text/javascript">
$(document).ready(function(){
    var course = " :: <?php echo $course_name; ?>";
    
    $('h1').html( $('h1').html( ) + course );
    $(document).attr('title', $(document).attr('title') + course );
    
    $.post( 'list_reference_materials.php',
        { section: "<?php echo $section; ?>" },
        function( data ) {
            $('div#current').html(data);
        }
    )
    
	var dragTimer;
	$(document).on('dragover', function(e) {
	    var dt = e.originalEvent.dataTransfer;
	    var text =  $('.fileUpload').parent().text();
	    if(dt.types != null && (dt.types.indexOf ? dt.types.indexOf('Files') != -1 : dt.types.contains('application/x-moz-file'))) {
	        $(".fileUpload").parent().css('background-color','#5d562c');
	        window.clearTimeout(dragTimer);
	    }
	});
	$(document).on('dragleave', function(e) {
	    dragTimer = window.setTimeout(function() {
	        $(".fileUpload").parent().css('background-color','transparent');
	        }, 25);
	});

    $('.fileUpload').fileUploader({
    	allowedExtension: 'pdf|doc|docx|xls|xlsx|ppt|pptx|txt|png|jpg|jar',
    	autoUpload: true,
    	beforeEachUpload: function(form){
    	    for( var key in form ) {
    	        if( form.hasOwnProperty( key ) )
    	           console.log( key + ': ' + form[key]);
    	    }
    	},
    	selectFileLabel: "Drag files here, or click this button to choose files",
    	afterEachUpload: function(data){
    		$.post( 'list_reference_materials.php',
		        { section: "<?php echo $section; ?>" },
		        function( data ) {
		            $('div#current').html(data);
		        }
		    );
		    $.pnotify({
                pnotify_title: 'File Uploaded',
                pnotify_text: 'Your file has been uploaded.',
                pnotify_shadow: true
            });
            console.log( data );
    	}
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