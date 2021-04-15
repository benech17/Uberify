$(function() {
    $(".list1, .list2").sortable({
        connectWith: ".sortable"
    });
});

// recharge la page 
function reloadData(argument) {
    window.location.reload();
}

// retourne la solution fournis par l'utilisateur
function getArray() {
    var indexArray = [];
    var lis = document.getElementById("list2")
        .querySelectorAll('li');
    lis.forEach((item) => {
        indexArray.push(item.id);
    });
    return indexArray;
}
function giveFeedback(num,exoid){
    $.ajax({
        type: 'GET',
        url: '/exercices/'+exoid+'/vote/'+num.id[1],
        dataType: "json",
    })
        .done(function(data) {
            var json = JSON.parse(data);
            console.log(data);
            if (json["result"] == 's') {
                $('#rating').html("👍")
                setTimeout(function () {
                    $('#feedbackModal').modal('hide');
                }, 2000);
            }
        })
        .fail(function(jqXHR, textStatus) {
            alert('Veuillez déplacer au moins une case ');
        })
        .always(function(jqXHR, textStatus) {
            //window.document.location = '/';
        });
    // /exercices/{id}/vote/{feedback}
}
// Envoi les donnée post via une requete async. ajax
function postRequest() {
    var indexArray = getArray();
    //console.log(indexArray);
    // var urlToGo = $('#path-to-controller').data("href");
    $.ajax({
        type: 'POST',
        url: $('#path-to-controller').data("href"),
        dataType: "json",
        data: {
            'indexArray': indexArray
        },
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

            var resultView = document.getElementById("result");
            if (json["result"] == 'f') {
                resultView.innerHTML = '<span id="result" class="card-title center red-text">Echec</span>';
                alert("Dommage ... reesayez la prochaine fois !");

            } else {
                resultView.innerHTML = '<span id="result" class="card-title center green-text">Succès</span>';
                $("#feedbackModal").modal()
            }

        })
        .fail(function(jqXHR, textStatus) {
            alert('Veuillez déplacer au moins une case ');
        })
        .always(function(jqXHR, textStatus) {
            //window.document.location = '/';
        });
}
