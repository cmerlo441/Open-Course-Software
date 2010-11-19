<?php

$no_header = 1;
require_once( '../_header.inc' );

if( $_SESSION[ 'admin' ] == 1 ) {
    $section = $db->real_escape_string( $_POST[ 'section' ] );
    
    $ref_query = 'select * from reference '
        . "where section = $section "
        . 'order by filename, available desc';
    $ref_result = $db->query( $ref_query );
    if( $ref_result->num_rows == 0 ) {
        print 'None.';
    } else {
        
        print "<table class=\"tablesorter\" id=\"reference\">\n";
        print "<thead>\n";
        print "  <tr>\n";
        print "    <th>File</th>\n";
        print "    <th>Uploaded</th>\n";
        print "    <th>Available</th>\n";
	print "    <th>Info</th>\n";
        print "  </tr>\n";
        print "</thead>\n\n";
        
        print "<tbody>\n";        
        while( $ref = $ref_result->fetch_assoc( ) ) {

            print "  <tr id=\"{$ref[ 'id' ]}\">\n";
            print "    <td class=\"title\"><a href=\"javascript:void(0)\" "
		. "class=\"download\" id=\"{$ref[ 'id' ]}\">"
		. "<span class=\"title\">{$ref[ 'filename' ]}</span></a>"
		. "</td>\n";
            print "    <td class=\"date\">"
		. date( 'M j, Y g:i A', strtotime( $ref[ 'uploaded' ] ) )
		. "</td>\n";
            print "    <td class=\"available\">"
		. "<a href=\"javascript:void(0)\" class=\"avail\" "
		. "id=\"{$ref[ 'id' ]}\"><span class=\"available\">";
            if( $ref[ 'available' ] == 1 ) {
                print 'Yes.';
            } else {
                print 'No.';
            }
            print "</span></a></td>\n";

	    /*
            print "    <td><a href=\"javascript:void(0)\" "
		. "class=\"delete\" id=\"{$ref[ 'id' ]}\">"
                . "<img src=\"$docroot/images/silk_icons/cancel.png\" "
		. "height=\"16\" width=\"16\" "
		. "alt=\"Delete {$ref[ 'filename' ]}\" /></a></td>\n";
	    */

	    print "    <td>"
		. "<a href=\"javascript:void(0)\" class=\"info\" "
		. "id=\"{$ref[ 'id' ]}\">Info</a></td>\n";

            print "  </tr>\n\n";
        }
        print "</tbody></table>\n";
    }
?>

<script type="text/javascript">
$(document).ready(function(){
    
    $('table.tablesorter').tablesorter( {
        sortList: [ [3,1], [0,0] ], widgets: [ 'ocsw' ]
    })
    
    $('a.delete').click(function(){
        var id = $(this).attr('id');
        $.post( 'list_reference_materials.php',
            { id_to_delete: id, section: "<?php echo $section; ?>" },
            function( data ) {
                $('div#current').html(data);
            }
        )
    })
    
    function toggle_availability( id ) {
        $.post( 'update_reference.php',
            { available: id }
        );

        if( $('div#available > span.available')
            .html() == 'Available for download' ) {
            $('div#available > span.available')
                .html( 'Unavailable for download' );
            $('tr#' + id + ' td.available span').html( 'No.' );
        } else {
            $('div#available > span.available')
                .html( 'Available for download' );
            $('tr#' + id + ' td.available span').html( 'Yes.' );
        }
    }

    $('a.avail').click(function(){
        var id = $(this).attr('id');

        $.post( 'update_reference.php',
            { available: id }
        );

        if( $('tr#' + id + ' td.available span').html() == 'Yes.' )
            $('tr#' + id + ' td.available span').html( 'No.' );
        else
            $('tr#' + id + ' td.available span').html( 'Yes.' );
    })

    $('a.info').click(function(){
        var id = $(this).attr('id');
	var title = $('tr[id=' + id + '] span.title').html();
	var available = $('tr[id=' + id + '] span.available').html();

	$.post('reference_more_info.php',
        { id: id },
        function( data ) {
	    $('div#info').html(data).dialog({
		buttons: {
		    "OK": function(){
			$(this).dialog( 'close' );
		    },
		    'Toggle Availability': function() {
		        toggle_availability( id );
		    }
                },
                modal: true,
                title: title,
                width: 500
	    });
	})
    })

    $('a.download').click(function(){
        var id = $(this).attr('id');
        window.location = "<?php echo $docroot; ?>/download_reference_material.php?id=" + id;
    })
})
</script>

<?php

}
?>