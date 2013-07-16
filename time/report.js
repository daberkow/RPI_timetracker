// Dan Berkowitz, berkod2@rpi.edu, dansberkowitz@gmail.com, January 2013

//This loas up the report
function loader()
{
    the_day = parseInt(start_time) * 1000;
    CompleteDate = new Date(the_day);
    FinishedDay = (1+CompleteDate.getMonth()) + '-' + CompleteDate.getDate() + '-' + CompleteDate.getFullYear();
    sqlDate = CompleteDate.getFullYear() + '-' + (1+CompleteDate.getMonth()) + '-' + CompleteDate.getDate();
    CompleteDate = new Date(the_day + (1000*60*60*24*14) - 1000*60*60*24);
    sqlLastDate = CompleteDate.getFullYear() + '-' + (1+CompleteDate.getMonth()) + '-' + CompleteDate.getDate();
    LastDay = (1+CompleteDate.getMonth()) + '-' + CompleteDate.getDate() + '-' + CompleteDate.getFullYear();
    $('#weekdescription').html("From: " + FinishedDay + " - " + LastDay);
    

    order = $.ajax({
        type: 'POST',
        url: './ajax.php',
        data: {type: 'printReport', start_date: sqlDate, group: groupName},
        success: function(data) {
            $('#tableArea').html(data);
            check_status();
        },
        error: function(data) {
            //error calling names
            
        }, 
    });
}

//Go back in time!
function lastweek()
{
    $('#tableArea').html("");
    start_time = parseInt(start_time) - (60*60*24*14);
    loader();
}

//To the future
function nextweek()
{
    $('#tableArea').html("");
    start_time = parseInt(start_time) + (60*60*24*14);
    loader();
}

//LOCK IT DOWN
function lock() {
    switch ($("#locker").html()) {
        case "Unlock Time Cards":
            var result = confirm("This will unlock the table for this week, do you want to continue?");
            $("#locker").attr('disabled', 'disabled');
            if (result) {
                order = $.ajax({
                    type: 'POST',
                    url: './ajax.php',
                    data: {type: 'unLockCards', start_date: sqlDate, group: groupName, end_date: sqlLastDate},
                    success: function(data) {
                        //console.log(data);
                        $("#locker").html("Lock Time Cards");
                        $("#locker").removeAttr("disabled");
                        $('.colored').css("background", "#CCCCCC");
                    },
                    error: function(data) {
                        //error calling names
                        
                    }, 
                });
            }
            break;
        case "Lock Time Cards":
            var result = confirm("This will lock the table for this week, finializing time cards, do you want to continue?");
            $("#locker").attr('disabled', 'disabled');
            if (result) {
                order = $.ajax({
                type: 'POST',
                url: './ajax.php',
                data: {type: 'LockCards', start_date: sqlDate, group: groupName, end_date: sqlLastDate},
                success: function(data) {
                    //console.log(data);
                    $("#locker").html("Unlock Time Cards");
                    $("#locker").removeAttr("disabled");
                    $('.colored').css("background", "#e40045");
                },
                error: function(data) {
                    //error calling names
                }, 
            });
            }
            break;
    }
}

//check if locked
function check_status() {
    order = $.ajax({
        type: 'POST',
        url: './ajax.php',
        data: {type: 'check_locked', start_date: sqlDate, group: groupName},
        success: function(data) {
            //console.log(data);
            if (data == "locked") {
                $("#locker").html("Unlock Time Cards");
                $('.colored').css("background", "#e40045");
            }            
        },
        error: function(data) {
            //error calling names
        }, 
    });
}