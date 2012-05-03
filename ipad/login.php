<?php

require_once( './_header.inc' );

?>

<form action="index.php" method="post">
    <div data-role="fieldcontain">
        <label for="username">Username:</label>
        <input type="text" name="username" id="username" autofocus="autofocus" />
    </div>
    
    <div data-role="fieldcontain">
        <label for="password">Password:</label>
        <input type="password" name="password" id="password" />
    </div>
    
   <button type="submit">Log In</button>
    
</form>

<script type="text/javascript">
$(function(){
    //$('input:visible:enabled:first').focus();
})
</script>

<?php

require_once( './_footer.inc' );

?>
