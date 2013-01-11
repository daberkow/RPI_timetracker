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


//this functions are for the grid interface
function clockPunch(passedDay, passedHour, passedHalf, passedTimePassed)
{
    $('#pageStatus').html("Saving");
    //start_time is first day time in unix timetamp, not javascript
    if (passedHalf == 30)
    {
        half = 2;
    }else{
        half = 0;
    }
    if (typeof(savedData['hour' + passedDay + "_" + passedHour + "_" + half]) == "undefined")
    {
        savedData['hour' + passedDay + "_" + passedHour + "_" + half] = 1;
        $("#hour_" + passedDay + "_" + passedHour + "_" + half).css("background-color", "green");
        if (typeof(savedData['day' + passedDay]) == "undefined")
        {
            savedData['day' + passedDay] = 0.5;
        }else{
            savedData['day' + passedDay] += 0.5;
        }
        punch(passedDay, passedHour, passedHalf, passedTimePassed, 1); 
    }else{ 
        //already has value, savedDay shoudl too
        if (savedData['hour' + passedDay + "_" + passedHour + "_" + half] == 1)
        {
            savedData['hour' + passedDay + "_" + passedHour + "_" + half] = 0;
            $("#hour_" + passedDay + "_" + passedHour + "_" + half).css("background-color", "transparent");
            savedData['day' + passedDay] -= 0.5;
            punch(passedDay, passedHour, passedHalf, passedTimePassed, 0); 
        }else{
            savedData['hour' + passedDay + "_" + passedHour + "_" + half] = 1;
            $("#hour_" + passedDay + "_" + passedHour + "_" + half).css("background-color", "green");
            savedData['day' + passedDay] += 0.5;
            punch(passedDay, passedHour, passedHalf, passedTimePassed, 1); 
        }
    }
    drawDay(passedDay);
}

function drawDay(passedDay)
{
    $("#dayTotal" + passedDay).val(savedData['day' + passedDay]);
}

function punch(passedDay, passedHour, passedTime, passedDuration, passedUsedTime)
{//database saves in quarters, the javascript currently works in halfs
    the_day = (parseInt(start_time) + ((passedDay-1) * 60*60*24)) * 1000;
    CompleteDate = new Date(the_day);
    CompleteDate.setHours(passedHour);
    CompleteDate.setMinutes(passedTime);
    StartTime = CompleteDate.getHours() + ":" + CompleteDate.getMinutes() + ":" + CompleteDate.getSeconds();
    CompleteDate = new Date(CompleteDate)
    FinishedDay = CompleteDate.getFullYear() + '-' + (1+CompleteDate.getMonth()) + '-' + CompleteDate.getDate();
    CompleteDate = new Date(CompleteDate.getTime() + (1000*60*(parseInt(passedDuration) - 1)));
    StopTime  = CompleteDate.getHours() + ":" + CompleteDate.getMinutes() + ":" + CompleteDate.getSeconds();
    order = $.ajax({
        type: 'POST',
        url: './ajax.php',
        data: {type: 'punchClock', day: FinishedDay, start_time: StartTime, end_time: StopTime, punch: passedUsedTime},
        success: function(data) {
            if (data == "Saved")
            {
                $('#pageStatus').html("Saved");
                $("#pageStatus").css("background-color", "green");
            }else{
                $('#pageStatus').html("Error");
                $("#pageStatus").css("background-color", "red");
            }
            setTimeout(function() {	fadeME("#pageStatus", "007b12") }, 500 );
        },
        error: function(data) {
            //error calling names
        }, 
    });
}

function fadeME(tag, start_color)
{
    the_color = parseInt(start_color, 16) + parseInt("010101", 16);
    new_color = "#" + the_color.toString(16);
    while (new_color.length < 7)
    {
        new_color += "0";
    } 
    $(tag).css("background", new_color);
    if( the_color<=16777215)
    {
        setTimeout(function() {	fadeME(tag,the_color) }, 300 );
    }else{
        $(tag).css("background", "white");
    }
}

function submitTimeCard()// we save along the way this isnt needed
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

function loadPage()
{
    $('#pageStatus').html("Loading...");
    for (k = 1; k < 15; k++)
    {
        savedData['day' + k] = 0;
        drawDay(k);
    }
    
    origin = new Date(start_time * 1000);
    order = $.ajax({
        type: 'POST',
        url: './ajax.php',
        data: {type: 'getPunches', start_day: start_time},
        success: function(data) {
            parsedJson = JSON.parse(data);
            for (i = 0; i < parsedJson.length; i++)
            {
                plexis = new Date(parsedJson[i]['startTime']);
                day = Math.floor((plexis.getTime() - origin.getTime() + (60*60*24*1000)) / (60*60*24*1000));
                if (plexis.getMinutes() == 0)
                {
                    half = 0;
                    $("#hour_" + day + "_" + plexis.getHours() + "_" + half).css("background-color", "green");
                    savedData['hour' + day + "_" + plexis.getHours() + "_0"] = 1;
                    savedData['day' + day] += 0.5;
                }else{
                    if (plexis.getMinutes() == 30)
                    {
                        half = 2;
                        $("#hour_" + day + "_" + plexis.getHours() + "_" + half).css("background-color", "green");
                        savedData['hour' + day + "_" + plexis.getHours() + "_1"] = 1;
                        savedData['day' + day] += 0.5;
                    }else{
                        //half = 1;
                    }
                }
                drawDay(day);
               
            }
        },
        error: function(data) {
            //error calling names
        }, 
    });
    $('#pageStatus').html("Synced");
}

function saveTemplate()
{
    $('#pageStatus').html("Saving");
    if ($('#templateName').val() == "")
    {
        alert("Please enter a name for the template");
        return;
    }
    savingString = "";
    for(row in savedData)
    {
        if(row[0] == 'h')
        {//hour data
            savingString += row.substring(4) + ",";
        }
    }
    console.log(savingString);
    order = $.ajax({
        type: 'POST',
        url: './ajax.php',
        data: {type: 'saveTemplate', dataString: savingString, temName: $('#templateName').val()},
        success: function(data) {
            if (data[0] == 'S')
            {
                splitData = data.split(" ");
                $('#pageStatus').html("Saved");
                $("#pageStatus").css("background-color", "green");
                $("#templates").append("<option value=" + splitData[1] + ">" + $('#templateName').val() + "</option>");
                $('#templateName').val("");
            }else{
                $('#pageStatus').html("Error");
                $("#pageStatus").css("background-color", "red");
            }
            setTimeout(function() {	fadeME("#pageStatus", "007b12") }, 500 );
        },
        error: function(data) {
            //error calling names
        }, 
    });
}

function loadTemplate()
{
    //stopped here need to load templates
}