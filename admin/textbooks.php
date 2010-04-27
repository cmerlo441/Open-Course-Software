<?php

$title_stub = "Textbooks";
require_once( '../_header.inc' );

if( $_SESSION[ 'admin' ] == 1 ) {
    
    $publisher_query = 'select * from publishers order by name';
    $publisher_result = $db->query( $publisher_query );
    $count = 0;
    $publishers = array( );
    while( $row = $publisher_result->fetch_assoc( ) ) {
        foreach( explode( ' ', 'id name url' ) as $field ) {
            $publishers[ $count ][ $field ] = $row[ $field ];
        }
        $count++;
    }
    $publisher_result->close();
    
    print "<div id=\"current_textbooks\"></div>\n";
?>

<h3>Add A New Textbook</h3>
<div id="add_new_textbook">
<table>
    <tr>
        <td>Title:</td>
        <td><input type="text" id="new_title" /></td>
    </tr>
    <tr>
        <td>Subtitle:</td>
        <td><input type="text" id="new_subtitle" /></td>
    </tr>
    <tr>
        <td>Edition:</td>
        <td>
            <table>
                <tr>
                    <td><div class="slider" id="edition_slider" style="width:100px"></div></td>
                    <td style="padding-left: 1em"><input type="text" id="edition_text" size="2" /></td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td>Year:</td>
        <td>
            <table>
                <tr>
                    <td><div class="slider" id="year_slider" style="width:100px"></div></td>
                    <td style="padding-left: 1em"><input type="text" id="year_text" size="4" /></td>
                </tr>
            </table>
        </td>
    <tr>
        <td>Publisher:</td>
        <td><select id="publisher">
<?php
foreach( $publishers as $publisher ) {
    print "            <option value=\"{$publisher[ 'id' ]}\">{$publisher[ 'name' ]}</option>\n";
}
?>
            </select></td>
    </tr>
    <tr>
        <td>ISBN:</td>
        <td><input type="text" id="isbn" /></td>
    </tr>
    <tr>
        <td colspan="2" align="center"><input type="submit" id="new_textbook" value="Add This Textbook" /></td>
    </tr>
</table>
</div>  <!-- div#add_new_textbook -->

<script type="text/javascript">
$(document).ready(function(){
    $.post( 'list_textbooks.php',
        function(data){
            $("div#current_textbooks").html(data);
        }
    )
    
    $("input#new_textbook").click(function(){
        var title = $("input#new_title").val();
        var subtitle = $("input#new_subtitle").val();
        var edition = $("input#edition_text").val();
        var year = $("input#year_text").val();
        var publisher = $("select#publisher").val();
        var isbn = $("input#isbn").val();
        $.post('list_textbooks.php',
            { title: title, subtitle: subtitle, edition: edition, year: year,
              publisher: publisher, isbn: isbn },
            function(data){
                $("div#current_textbooks").html(data);
        })
        $("input#new_title").val("");
        $("input#new_subtitle").val("");
        $("div#edition_slider").slider('value',1);
        $("input#edition_text").val("1");
        $("div#year_slider").slider('value', "<?php echo date( 'Y' ); ?>");
        $("input#year_text").val("<?php echo date( 'Y' ); ?>");
        $("input#isbn").val("");
    })
    
    $("div#year_slider").slider({
        value: "<?php echo date( 'Y' ); ?>",
        min: 1950,
        max: "<?php echo date( 'Y' ) + 2; ?>",
        slide: function(event,ui){
            $("#year_text").val(ui.value);
        }
    })
    $("#year_text").val($("#year_slider").slider("value"));

    $("div#edition_slider").slider({
        value: 1,
        min: 1,
        max: 20,
        slide: function(event,ui){
            $("#edition_text").val(ui.value);
        }
    })
    $("#edition_text").val($("#edition_slider").slider("value"));
})
</script>

<?php
    
} else {
    print $no_admin;
}

$lastmod = filemtime( $_SERVER[ 'SCRIPT_FILENAME' ] );
include( "$fileroot/_footer.inc" );

?>
