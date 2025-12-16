// When the page is loaded
$(document).ready(function(){

    // set our default option to id=0 when page loads to reset after return/reload
    $('#select_parent option[id="0"]').attr("selected",true);
    
    // When our select changes
    $("select").on( "change", function(){
        
        console.log(window.location.href);

        // Get the selected options target page and target anchor
        var target_page = $('option:selected', this).attr('data-target-page');
        var target_anchor = $('option:selected', this).attr('data-target-anchor');
        
        //Removing slash version that was causing crashes
        //var buffer = '/';
        var buffer = '/';
        
        
        if(target_page !== '')
            buffer += target_page;
        
        // True or false if the current address includes the address we are trying to forward to
        var same_page = window.location.href.includes(buffer);
        
        if(target_anchor !== '')
            buffer += "#" + target_anchor;

        // Build our link and redirect to it
        //window.location.href = buffer;
        
        // Redirect in a way that passes information needed for Google Tag Manager or whatever
        window.open(buffer,'_self');
        window.dataLayer = window.dataLayer || [];
        dataLayer.push({'event':'homepage-navigation'});
        
        // if the target is on the same page, force a reload
        if(same_page)
            window.location.reload();
    });
    
    
});
