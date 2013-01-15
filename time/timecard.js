// Dan Berkowitz, berkod2@rpi.edu, dansberkowitz@gmail.com, January 2013

function lastweek()
{
    wipeBoard(false);
    start_time = parseInt(start_time) - (60*60*24*14);
    for (i = 1; i <= 14; i++)
    {
        var d = new Date((start_time * 1000) + ((i-1)*(60*60*24*1000)));
        $(".day" + i + "Name").html((d.getMonth() + 1) + "/" + d.getDate() + "/" + d.getFullYear());
    }
    loadPage();
}

function nextweek()
{
    wipeBoard(false);
    start_time = parseInt(start_time) + (60*60*24*14);
    for (i = 1; i <= 14; i++)
    {
        var d = new Date((start_time * 1000) + ((i-1)*(60*60*24*1000)));
        $(".day" + i + "Name").html((d.getMonth() + 1) + "/" + d.getDate() + "/" + d.getFullYear());
    }
    loadPage();
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
        $("#hour" + passedDay + "_" + passedHour + "_" + half).css("background-color", "green");
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
            $("#hour" + passedDay + "_" + passedHour + "_" + half).css("background-color", "transparent");
            savedData['day' + passedDay] -= 0.5;
            punch(passedDay, passedHour, passedHalf, passedTimePassed, 0); 
        }else{
            savedData['hour' + passedDay + "_" + passedHour + "_" + half] = 1;
            $("#hour" + passedDay + "_" + passedHour + "_" + half).css("background-color", "green");
            savedData['day' + passedDay] += 0.5;
            punch(passedDay, passedHour, passedHalf, passedTimePassed, 1); 
        }
    }
    drawDay(passedDay);
}

function punch(passedDay, passedHour, passedTime, passedDuration, passedUsedTime)
{//database saves in quarters, the javascript currently works in halfs
    the_day = (parseInt(start_time) + ((passedDay-1) * 60*60*24)) * 1000;
    CompleteDate = new Date(the_day);
    CompleteDate.setHours(passedHour);
    CompleteDate.setMinutes(passedTime);
    //this works because the time is being used for one thing and the day is seperate
    StartTime = CompleteDate.getHours() + ":" + CompleteDate.getMinutes() + ":" + CompleteDate.getSeconds();
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
                statusChange(1);
            }else{
                //console.log(data);
                statusChange(2);
            }
        },
        error: function(data) {
            //error calling names
            statusChange(2);
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
                    $("#hour" + day + "_" + plexis.getHours() + "_" + half).css("background-color", "green");
                    savedData['hour' + day + "_" + plexis.getHours() + "_0"] = 1;
                    savedData['day' + day] += 0.5;
                }else{
                    if (plexis.getMinutes() == 30)
                    {
                        half = 2;
                        $("#hour" + day + "_" + plexis.getHours() + "_" + half).css("background-color", "green");
                        savedData['hour' + day + "_" + plexis.getHours() + "_2"] = 1;
                        savedData['day' + day] += 0.5;
                    }else{
                        //half = 1;
                    }
                }
                drawDay(day);
            }
            statusChange(1);
        },
        error: function(data) {
            //error calling names
        }, 
    });
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
        if(row[0] == 'h' && savedData[row] == 1)
        {//hour data
            savingString += row.substring(4) + ",";
        }
    }
    //console.log(savingString);
    order = $.ajax({
        type: 'POST',
        url: './ajax.php',
        data: {type: 'saveTemplate', dataString: savingString, temName: $('#templateName').val()},
        success: function(data) {
            if (data[0] == 'S')
            {
                splitData = data.split(" ");
                statusChange(1);
                $("#templates").append("<option value=" + splitData[1] + ">" + $('#templateName').val() + "</option>");
                $('#templateName').val("");
            }else{
                statusChange(2);
            }
        },
        error: function(data) {
            //error calling names
            statusChange(2);
        }, 
    });
}

function drawDay(passedDay)
{
    $("#dayTotal" + passedDay).val(savedData['day' + passedDay]);
}

function wipeBoard(shouldSave)
{
    for(row in savedData)
    {
        if(row[0] == 'h')
        {//hour data
            $("#" + row).css("background-color", "transparent");
            savedData[row] = 0;
            temp = row.substring(4);
            temp = temp.split("_");
            //console.log(temp[0]);
            drawDay(temp[0]);
        }else{
            if (row[0] == 'd')
            {
                savedData[row] = 0;
                drawDay(row.substring(3));
            }
            
        }
    }
    if (shouldSave)
    {
        the_day = parseInt(start_time) * 1000;
        CompleteDate = new Date(the_day);
        FinishedDay = CompleteDate.getFullYear() + '-' + (1+CompleteDate.getMonth()) + '-' + CompleteDate.getDate();
        order = $.ajax({
            type: 'POST',
            url: './ajax.php',
            data: {type: 'DBMacro', macro_code: 2, start_date: FinishedDay},
        });
    }
}

function loadTemplate()
{
    if ( parseInt($('#templates').val()) <= 0)
    {
        return;
    }
    order = $.ajax({
        type: 'POST',
        url: './ajax.php',
        data: {type: 'getTemplate', template: $('#templates').val()},
        success: function(data) {
            //console.log(data);
            split_digest = JSON.parse(data);
            split_digest = split_digest[0][0].toString();
            split_digest = split_digest.split(",");
            //console.log(split_digest);
            wipeBoard(true);
            for(box in split_digest)
            {
                temp_Split = split_digest[box].split("_");
                //console.log(temp_Split);
                if (temp_Split.length == 3)
                {
                    $("#hour" + temp_Split[0] + "_" + temp_Split[1] + "_" +(temp_Split[2])).css("background-color", "green");
                    savedData['hour' + temp_Split[0] + "_" + temp_Split[1] + "_" + temp_Split[2]] = 1;
                    savedData['day' + temp_Split[0]] += 0.5;
                    drawDay(temp_Split[0]);
                }
            }
            save_template_db();
            statusChange(1);
        },
        error: function(data) {
            //error calling names
            statusChange(2);
        }, 
    });
}

function save_template_db()
{
    the_day = parseInt(start_time) * 1000;
    CompleteDate = new Date(the_day);
    FinishedDay = CompleteDate.getFullYear() + '-' + (1+CompleteDate.getMonth()) + '-' + CompleteDate.getDate();
    
    order = $.ajax({
        type: 'POST',
        url: './ajax.php',
        data: {type: 'DBMacro', macro_code: 1,template: $('#templates').val(), start_date: FinishedDay},
        success: function(data) {
            if (data[0] == 'S')
            {
                statusChange(1);
            }else{
                statusChange(2);
            }
        },
        error: function(data) {
            //error calling names
            statusChange(2);
        }, 
    });
}

function statusChange(passedStatus)
{
    totalHours = 0.0;
    for(day in savedData)
    {
        if (day[0] == 'd')
        {
            totalHours += parseFloat(savedData[day]);
        }
    }
    switch (passedStatus)
    {
        case 1:
            $('#pageStatus').html("Saved, Total Hours: " + totalHours);
            $("#pageStatus").css("background-color", "green");
            break;
        case 2:
            $('#pageStatus').html("Error");
            $("#pageStatus").css("background-color", "red");
            alert("An Error has occured, please refresh page");
            break;
    }
    setTimeout(function() {	fadeME("#pageStatus", "007b12") }, 500 );
}