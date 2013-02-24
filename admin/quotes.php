<?php

$title_stub = 'Quotes';
require_once( '../_header.inc' );

if( $_SESSION[ 'admin' ] == 1 ) {

?>

<div id="search">
  <label for="search_string">Search for a quote:</label>
  <input type="text" id="search_string" />
</div>

<div class="dialog" id="edit_quote"></div>
<div id="quotes"></div>

<script type="text/javascript">

$(document).ready(function(){
    $.get( 'quotes_data.php',
    	function(data){
    	    $('div#quotes').html(data);
            $('a.edit').click(function(){
                var id = $(this).attr('id').substring(5);
                $.post('quote.php',
                    { id: id },
                    function(data){
                        $('div#edit_quote').html(data).dialog({
                            autoOpen: true,
                            hide: 'puff',
                            modal: true,
                            title: "Edit This Quote",
                            width: 600,
                            buttons:[{
                                text: "Save Changes",
                                click: function(){
                                    var quote = $('div#edit_quote > textarea#quote').val();
                                    var attribution = $('div#edit_quote > input#attribution').val();
                                    console.log(quote + "\n" + attribution);
                                    $.post('quotes_data.php',
                                        {
                                            edit: id,
                                            quote: quote,
                                            attribution: attribution
                                        },
                                        function(data){
                                            $('div#quotes').html(data);
                                        }
                                    );
                                    $('div#edit_quote').dialog("destroy");
                                }
                            }]
                        });
                    }
                )
            })
        }
    )

    $('input#search_string').focus().keyup(function(){
        var search = $('input#search_string').val();

        if( search.length < 2 )
            search = "";

        $.post( 'quotes_data.php',
            { search: search },
            function(data){
                $('div#quotes').html(data);
            $('a.edit').click(function(){
                var id = $(this).attr('id').substring(5);
                $.post('quote.php',
                    { id: id },
                    function(data){
                        $('div#edit_quote').html(data).dialog({
                            autoOpen: true,
                            hide: 'puff',
                            modal: true,
                            title: "Edit This Quote",
                            width: 600,
                            buttons:[{
                                text: "Save Changes",
                                click: function(){
                                    var quote = $('div#edit_quote > textarea#quote').val();
                                    var attribution = $('div#edit_quote > input#attribution').val();
                                    console.log(quote + "\n" + attribution);
                                    $.post('quotes_data.php',
                                        {
                                            edit: id,
                                            quote: quote,
                                            attribution: attribution
                                        },
                                        function(data){
                                            $('div#quotes').html(data);
                                        }
                                    );
                                    $('div#edit_quote').dialog("destroy");
                                }
                            }]
                        });
                    }
                )
            })
            }
        )
    })

})

</script>

<?php

} else {
    print $no_admin;
}

$lastmod = filemtime( $_SERVER[ 'SCRIPT_FILENAME' ] );
include( "$fileroot/_footer.inc" );

?>