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
    if (typeof(savedData['hour' + passedDay + "_" + passedHour + "_" + passedHalf]) == "undefined")
    {
        savedData['hour' + passedDay + "_" + passedHour + "_" + passedHalf] = 1;
        $("#hour_" + passedDay + "_" + passedHour + "_" + passedHalf).css("background-color", "green");
        if (typeof(savedData['day' + passedDay]) == "undefined")
        {
            savedData['day' + passedDay] = 0.5;
        }else{
            savedData['day' + passedDay] += 0.5;
        }
        punch(passedDay, passedHour, passedHalf,1); 
    }else{ 
        //already has value, savedDay shoudl too
        if (savedData['hour' + passedDay + "_" + passedHour + "_" + passedHalf] == 1)
        {
            savedData['hour' + passedDay + "_" + passedHour + "_" + passedHalf] = 0;
            $("#hour_" + passedDay + "_" + passedHour + "_" + passedHalf).css("background-color", "transparent");
            savedData['day' + passedDay] -= 0.5;
            punch(passedDay, passedHour, passedHalf,0); 
        }else{
            savedData['hour' + passedDay + "_" + passedHour + "_" + passedHalf] = 1;
            $("#hour_" + passedDay + "_" + passedHour + "_" + passedHalf).css("background-color", "green");
            savedData['day' + passedDay] += 0.5;
            punch(passedDay, passedHour, passedHalf,1); 
        }
    }
    drawDay(passedDay);
}

function drawDay(passedDay)
{
    $("#dayTotal" + passedDay).val(savedData['day' + passedDay]);
}

function punch(passedDay, passedHour, passedQuarter, passedUsedTime)
{//database saves in quarters, the javascript currently works in halfs
    the_day = (parseInt(start_time) + ((passedDay-1) * 60*60*24)) * 1000;
    CompleteDate = new Date(the_day);
    FinishedDay = CompleteDate.getFullYear() + '-' + (1+CompleteDate.getMonth()) + '-' + CompleteDate.getDate();
    console.log("Day: " + CompleteDate.getDate() + "-" + (1+CompleteDate.getMonth()) + "-" + CompleteDate.getFullYear() + " , Hour: " + passedHour + ", Quarter: " + passedQuarter + " Punch: " + passedUsedTime); //stopped here can not test
    order = $.ajax({
        type: 'POST',
        url: './ajax.php',
        data: {type: 'punchClock', day: FinishedDay, hour: passedHour, quarter: passedQuarter, punch: passedUsedTime, mode: "half"},
        success: function(data) {
            console.log(data);
        },
        error: function(data) {
            //error calling names
        }, 
    });
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