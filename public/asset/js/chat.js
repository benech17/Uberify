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

    refreshMessages();
});

function loadNewMessages() {
    $.ajax({
        type: "POST",
        url: apiRoot + "conversation/" + selectedConvId + "/" + lastMsgId,
        dataType: "json",
        //data: {"hash": token},
        success: function(oRep){
            var i;
            for(i=0;i<oRep.messages.length;i++) {
                var incomingMsg = oRep.messages[i].incoming ? incomingHtml.clone() : outgoingHtml.clone();
                incomingMsg.find("p").html(oRep.messages[i].content);
                incomingMsg.find(".time_date").html(oRep.messages[i].createdAt);
                $("#convMessages").append(incomingMsg);
            }

            lastMsgId = oRep.message[i].id;
        }
    });

}


function refreshMessages()
{
    setTimeout(refreshMessages, 3000);
    if(selectedConvId > 0)
        loadNewMessages();
}
function selectConv(convId) {
    exitConv();
    selectedConvId = convId;
}
function exitConv() {
    selectedConvId = -1;
    $(".incoming_msg").remove();
    $(".outgoing_msg").remove();
}
