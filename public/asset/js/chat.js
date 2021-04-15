var apiRoot = "http://localhost:8000/";
var lastMsgId = 0;
var selectedConvId = -1;
var incomingHtml = false;
var outgoingHtml = false;

$(document).ready(function() {
    incomingHtml = $(".incoming_msg").clone();
    outgoingHtml = $(".outgoing_msg").clone();
    $(".incoming_msg").remove();
    $(".outgoing_msg").remove();
    console.dir(outgoingHtml);
    refreshMessages();

    $("#exampleModal").on("hidden.bs.modal", function () {
        exitConv();
    });
});

function loadNewMessages() {
    $.ajax({
        type: "POST",
        url: apiRoot + "conversation/" + selectedConvId + "/" + lastMsgId,
        dataType: "json",
        //data: {"hash": token},
        success: function(oRep){
            $(".loading_msg").remove();

            var i;
            console.dir(oRep);
            for(i=0;i<oRep.messages.length;i++) {
                var incomingMsg = oRep.messages[i].incoming ? incomingHtml.clone() : outgoingHtml.clone();
                incomingMsg.find("p").html(oRep.messages[i].content);
                incomingMsg.find(".time_date").html(oRep.messages[i].createdAt);
                console.dir(outgoingHtml);
                $("#messagesContainer").append(incomingMsg);
            }
            if(i>0) {
                //console.log(i);
                lastMsgId = oRep.messages[i-1].id;
            }
        }
    });
}

function sendNewMessage() {
    var msg = $("#write_msg").val();

    $.ajax({
        type: 'PUT',
        url: 'http://localhost:8000/conversation/1',
        data: JSON.stringify({ content: msg }),
        contentType: "application/json; charset=utf-8",
        dataType: 'json',
        statusCode: {
            301: function(responseObject, textStatus, errorThrown) {

            },
            302: function(responseObject, textStatus, errorThrown) {
                //yor code goes here
            }
        },
        success: function(data1) {
            //console.log(data1)
            //alert("envoyé avec succes!");
        }
    })
        .done(function(data) {

            var json = JSON.parse(data);
            console.log(data);

        })
        .fail(function(jqXHR, textStatus) {
            alert('Veuillez déplacer au moins une case ');
        })
        .always(function(jqXHR, textStatus) {
            //window.document.location = '/';
        });
    var myMsg = outgoingHtml.clone();
    console.log(msg);
    myMsg.find("p").html(msg);
    myMsg.find(".time_date").html("Now");
    $("#messagesContainer").append(myMsg);
    myMsg.wrap( "<div class='loading_msg'></div>" );

    $("#write_msg").val('');

}


function refreshMessages()
{
    setTimeout(refreshMessages, 3000);
    if(selectedConvId > 0)
        loadNewMessages();
}
function selectConv(convId) {
    selectedConvId = convId;
}
function exitConv() {
    selectedConvId = -1;
    lastMsgId = 0;
    $(".incoming_msg").remove();
    $(".outgoing_msg").remove();
}
