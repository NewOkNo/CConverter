function callBaseData(){
    const xhttp = new XMLHttpRequest();

    moment.locale('et');
    var dateNow = moment().format('L');
    var timeNow = moment().format('LT').split(':');


    var SendInfo= { SendInfo: [dateNow]};

    $.ajax({
        type: 'post',
        url: 'Your-URI',
        data: JSON.stringify(SendInfo),
        contentType: "application/json; charset=utf-8",
        traditional: true,
        success: function (data) {
        console.log(data)
        }
    });
    //if(timeNow[0]>13)
}