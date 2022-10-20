
$(document).ready(function() {
    $(".schedule input").attr("disabled", "disabled").off('click');
})

//jQuery('#datetimepicker').datetimepicker({
//  format:'unixtime'
//});

$('#sinfo').click(function(){
            $("#schedform").toggle();
            $("#tform").hide();
            $("#pform").hide();
            $("#hform").hide();
    
});
$('#pinfo').click(function(){
            $("#pform").toggle();
            $("#tform").hide();
            $("#schedform").hide();
            $("#hform").hide();

});
$('#tinfo').click(function(){
            $("#tform").toggle();
            $("#pform").hide();
            $("#schedform").hide();
            $("#hform").hide();

});

$('#hostbtn').click(function(){
            $("#hform").toggle();
            $("#tform").hide();
            $("#pform").hide();
            $("#schedform").hide();

});


function scheduleAmatch(){
    if($('#steam1').val()==$('#steam2').val()){
        
        $('#schedform .error').html("<span class='alert alert-danger'>Two teams can't be same.</span>");
         setTimeout(function() {
         $('#schedform .error').html("");
    }, 3000);
        
    }else{
        $(".schedule input").removeAttr("disabled");
    }
}

$('#prsubmit').click(function(e){
   e.preventDefault(); 
});


$('form#tf').submit(function (e) {
	e.preventDefault();
	updateTeamData(this);
});

$('form#pf').submit(function (e) {
	e.preventDefault();
	updateplayerData(this);
});

$('form#sf').submit(function (e) {
	e.preventDefault();
	updateMatchSchedule(this);
});


function updateMatchSchedule(form) {
    var form_data = new FormData(form);
    
//    for (var value of form_data.values()) {
//   console.log(value);                             
    $.ajax({
        url: 'update.php', // point to server-side PHP script 
        dataType: 'text',  // what to expect back from the PHP script, if anything
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,                         
        type: 'post',
        success: function(res){
//            alert(php_script_response); // display response from the PHP script, if any
        }
     });
};

function updateplayerData(form) {
    var file_data = $('#img').prop('files')[0];   
    var form_data = new FormData(form);                  
    form_data.append('file', file_data);
    $.ajax({
        url: 'update.php', // point to server-side PHP script 
        dataType: 'text',  // what to expect back from the PHP script, if anything
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,                         
        type: 'post',
        success: function(res){
//            alert(php_script_response); // display response from the PHP script, if any
        }
     });
};

function updateTeamData(form) {
    var form_data = new FormData(form);
                                
    $.ajax({
        url: 'update.php', // point to server-side PHP script 
        dataType: 'text',  // what to expect back from the PHP script, if anything
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,                         
        type: 'post',
        success: function(res){
//            alert(php_script_response); // display response from the PHP script, if any
        }
     });
};


//Countdown which needs to be implemented
function matchtimer(timestamp){
    
    
}

function hostmatch(val){
    console.log(val);
    window.location.href = "hostMatchSession.php?mid="+val;
}

