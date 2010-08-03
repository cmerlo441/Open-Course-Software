<?php

$no_header = 1;
require_once( '../_header.inc' );

if( $_SESSION[ 'admin' ] == 1 ) {
    $update_value = $db->real_escape_string( $_POST[ 'update_value' ] );
    $original_html = $db->real_escape_string( $_POST[ 'original_html' ] );
    $element_id = $db->real_escape_string( $_POST[ 'element_id' ] );

    if( trim( $update_value != trim( $original_html ) ) ) {
        $update_value = trim( $update_value );
        if( $element_id == 'college_url' or $element_id == 'department_url' ) {
            if( substr( $update_value, 0, 7 ) != 'http://' ) {
                $update_value = 'http://' . $update_value;
            }
        }
        $update_query = "update prof set {$element_id} = \"$update_value\"";
        $update_result = $db->query( $update_query );
        if( $db->affected_rows == 1 ) {
            if( $element_id == 'suffix' ) {
                if( $update_value == 0 ) {
                    print '(Click here to add)';
                } else {
                    $suffix_query = "select suffix from suffixes where id = $update_value";
                    $suffix_result = $db->query( $suffix_query );
                    $suffix_row = $suffix_result->fetch_assoc();
                    print $suffix_row[ 'suffix' ];
                }
            } else {
                if( $update_value == '' ) {
                    print '(Click here to add)';
                } else {
		    if( preg_match( '/password/', $element_id ) == 1 ) {
		        for( $i = 0; $i < strlen( $update_value ); $i++ ) {
		            print "&bull;";
		        }
		    } else {
		      print $update_value;
		    }
		}
	    }

            $prof_query = 'select * from prof';
            $prof_result = $db->query( $prof_query );
            $prof = $prof_result->fetch_assoc( );
            $prof[ 'name' ] = $prof[ 'first' ] . ' ';
            if( trim( $prof[ 'middle' ] ) != '' ) {
              $prof[ 'name' ] .= $prof[ 'middle' ] . ' ';
            }
            $prof[ 'name' ] .= $prof[ 'last' ];
            if( $prof[ 'suffix' ] != 0 ) {
              $suffix_query = 'select suffix from suffixes '
                . "where id = \"{$prof[ 'suffix' ]}\"";
              $suffix_result = $db->query( $suffix_query );
              $suffix_row = $suffix_result->fetch_assoc();
              $suffix_result->close();
              $prof[ 'name' ] .= ', ' . $suffix_row[ 'suffix' ];
            }
            $prof_result->close();
?>

<script type="text/javascript">
$(document).ready(function(){
    var field = "<?php echo $element_id; ?>";
    if( field == 'first' || field == 'middle' || field == 'last' || field == 'suffix' ) {
        $("div#top div#prof p#name").html("<?php echo $prof[ 'name' ]; ?>");
    } else if( field == 'title' ) {
        $("div#top div#prof p#title").html("<?php echo $prof[ 'title' ]; ?>");
    } else if( field == 'department' || field == 'department_url' ) {
        var url = "<?php echo $prof[ 'department_url' ]; ?>";
        if( url == '' ) {
            $("div#top div#prof p#department").html("Department of <?php echo $prof[ 'department' ]; ?>");
        } else {
            $("div#top div#prof p#department").html("<a href=\"<?php echo $prof[ 'department_url' ]; ?>\">" +
                "Department of <?php echo $prof[ 'department' ]; ?></a>");
        }
    } else if( field == 'college_name' || field == 'college_url' ) {
        var url = "<?php echo $prof[ 'college_url' ]; ?>";
        if( url == '' ) {
            $("div#top div#prof p#college").html("<?php echo $prof[ 'college_name' ]; ?>");
        } else {
            $("div#top div#prof p#college").html("<a href=\"<?php echo $prof[ 'college_url' ]; ?>\">" +
                "<?php echo $prof[ 'college_name' ]; ?></a>");
        }
    } else if( field = 'college_address' ) {
        $("div#top div#prof p#college_address").html("<?php echo $prof[ 'college_address' ]; ?>");
    }

})
</script>

<?php
        } else {
            print ( trim( $original_html ) == '' ? '(Click here to add)' : trim( $original_html ) );
        }
    } else {
        print ( trim( $original_html ) == '' ? '(Click here to add)' : trim( $original_html ) );        
    }
}

?>
