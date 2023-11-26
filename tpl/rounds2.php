<?php
lRequired();
$__output = '<div id="ajax"></div>
<script type="text/javascript">
$( document ).ready(function() {
    function loadlink(){
        jQuery.get( "round.html?tid='.$_REQUEST['tid'].'", function( data ) {
            $( "#ajax" ).html( data );
});
      
    }  
    loadlink(); // This will run on page load
    setInterval(function(){
        loadlink() // this will run after every 5 seconds
    }, 5000);
});

</script>';