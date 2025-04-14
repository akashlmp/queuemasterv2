

// table Search
$(document).ready(function(){
    $("#staffAccessInput").on("keyup", function() {
        var value = $(this).val().toLowerCase();
        
        $("#staffAccessTable tbody tr").filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });
  });