$(function() {
    var baseURL = 'https://schmaltz.local/wp/wp-admin/admin.php?page=edit.php%3Fpost_type%3Dcost_card#costcards';
    //load content for first tab and initialize
    $('#home').load(baseURL+'settings', function() {
        $('#myTabs').tab(); //initialize tabs
    });    
    $('#myTabs').bind('show', function(e) {    
       var pattern=/#.+/gi //use regex to get anchor(==selector)
       var contentID = e.target.toString().match(pattern)[0]; //get anchor         
       //load content for selected tab
        $(contentID).load(baseURL+contentID.replace('#',''), function(){
            $('#myTabs').tab(); //reinitialize tabs
        });
    });
});