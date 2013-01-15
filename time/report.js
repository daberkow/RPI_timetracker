// Dan Berkowitz, berkod2@rpi.edu, dansberkowitz@gmail.com, January 2013

function loader()
{
    the_day = parseInt(start_time) * 1000;
    CompleteDate = new Date(the_day);
    FinishedDay = (1+CompleteDate.getMonth()) + '-' + CompleteDate.getDate() + '-' + CompleteDate.getFullYear();
    sqlDate = CompleteDate.getFullYear() + '-' + (1+CompleteDate.getMonth()) + '-' + CompleteDate.getDate();
    CompleteDate = new Date(the_day + (1000*60*60*24*14) - 1000*60*60*24);
    LastDay = (1+CompleteDate.getMonth()) + '-' + CompleteDate.getDate() + '-' + CompleteDate.getFullYear();
    $('#weekdescription').html("From: " + FinishedDay + " - " + LastDay);
    

    order = $.ajax({
        type: 'POST',
        url: './ajax.php',
        data: {type: 'printReport', start_date: sqlDate, group: groupName},
        success: function(data) {
            $('#tableArea').html(data);
        },
        error: function(data) {
            //error calling names
            
        }, 
    });
}

function lastweek()
{
    $('#tableArea').html("");
    start_time = parseInt(start_time) - (60*60*24*14);
    loader();
}

function nextweek()
{
    $('#tableArea').html("");
    start_time = parseInt(start_time) + (60*60*24*14);
    loader();
}