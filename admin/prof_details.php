<?php

$title_stub = 'About You';
require_once( '../_header.inc' );

function display( $field, $password = 0 ) {
    if( trim( $field ) == '' or trim( $field ) == '0' ) {
        print '(Click here to add)';
    } else {
      if( $password == 1 ) {
	for( $i = 0; $i < strlen( $field ); $i++ ) {
	  print "&bull;";
	}
      } else {
        print $field;
      }
    }
}

if( $_SESSION[ 'admin' ] == 1 ) {

  if( $prof[ 'suffix' ] > 0 ) {
    $suffix_query = "select suffix from suffixes where id = {$prof[ 'suffix' ]}";
    $suffix_result = $db->query( $suffix_query );
    $suffix_row = $suffix_result->fetch_assoc( );
    $suffix = $suffix_row[ 'suffix' ];
  } else {
    $suffix = '';
  }

  if( $_SESSION[ 'admin' ] == 1 ) {
    print "<p>Click on a field to edit it.</p>\n";
?>

<table id="prof_details">
    <tr>
        <td class="label">Your first name:</td>
        <td><span class="editInPlace" id="first"><?php display( $prof[ 'first' ] ); ?></span></td>
    </tr>
    <tr>
        <td class="label">Your middle name:</td>
        <td><span class="editInPlace" id="middle"><?php display( $prof[ 'middle' ] ); ?></span></td>
    </tr>
    <tr>
        <td class="label">Your last name:</td>
        <td><span class="editInPlace" id="last"><?php display( $prof[ 'last' ] ); ?></span></td>
    </tr>
    <tr>
        <td class="label">Your suffix:</td>
        <td><span class="editInPlace" id="suffix"><?php display( $suffix ); ?></span></td>
    </tr>
    <tr>
        <td class="label">Your e-mail address:</td>
        <td><span class="editInPlace" id="email"><?php display( $prof[ 'email' ] ); ?></span></td>
    </tr>
    <tr>
        <td class="label">Your mobile e-mail address:</td>
        <td><span class="editInPlace" id="mobile_email"><?php display( $prof[ 'mobile_email' ] ); ?></span></td>
    </tr>
    <tr>
        <td class="label">Your title:</td>
        <td><span class="editInPlace" id="title"><?php display( $prof[ 'title' ] ); ?></span></td>
    </tr>

    <tr>
        <td>&nbsp;</td>
    </tr>

    <tr>
        <td class="label">Your Twitter username:</td>
        <td><span class="editInPlace" id="twitter_username"><?php display( $prof[ 'twitter_username' ] ); ?></span></td>
    </tr>
    <tr>
        <td class="label">Your Twitter password:</td>
	<td><span class="editInPlace" id="twitter_password"><?php display( $prof[ 'twitter_password' ], 1 ); ?></span></td>
    </tr>
    
    <tr>
        <td>&nbsp;</td>
    </tr>

    <tr>
        <td class="label">Department Name:</td>
        <td><span class="editInPlace" id="department"><?php display( $prof[ 'department' ] ); ?></span></td>
    </tr>
    <tr>
        <td class="label">Department URL:</td>
        <td><span class="editInPlace" id="department_url"><?php display( $prof[ 'department_url' ] ); ?></span></td>
    </tr>
    <tr>
        <td class="label">College Name:</td>
        <td><span class="editInPlace" id="college_name"><?php display( $prof[ 'college_name' ] ); ?></span></td>
    </tr>
    <tr>
        <td class="label">College URL:</td>
        <td><span class="editInPlace" id="college_url"><?php display( $prof[ 'college_url' ] ); ?></span></td>
    </tr>
    <tr>
        <td class="label">College Physical Address:</td>
        <td><span class="editInPlace" id="college_address"><?php display( $prof[ 'college_address' ] ); ?></span></td>
    </tr>
</table>

<script type="text/javascript">
$(document).ready(function(){
    var suffixes;

    $.post( "<?php echo $docroot; ?>/serialize_suffixes.php",
        function(data){
            suffixes = data;
            $("span.editInPlace#suffix").editInPlace({
                url: 'update_prof.php',
                params: 'ajax=yes',
                field_type: 'select',
                select_options: suffixes,
        		saving_image: "<?php echo $docroot; ?>/images/ajax-loader.gif"
            })
        }
    )

    $("span.editInPlace:not([id=suffix])").editInPlace({
        url: 'update_prof.php',
        params: 'ajax=yes',
		saving_image: "<?php echo $docroot; ?>/images/ajax-loader.gif"
    });

})
</script>

<?php
    }

} else {
  print $no_admin;
}

?>
