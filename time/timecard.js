// Dan Berkowitz, berkod2@rpi.edu, dansberkowitz@gmail.com, January 2013
WipeBegun = 0;
function lastweek()
{
    wipeBoard(false);
    start_time = parseInt(start_time) - (60*60*24*14);
    for (i = 1; i <= 14; i++)
    {
        var d = new Date((start_time * 1000) + ((i-1)*(60*60*24*1000)));
        $(".day" + i + "Name").html((d.getMonth() + 1) + "/" + d.getDate() + "/" + d.getFullYear());
    }
    loadPage(0);
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
    loadPage(0);
}


//this functions are for the grid interface
function clockPunch(passedDay, passedHour, passedHalf, passedTimePassed)
{
    if (locked == 2) {
        alert("This time period has been locked");
        return;
    }else{
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
}

function punch(passedDay, passedHour, passedTime, passedDuration, passedUsedTime)
{//database saves in quarters, the javascript currently works in halfs
    var the_day = (parseInt(start_time) + ((passedDay-1) * 60*60*24)) * 1000;
    var CompleteDate = new Date(the_day);
    CompleteDate.setHours(passedHour);
    CompleteDate.setMinutes(passedTime);
    //this works because the time is being used for one thing and the day is seperate
    var StartTime = CompleteDate.getHours() + ":" + CompleteDate.getMinutes() + ":" + CompleteDate.getSeconds();
    var FinishedDay = CompleteDate.getFullYear() + '-' + (1+CompleteDate.getMonth()) + '-' + CompleteDate.getDate();
    CompleteDate = new Date(CompleteDate.getTime() + (1000*(60*(parseInt(passedDuration) - 1))));
    var StopTime  = CompleteDate.getHours() + ":" + CompleteDate.getMinutes() + ":" + CompleteDate.getSeconds();
    if (pageoverride) {
        order = $.ajax({
            type: 'POST',
            url: './ajax.php',
            data: {type: 'punchClock', day: FinishedDay, start_time: StartTime, end_time: StopTime, punch: passedUsedTime, override: $('#SaveAsUser').val(), group: pagegroup},
            success: function(data) {
                //console.log(data);
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
    }else{
        order = $.ajax({
            type: 'POST',
            url: './ajax.php',
            data: {type: 'punchClock', day: FinishedDay, start_time: StartTime, end_time: StopTime, punch: passedUsedTime, group: pagegroup},
            success: function(data) {
                //console.log(data);
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
}

function fadeME(tag, start_color)
{
    the_color = parseInt(start_color, 16) + parseInt("010001", 16);
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

function loadPage(passedRun)
{
    locked = 0;
    check_status();
    
}

function pageConfig() {
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
        data: {type: 'getPunches', group: pagegroup, start_day: start_time},
        success: function(data) {
            parsedJson = JSON.parse(data);
            for (i = 0; i < parsedJson.length; i++)
            {
                IE_Fix_String = parsedJson[i]['startTime'];
                IE_Date = IE_Fix_String.split(" ");
                IE_Time = IE_Date[1].split(":");
                IE_Date = IE_Date[0].split("-");
                plexis = new Date(IE_Date[0], IE_Date[1] - 1, IE_Date[2], IE_Time[0], IE_Time[1], IE_Time[2]);
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
            if (locked == 2) {
                statusChange(3);
            }else{
                if (locked == 3) {
                    statusChange(1);
                }
            }
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
    if (shouldSave)
    {
        WipeBegun = 1;
        var the_day = parseInt(start_time) * 1000;
        var CompleteDate = new Date(the_day);
        var FinishedDay = CompleteDate.getFullYear() + '-' + (1+CompleteDate.getMonth()) + '-' + CompleteDate.getDate();
        order = $.ajax({
            type: 'POST',
            url: './ajax.php',
            data: {type: 'DBMacro', macro_code: 2, group: pagegroup, start_date: FinishedDay},
            success: function(data) {
                if (data[0] == 'S')
                {
                    WipeBegun = 0;
                }else{
                    WipeBegun = 5;
                }
            }
        });
    }
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
    
}

function loadTemplate()
{
    if (locked == 2) {
        alert("This time period has been locked");
        return;
    }
    if ( parseInt($('#templates').val()) <= 0)
    {
        if (parseInt($('#templates').val()) == -1) {
            window.open("./templates.php");
        }   
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
    //This wipebegun code is for a race condition between wiping and saving
    if (WipeBegun > 0) {
        var the_day = parseInt(start_time) * 1000;
        var CompleteDate = new Date(the_day);
        var FinishedDay = CompleteDate.getFullYear() + '-' + (1+CompleteDate.getMonth()) + '-' + CompleteDate.getDate();
        
        order = $.ajax({
            type: 'POST',
            url: './ajax.php',
            data: {type: 'DBMacro', macro_code: 1,template: $('#templates').val(), group: pagegroup, start_date: FinishedDay},
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
    }else{
        if (WipeBegun < 5) {
            WipeBegun++;
            setTimeout(function() { save_template_db() }, 300 );
        }else{
            statusChange(2);
        }
    }
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
        case 3:
            $('#pageStatus').html("Time Card Locked! Total Hours: " + totalHours);
            $("#pageStatus").css("background-color", "blue");
            break;
        
    }
    setTimeout(function() {	fadeME("#pageStatus", "00FF00") }, 500 );
}


function selectUser()
{
    wipeBoard(false);
    pageoverride = true;
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
        data: {type: 'getPunches', group: pagegroup, start_day: start_time, override: $('#SaveAsUser').val()},
        success: function(data) {
            parsedJson = JSON.parse(data);
            for (i = 0; i < parsedJson.length; i++)
            {
                IE_Fix_String = parsedJson[i]['startTime'];
                IE_Date = IE_Fix_String.split(" ");
                IE_Time = IE_Date[1].split(":");
                IE_Date = IE_Date[0].split("-");
                plexis = new Date(IE_Date[0], IE_Date[1] - 1, IE_Date[2], IE_Time[0], IE_Time[1], IE_Time[2]);
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
            if (locked == 2) {
                statusChange(3);
            }else{
                if (locked == 3) {
                    statusChange(1);
                }
            }
        },
        error: function(data) {
            //error calling names
        }, 
    });
    
}

function check_status() {
    locked = 0;
    var the_day = parseInt(start_time) * 1000;
    var CompleteDate = new Date(the_day);
    var sqlDate = CompleteDate.getFullYear() + '-' + (1+CompleteDate.getMonth()) + '-' + CompleteDate.getDate();
    order = $.ajax({
        type: 'POST',
        url: './ajax.php',
        data: {type: 'check_locked', start_date: sqlDate, group: pagegroup},
        success: function(data) {
            //console.log(data);
            if (data == "locked") {//this is so it wont be confused with the sql entries
                locked = 2;
            }else{
                locked = 3;
            }
            pageConfig();
        },
        error: function(data) {
            //error calling names
            
        }, 
    });
}