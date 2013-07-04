
/**
 * This is an example of how to make an ajax call to a controller
 */

$(document).ready(function() {
	
	$('#ajax').on('click',function(){

		// sample data
		var data = { 'myVar' : 'baby ajax' };

		alert("Var sent to controller: " + data.myVar);

		// to make an ajax call simply point the url to: controller/ajax
		// ajax-call -> ajaxCallAction
		$.ajax({
			url: 'index/ajax-call',
			data: data,
			type:'POST',
			dataType:'json' //makes functionality like $.getJSON
		}).error(function(error){
			//console.log(error); 
			//console.log(error.status); //error code
			//console.log(error.responseText); //error info
		}).success(function(result,status){
			//console.log(result); //already a JSON object
			//console.log(status); // success typeof string

			if (typeof result.html_response != 'undefined') {
				// we recieved an html response, output to screen
				$('#ajax_response').html(result.html_response);
			}

		});
	});

});

// this function will be called before the ajax call is executed
$(document).ajaxStart(function() {
  //console.log('started');
  console.log($(this));
});