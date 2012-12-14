function gotoGroup()
{
    window.location = './group.php?group=' + $('#groupSelector').val();
}

function lastweek()
{
    start_time = parseInt(start_time) - (60*60*24*14);
    for (i = 1; i <= 14; i++)
    {
        var d = new Date((start_time * 1000) + ((i-1)*(60*60*24*1000)));
        $(".day" + i + "Name").html((d.getMonth() + 1) + "/" + d.getDate() + "/" + d.getFullYear());
    }
}

function nextweek()
{
    start_time = parseInt(start_time) + (60*60*24*14);
    for (i = 1; i <= 14; i++)
    {
        var d = new Date((start_time * 1000) + ((i-1)*(60*60*24*1000)));
        $(".day" + i + "Name").html((d.getMonth() + 1) + "/" + d.getDate() + "/" + d.getFullYear());
    }
}

function clockPunch(passedDay, passedHour, passedHalf)
{
    //start_time is first day time in unix timetamp, not javascript
    $("#hour_" + passedDay + "_" + passedHour + "_" + passedHalf).css("background-color", "green");
    if (typeof(savedData['hour' + passedDay + "_" + passedHour + "_" + passedHalf]) == "undefined")
    {
        savedData['hour' + passedDay + "_" + passedHour + "_" + passedHalf] = 1;
    }else{
        if (savedData['hour' + passedDay + "_" + passedHour + "_" + passedHalf] == 1)
        {
            savedData['hour' + passedDay + "_" + passedHour + "_" + passedHalf] == 0;
        }else{
            savedData['hour' + passedDay + "_" + passedHour + "_" + passedHalf] == 1;
        }
    }
}

function punch(passedDay, passedHour, passedHalf, passedQuarter)
{//database saves in quarters, the javascript currently works in halfs
    the_day = new Date((start_time * 1000) + (passedDay * (i-1) * 60*60*24*1000));
    console.log(the_day); //stopped here can not test
    /*order = $.ajax({
        type: 'POST',
        url: './ajax.php',
        data: {type: 'punchClock', day: , hour: passedHour, quarter: passedQuarter},
        success: function(data) {
               
        },
        error: function(data) {
            //error calling names
        }, 
    });*/
}

function submitTimeCard()
{//here we have savedData and start_time for out information
    order = $.ajax({
        type: 'POST',
        url: './ajax.php',
        data: {type: 'submitTimeCard'},
        success: function(data) {
               
        },
        error: function(data) {
            //error calling names
        }, 
    });
}