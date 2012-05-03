<?php

require_once( './_header.inc' );

?>

    <nav>

<?php
if( $_SESSION[ 'admin' ] == 1 ) {
?>
    <ul data-role="listview" data-inset="true" id="admin">
        <li data-role="list-divider">Professor Tools</li>
        <li><a href="<?php echo $docroot; ?>/admin/current_sections.php">Current Sections</a></li>

        <li><a href="index.php?logout=true">Log Out</a></li>
    </ul>
<?php
} else {
?>
    <ul data-role="listview" data-inset="true">
        <li data-role="list-divider">Accounts</li>
        <li><a href="login.php">Log In</a></li>
        <li><a href="create_account.php">Create Account</a></li>
    </ul>
<?php
}
?>
    
    <ul data-role="listview" data-inset="true">
        <li data-role="list-divider">Other Information</li>
        <li><a href="schedule.php">My Schedule</a></li>
        <li><a href="contact.php">Contact Information</a></li>
    </ul>

</nav>

<?php

require_once( './_footer.inc' );

?>
