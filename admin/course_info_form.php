<?php

$no_header = 1;
require_once( '../_header.inc' );

if( $_SESSION[ 'admin' ] == 1 ) {

    $id = $db->real_escape_string( $_POST[ 'course' ] );
    $course_query = 'select id, dept, course, credits, short_name, long_name, '
	. 'prereq, catalog, outline '
	. 'from courses '
	. "where id = $id";
    $course_result = $db->query( $course_query );
    $row = $course_result->fetch_assoc( );

?>

<table style="margin: auto">
  <tr>
    <td>Department</td>
    <td><input type="text" id="dept" size="5"
      value="<?php echo $row[ 'dept' ]; ?>" /></td>
  </tr>

  <tr>
    <td>Course</td>
    <td><input type="text" id="course" size="5"
      value="<?php echo $row[ 'course' ]; ?>" /></td>
  </tr>

  <tr>
    <td>Credits</td>
    <td><input type="text" id="credits" size="5"
      value="<?php echo $row[ 'credits' ]; ?>" /></td>
  </tr>

  <tr>
    <td>Short Name</td>
    <td><input type="text" id="short_name" size="25"
      value="<?php echo $row[ 'short_name' ]; ?>" /></td>
  </tr>

  <tr>Long Name</td>
    <td><input type="text" id="long_name" size="40"
      value="<?php echo $row[ 'long_name' ]; ?>" /></td>
  </tr>

  <tr>
    <td>Prerequisites</td>
    <td><textarea id="prereq" rows="3" cols="60"><?php
         echo $row[ 'prereq' ]; ?></textarea></td>
  </tr>

  <tr>
    <td>Catalog Description</td>
    <td><textarea id="catalog" rows="3" cols="60"><?php
         echo $row[ 'catalog' ]; ?></textarea></td>
  </tr>

  <tr>
    <td>Course Outline</td>
    <td><textarea id="outline" rows="6" cols="60"><?php
         echo $row[ 'outline' ]; ?></textarea></td>
  </tr>

</table>

<button id="delete" style="align: center">Delete This Course</button>

<script type="text/javascript">
$(document).ready(function(){

    $('button#delete').button();

})
</script>

<?php

}

?>