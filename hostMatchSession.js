


function getIndex(arr, ele){
 for(var i=0; i<arr.length; i++){
   if(arr[i]==ele){
     return i;
   }
 }
}

$(document).ready(function(){
//    $('#electedtoss').addClass('disable-div');

    var conn = new WebSocket('ws://localhost:8080');
    conn.onopen = function(e) {
        console.log("Connection established!");
    };

    conn.onmessage = function(e) {
        console.log(e.data);
    };

//    $("#sub").click(function(){
//        var s = $("#post").val()
//       conn.send(s) 
//    });
    
    


$('.teambtn').click( function(){
    $(this).addClass('wontoss');
});

$('#tossbtn').click( function(){
    $('#playersid').show();
    
       var whoWonTheToss = document.querySelector('.wontoss').getAttribute("value");
       var electedTo =     $(".elecbtn input[type='checkbox']").val();
    
    $.each($(".elecbtn input[type='checkbox']:checked"), function(){
                electedTo = ($(this).val());
            });
    
      $.ajax({
        url: 'update.php', 
        type: 'post',
        data: {toss: whoWonTheToss, electedTo: electedTo, mid: $('#midval').val()},                         
        success: function(res){
            $('.Tossdiv').addClass('disable-div');
            
            var wontossTeamName = ($('.wontoss').text()).trim();
            var elected='';
            if(electedTo==0){
                elected = "bowl"
            }else{
                elected = "bat"
            }
//            conn.send(wontossTeamName+" won the toss and have elected to "+elected+" first");
            var toss = {}
            toss['teamToss'] = wontossTeamName;
            toss['elected'] = elected;
            toss['step'] = 'toss';
            toss['mid'] = $('#midval').val();

            conn.send(JSON.stringify(toss));
        }
     });
    
    
    
});


///Selectable Jquery
  $( function() {
    $( ".selectablet1" ).selectable({
      stop: function() {
        var result = $( "#select-result" ).empty();
        $( ".ui-selected", this ).each(function() {
          var index = $( ".selectablet1 li" ).index( this );
          result.append( " ." + ( index + 1 ) );
            
        });
      }
    });
  } );


  $( function() {
    $( ".selectablet2" ).selectable({
      stop: function() {
        var result = $( "#select-result" ).empty();
        $( ".ui-selected", this ).each(function() {
          var index = $( "#selectablet2 li" ).index( this );
          result.append( " #" + ( index + 1 ) );
        });
      }
    });
  } );

var team1pid = new Array();
var team1pname = new Array();

var team2pid = new Array();
var team2pname = new Array();

$('#selpro').click( function(){

    $(".selectablet1 .ui-selected").each(function() {
        team1pid.push($(this).val());
        team1pname.push($(this).text());
    });

     $(".selectablet2 .ui-selected").each(function() {
            team2pid.push($(this).val());
            team2pname.push($(this).text());
        });
    
    
    if(team1pid.length==11 && team2pid.length==11){
        //Have to add commentary
        var playing11 = {}
        playing11['t1'] = team1pid;
        playing11['t2'] = team2pid;
        playing11['t1name'] = $('#team1').val()
        playing11['t2name'] = $('#team2').val()
        
        playing11['mid'] = $('#midval').val();
        playing11['step'] = 'team';
        
        conn.send(JSON.stringify(playing11));
        
        if(($('.wontoss').attr('value') == '1' && $(".elecbtn input[type='checkbox']").val()=='1') || ($('.wontoss').attr('value') == '2' && $(".elecbtn input[type='checkbox']").val()=='0')){
          for (var i = 0; i < team1pid.length; i++) {
            $('#strikeOp').append('<option value="'+team1pid[i]+'">'+team1pname[i]+'</option>');
            $('#nstrikeOp').append('<option value="'+team1pid[i]+'">'+team1pname[i]+'</option>');
            $('#bowler').append('<option value="'+team2pid[i]+'">'+team2pname[i]+'</option>');
          }
        }else{
          for (var i = 0; i < team1pid.length; i++) {
            $('#strikeOp').append('<option value="'+team2pid[i]+'">'+team2pname[i]+'</option>');
            $('#nstrikeOp').append('<option value="'+team2pid[i]+'">'+team2pname[i]+'</option>');
            $('#bowler').append('<option value="'+team1pid[i]+'">'+team1pname[i]+'</option>');
          }
        }

        $('#startInning').show();
        $('#playersid').hide();
    }

});
function updateScorecard(strike, nstrike, bowler, mid, type){

  if(type=="legal"){
    var scoreCard = {}
      scoreCard['strike'] = strike;
      scoreCard['nstrike'] = nstrike;
      scoreCard['bowler'] = bowler;
      scoreCard['mid'] = mid;
      scoreCard['step'] = 'scorecard';
      
      conn.send(JSON.stringify(scoreCard));
  }
  

}

//Match Starting....
$('#stpro').click( function(){

   var strike = $('#strikeOp').val();
   var nstrike = $('#nstrikeOp').val();
   var bowler = $('#bowler').val();
   var mid = $('#midval').val();

      var matchSessionStart = {}
      matchSessionStart['strike'] = strike;
      matchSessionStart['nstrike'] = nstrike;
      matchSessionStart['bowler'] = bowler;
      matchSessionStart['mid'] = mid;
      matchSessionStart['step'] = 'startSession';
      
      conn.send(JSON.stringify(matchSessionStart));
      strikebatname = '';
      nstrikebatname = '';
      bowlerName ='';

            if(team1pid.indexOf(strike)==-1){
              strikebatname = team2pname[getIndex(team2pid, strike)];
              nstrikebatname = team2pname[getIndex(team2pid, nstrike)];
              bowlerName = team1pname[getIndex(team1pid, bowler)];

            }else{
              strikebatname = team1pname[getIndex(team1pid, strike)];
              nstrikebatname = team1pname[getIndex(team1pid, nstrike)];
              bowlerName = team2pname[getIndex(team2pid, bowler)];

            }
            $('#strikeBat').val(strike);
            $('#strikeBat').attr('data-pname', strikebatname);

            $('#nstrikeBat').val(nstrike);
            $('#nstrikeBat').attr('data-pname', nstrikebatname);

            $('#bowlerID').val(bowler);
            $('#bowlerID').attr('data-pname', bowlerName);
            
            $('.summary .str').html(strikebatname)
            $('.summary .nstr').html(nstrikebatname)


      $('#playMatch').show();
      $('#startInning').hide();
});



//var out = 0;
//var bowlCategory = 'empty';
    var wkcat = 0;
    var runsThrough = 0;
    var runScoredInthisBall = 0;
    var runOutPlayer = 0;
    var nextBatsman = 0;
    var nextBowler = 0;
    var strikeBatsmanId = $('#strikeBat').val();
    var strikeBatsmanName =  $('#strikeBat').attr('data-pname');
    var nonstrikeBatsmanId = $('#nstrikeBat').val();
    var nonstrikeBatsmanName =  $('#nstrikeBat').attr('data-pname');

function rotateStrike(){
    var temp = $('#strikeBat').val();
    var temp1 = $('#strikeBat').attr('data-pname');

    $('#strikeBat').val($('#nstrikeBat').val());
    $('#strikeBat').attr('data-pname', $('#nstrikeBat').attr('data-pname'));

    $('#nstrikeBat').val(temp);
    $('#nstrikeBat').attr('data-pname', temp1);

    $('.summary .str').html($('#strikeBat').attr('data-pname'));
    $('.summary .nstr').html($('#nstrikeBat').attr('data-pname'));
   } 

$('#bowlcat input').on('change', function() {
    bowlCategory = $('#bowlcat input[name=bcat]:checked').val();
    
    if(bowlCategory!='legal'){
        legalDelivery = 0;
    }else{
        legalDelivery = 1;
    }

});    
    
    
$('#out input').on('change', function() {
  isBatsmanOut = $('#out input[name=batout]:checked').val();
  if(isBatsmanOut==1){
    
    $('#wkcat').show();
    if(bowlCategory == 'noball'){
      $('.bowled, .caught, .lbw, .htwkt').prop( "disabled", true );
    }
    $('#runs').hide();
    $('#runsThrough').hide();
  }else{
    $('#runs').show();
    $('#wkcat').hide();
  }
  
});

$('#wkcat input').on('change', function() {
  wkcat = $('#wkcat input[name=wkcat]:checked').val();
  if(wkcat=='caught'){
    $('#whoCaught').show();
    $('#runOut').hide();
    $('#runOutPlayer').hide();
    
  }else if(wkcat=='ro'){
      $('#runOutPlayer .p1 span label').html('<input type="radio" name="runOutPlayer" value="'+$('#strikeBat').val()+'"> '+$('#strikeBat').attr('data-pname'));
      $('#runOutPlayer .p2 span label').html('<input type="radio" name="runOutPlayer" value="'+$('#nstrikeBat').val()+'"> '+$('#nstrikeBat').attr('data-pname'));
    
      $('#runOutPlayer').show();
    $('#runOut').show();
    $('#whoCaught').hide();
    $('#runsThrough').hide();
  }

});

$('#runs input').on('change', function() {
  runScoredInthisBall = $('#runs input[name=runs]:checked').val();
  if(runScoredInthisBall != '0'){
      $('#runsThrough').show();
  }else{
    $('#runsThrough').hide();
  }

});
$(document).on('change', '#runOutPlayer input', function() {
runOutPlayer = $('#runOutPlayer input[name=runOutPlayer]:checked').val();
});


//New Batsman Entry..
$(document).on('change', '#strikeChanged input', function() {
  strikeChanged = $('#strikeChanged input[name=strikeChanged]:checked').val();
  });

  
//Batsmen..
  $(document).on('change', '#strikeBat', function() {
    strikeBatsmanId = $('#strikeBat').val();
    strikeBatsmanName = $('#strikeBat').attr('data-pname');
    });
  $(document).on('change', '#nstrikeBat', function() {
      nonstrikeBatsmanId = $('#nstrikeBat').val();
      nonstrikeBatsmanName = $('#sntrikeBat').attr('data-pname');
      });

//Select Next Batsman..  
$(document).on('change', '#nextBatsman select', function() {
  nextBatsman = $(this).val();

  if((wkcat=='caught' || wkcat == 'ro') && nextBatsman!=0){
    $('#strikeChanged').show();
  }
  });

//Select Next Bowler..  
$(document).on('change', '#nextOver select', function() {
  nextBowler = $(this).val();
  });



//LegBye/Bye
$('#runsThrough input').on('change', function() {
  runsThrough = $('#runsThrough input[name=runsthrough]:checked').val();
});


$('.teambtn').click(function(e){
     $(this).css("background", "rgb(204,229,255)");
    $(this).css("color", "black");
});

$('#bowlpro').click(function(e){
     $('#playMatch input[type=radio]').prop('checked', false);
     var currBowl = $('#bowlN').val();

     var bowls = {}
     bowls['bowl'] = currBowl;
     bowls['bowlCat'] = bowlCategory;
     bowls['nstrike'] = $('#nstrikeBat').val();
     bowls['strike'] = $('#strikeBat').val();
     bowls['bowler'] = $('#bowlerID').val();
     bowls['mid'] = $('#midval').val();
     bowls['legal'] = legalDelivery;
     bowls['isOut'] = isBatsmanOut;
     bowls['wcat'] = wkcat;
     bowls['runoutP1'] = $('#rop1').val(); // if runOut
     bowls['runoutP2'] = $('#rop2').val(); // if runOut
     bowls['runoutPlayer'] = runOutPlayer;
     bowls['run'] = runScoredInthisBall;
     bowls['runsThrough'] = runsThrough;
     bowls['step'] = 'bowls';
     
     conn.send(JSON.stringify(bowls));


    updateScorecard($('#strikeBat').val(), $('#nstrikeBat').val(), $('#bowlerID').val(), $('#midval').val(),'legal');



     if(isBatsmanOut==1){ // Select Next Batsman
          $('#bowlcat input').attr('disabled', true);
          $('#out input').attr('disabled', true);
          $('#bowlpro').attr('disabled', true);

            $.ajax({
              url: 'update.php', 
              type: 'post',
              data: {tbatting: $('#BattingTeam').val(), mid: $('#midval').val()},                         
              success: function(res){
                if(res.trim()=='inningBreak'){
                    //Innings Break
                    var innings = {}
                    innings['inning'] = $('#inning').val();
                    innings['mid'] = $('#midval').val();
                    innings['step'] = 'inningBreak';

                    conn.send(JSON.stringify(innings));
                    $('#inning').val(2);
                    
                    //Swapping Team
                    var temp = $('#BattingTeam').val();
                    $('#BattingTeam').val($('#BowlingTeam').val());
                    $('#BowlingTeam').val(temp);
                    $('#bowlN').val(0);
                    ////

                    $.ajax({
                      url: 'update.php', 
                      type: 'post',
                      data: {tbatting: $('#BattingTeam').val(), tbowling: $('#BowlingTeam').val(), mid: $('#midval').val(), type: 'updateBowler'},                         
                      success: function(res){
                        res = JSON.parse(res);
                        $('#bowler').html(res.bowler);
                        $('#strikeOp').html(res.bowler);
                        $('#nstrikeOp').html(res.bowler);

                        $('#startInning').show();

                        return;
                      }
                      });

                }else if(res.trim()=='matchOver'){
                   //Match Over
                }
                else{
                  $('#nextBatsman select').html(res);
                }
                  
              }
              });
          $('#nextBatsman').show();
     }

     if(parseInt(currBowl)%6==0  && legalDelivery==1){ //Rotate Strike --Over Completed

      console.log('Current Bowl: '+currBowl);

          if($('#matchSize').val() == currBowl && $('#inning').val()==1){
console.log('Inning Break');
            //Innings Break
                var innings = {}
                innings['bowl'] = currBowl;
                innings['bowlCat'] = bowlCategory;
                innings['nstrike'] = $('#nstrikeBat').val();
                innings['strike'] = $('#strikeBat').val();
                innings['mid'] = $('#midval').val();
                innings['step'] = 'inningBreak';

                conn.send(JSON.stringify(innings));

                if(($('.wontoss').attr('value') == '1' && $(".elecbtn input[type='checkbox']").val()=='1') || ($('.wontoss').attr('value') == '2' && $(".elecbtn input[type='checkbox']").val()=='0')){
                  for (var i = 0; i < team1pid.length; i++) {
                    $('#strikeOp').append('<option value="'+team2pid[i]+'">'+team2pname[i]+'</option>');
                    $('#nstrikeOp').append('<option value="'+team2pid[i]+'">'+team2pname[i]+'</option>');
                    $('#bowler').append('<option value="'+team1pid[i]+'">'+team1pname[i]+'</option>');
                  }
                }else{
                  for (var i = 0; i < team1pid.length; i++) {

                    $('#strikeOp').append('<option value="'+team1pid[i]+'">'+team1pname[i]+'</option>');
                    $('#nstrikeOp').append('<option value="'+team1pid[i]+'">'+team1pname[i]+'</option>');
                    $('#bowler').append('<option value="'+team2pid[i]+'">'+team2pname[i]+'</option>');
                  }
                }
        
                $('#inning').val(2);
                $('#startInning').show();
                $('#playMatch').hide();

          }else if($('#matchsize').val() == currBowl && $('#inning').val()==2){

            //Match Over
                var finish = {}
                finish['bowlCat'] = bowlCategory;
                finish['bowl'] = currBowl;
                finish['nstrike'] = $('#nstrikeBat').val();
                finish['strike'] = $('#strikeBat').val();
                finish['mid'] = $('#midval').val();
                finish['step'] = 'matchFinish';
                
                conn.send(JSON.stringify(finish));
          }else{
      
          $('#bowlcat input').attr('disabled', true);
          $('#out input').attr('disabled', true);
          $('#bowlpro').attr('disabled', true);

          rotateStrike();
            $.ajax({
              url: 'update.php', 
              type: 'post',
              data: {tbowling: $('#BowlingTeam').val(), mid: $('#midval').val()},                         
              success: function(res){
                  $('#nextOver select').html(res);
              }
              });
          $('#nextOver').show();


        }
    }

     if(legalDelivery==1){ // Increment Bowl
      currBowl=parseInt(currBowl)+1;
        $('#bowlN').val(currBowl);
     }
     $('#runOut, #whoCaught, #runs, #wkcat, #runsThrough, #runOutPlayer').hide();

     if(parseInt(runScoredInthisBall)%2!=0){ //Rotate Strike --Odd Runs taken
      rotateStrike();
    }
     
});


$('#nextBatBut').click(function(e){
  
  var batsmantemp = $('#strikeBat').val();
  if(wkcat == 'caught'){
    if(strikeChanged==1){ //Batsmen changed their strike. when caught.. New Batsman on NonStrike.
      rotateStrike();
      $('#nstrikeBat').val(nextBatsman);
      $('#nstrikeBat').attr('data-pname', $('#nextBatsman select option:selected').text());
    }else{ //New Batsman on Strike.
      $('#strikeBat').val(nextBatsman);
      $('#strikeBat').attr('data-pname', $('#nextBatsman select option:selected').text());
    }  
  }else if(wkcat == 'ro'){

    //Batsmen changed their strike. when RunOut.. New Batsman on NonStrike (Batsman Out was Striker).
    if(strikeChanged==1 && $('#strikeBat').val() == runOutPlayer){ 
      rotateStrike();
      $('#nstrikeBat').val(nextBatsman);
      $('#nstrikeBat').attr('data-pname', $('#nextBatsman select option:selected').text());

    }else if(strikeChanged==1 && ($('#nstrikeBat').val()==runOutPlayer)){
      rotateStrike();
      $('#strikeBat').val(nextBatsman);
      $('#strikeBat').attr('data-pname', $('#nextBatsman select option:selected').text());

    }else if(strikeChanged==0 && ($('#strikeBat').val()==runOutPlayer)){
      $('#strikeBat').val(nextBatsman);
      $('#strikeBat').attr('data-pname', $('#nextBatsman select option:selected').text());

    }else if(strikeChanged==0 && ($('#nstrikeBat').val()==runOutPlayer)){
      $('#nstrikeBat').val(nextBatsman);
      $('#nstrikeBat').attr('data-pname', $('#nextBatsman select option:selected').text());
    }

  }else{
    $('#strikeBat').val(nextBatsman);
      $('#strikeBat').attr('data-pname', $('#nextBatsman select option:selected').text());
  }
 
  $('#nextBatsman').hide();
  $('#bowlpro').attr('disabled', false);
  $('#bowlcat input').attr('disabled', false);
  $('#out input').attr('disabled', false);
  $('.bowled, .caught, .lbw, .htwkt').prop( "disabled", false );

  var nextBat = {}
  nextBat['nextBat'] = nextBatsman;
  nextBat['mid'] = $('#midval').val();
  nextBat['step'] = 'nextBat';
  nextBat['nstrike'] = $('#nstrikeBat').val();
  nextBat['strike'] = $('#strikeBat').val();
  if(wkcat=='ro'){
    nextBat['outPlayer'] = runOutPlayer;
  }else{
    nextBat['outPlayer'] = batsmantemp;
  }

  conn.send(JSON.stringify(nextBat));

});


$('#nextOverBut').click(function(e){

  var nextOver = {}
  nextOver['nextBowler'] = nextBowler;
  nextOver['nextBowlerName'] = $('#nextOver select option:selected').text();
  nextOver['currBowler'] = $('#bowlerID').val();
  nextOver['mid'] = $('#midval').val();
  nextOver['step'] = 'nextOver';
  nextOver['nstrike'] = $('#nstrikeBat').val();
  nextOver['strike'] = $('#strikeBat').val();

  conn.send(JSON.stringify(nextOver));


  //ChangeBowler
  $('#bowlerID').val(nextBowler);
  $('#bowlerID').attr('data-pname', $('#nextOver select option:selected').text());

  $('#nextOver').hide();
  $('#bowlpro').attr('disabled', false);
  $('#bowlcat input').attr('disabled', false);
  $('#out input').attr('disabled', false);

});


}); //Document.ready()