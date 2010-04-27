<?php

$no_header = 1;
require_once( '../_header.inc' );

if( $_SESSION[ 'admin' ] == 1 ) {
    $section = $db->real_escape_string( $_POST[ 'section' ] );
    if( isset( $_POST[ 'id_to_delete' ] ) ) {
        $id = $db->real_escape_string( $_POST[ 'id_to_delete' ] );
        $delete_query = 'delete from reference '
            . "where id = $id";
        $delete_result = $db->query( $delete_query );
    }
    
    if( isset( $_POST[ 'available' ] ) ) {
        $id = $db->real_escape_string( $_POST[ 'available' ] );
        $avail_query = 'select available from reference '
            . "where id = $id";
        $avail_result = $db->query( $avail_query );
        $avail_row = $avail_result->fetch_assoc( );
        $avail = $avail_row[ 'available' ];
        
        $update_query = 'update reference '
            . 'set available = '
            . ( $avail == 0 ? '1' : '0' )
            . " where id = $id";
        $update_result = $db->query( $update_query );
    }
    
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
        print "    <th>Size</th>\n";
        print "    <th>Uploaded</th>\n";
        print "    <th>Available</th>\n";
        print "    <th>Delete</th>\n";
        print "  </tr>\n";
        print "</thead>\n\n";
        
        print "<tbody>\n";        
        while( $ref = $ref_result->fetch_assoc( ) ) {
            print "  <tr id=\"{$ref[ 'id' ]}\">\n";
            print "    <td><a href=\"javascript:void(0)\" class=\"download\" id=\"{$ref[ 'id' ]}\">{$ref[ 'filename' ]}</a></td>\n";
            print "    <td align=\"right\">{$ref[ 'size' ]}</td>\n";
            print "    <td>" . date( 'M j, Y g:i A', strtotime( $ref[ 'uploaded' ] ) ) . "</td>\n";
            print "    <td><a href=\"javascript:void(0)\" class=\"avail\" id=\"{$ref[ 'id' ]}\">";
            if( $ref[ 'available' ] == 1 ) {
                print 'Yes.';
            } else {
                print 'No.';
            }
            print "</a></td>\n";
            print "    <td><a href=\"javascript:void(0)\" class=\"delete\" id=\"{$ref[ 'id' ]}\">"
                . "<img src=\"$docroot/images/silk_icons/cancel.png\" height=\"16\" "
                . "width=\"16\" alt=\"Delete {$ref[ 'filename' ]}\" /></a></td>\n";
            print "  </tr>\n\n";
        }
        print "</tbody></table>\n";
    }
?>

<script type="text/javascript">
$(document).ready(function(){
    
    $('table.tablesorter').tablesorter( {
        sortList: [ [3,1], [0,0] ], widgets: [ 'phprof' ]
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
    
    $('a.avail').click(function(){
        var id = $(this).attr('id');
        $.post( 'list_reference_materials.php',
            { available: id, section: "<?php echo $section; ?>" },
            function(data){
                $('div#current').html(data);
            }
        )
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