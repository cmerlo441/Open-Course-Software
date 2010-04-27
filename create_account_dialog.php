<?php

$no_header = 1;
require_once( './_header.inc' );

?>

<div class="ui-state-error ui-corner-all" id="create_error" style="display: none; margin-bottom: 1em;">
    <table><tr>
        <td><span class="ui-icon ui-icon-alert"></span></td>
        <td><strong>Alert:</strong> You must fill in all required fields.</td>
    </tr></table>
</div><!-- div.ui-state-error#create_error -->

<table>
    <tr>
        <td id="first">First&nbsp;Name</td>
        <td><input type="text" name="first" id="first" class="text ui-widget-content ui-corner-all" /></td>
    </tr>
    <tr>
        <td>Middle&nbsp;Name&nbsp;(Optional)</td>
        <td><input type="text" name="middle" id="middle" class="text ui-widget-content ui-corner-all" /></td>
    </tr>
    <tr>
        <td id="last">Last&nbsp;Name</td>
        <td><input type="text" name="last" id="last" class="text ui-widget-content ui-corner-all" /></td>
    </tr>
    <tr>
        <td id="email">E-Mail&nbsp;Address</td>
        <td><input type="text" name="email" id="email" class="text ui-widget-content ui-corner-all" /></td>
    </tr>
    <tr>
        <td id="banner">MyNCC&nbsp;ID</td>
        <td><input type="text" name="banner" id="banner" class="text ui-widget-content ui-corner-all" /></td>
    </tr>
</table>
<br />

<div class="ui-state-error ui-corner-all" id="courses_error" style="display: none; margin-bottom: 1em;">
    <table><tr>
        <td><span class="ui-icon ui-icon-alert"></span></td>
        <td><strong>Alert:</strong> You must be in at least one class.</td>
    </tr></table>
</div><!-- div.ui-state-error#create_error -->

<p id="courses" style="text-align: center">Which Class(es) Are You Taking?</p>
<br />

<?php
$sections_query = 'select c.dept, c.course, c.short_name as title, s.section, s.id '
    . 'from courses as c, sections as s '
    . 'where s.course = c.id '
    . 'order by c.dept, c.course, s.section';
$sections_result = $db->query( $sections_query );
while( $section = $sections_result->fetch_assoc( ) ) {
    print "<input type=\"checkbox\" name=\"{$section[ 'id' ]}\" id=\"{$section[ 'id' ]}\" "
        . "class=\"checkbox ui-widget-content ui-corner-all\" />&nbsp;"
        . "{$section[ 'dept' ]} {$section[ 'course' ]} {$section[ 'section' ]}: {$section[ 'title' ]}<br />\n";
}

?>
</fieldset>
</form>