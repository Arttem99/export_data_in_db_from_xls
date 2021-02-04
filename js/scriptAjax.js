
document.addEventListener("DOMContentLoaded", view);


function adddb() {
    $.ajax({
        url:"function.php",
        type: "POST",
        dataType: "JSON",
        cache:false,
        data: {
            action: "add"
        },
        success: function (data) {
            if (data.result =='true') {
                $('#success_message').fadeIn().html("Выполено!");
                setTimeout(function () {
                    $('#success_message').fadeOut("Slow");
                }, 2000);
                view();
            }
            else {
                $('#error_message').fadeIn().html("Ошибка!");
                setTimeout(function () {
                    $('#error_message').fadeOut("Slow");
                }, 2000);
            }
        }

    });
}

function view(){
    $.ajax({
        type: "POST",
        url: "function.php",
        cache:false,
        data: {
            method:"view"
        },
        success: function (data) {
            $("#table_container").html(data);
        }
    });
}

$("#load_btn").click(function (e) {
    e.preventDefault();
    view();
});

var typePrice =$("#typePrice").val();
$("#typePrice").change(function () {
    typePrice = $("#typePrice").val();
});

var minPrice = $("#minPrices").val();
$("#minPrices").change(function () {
    minPrice = $("#minPrices").val();
});
var maxPrice = $("#maxPrices").val();
$("#maxPrices").change(function () {
    maxPrice = $("#maxPrices").val();
});

$('.numbersOnly').keyup(function(e) {
    if(this.value!='-')
        while(isNaN(this.value))
            this.value = this.value.split('').reverse().join('').replace(/[\D]/i,'')
                .split('').reverse().join('');
}).on("cut copy paste",function(e){
        e.preventDefault();
    });
$('.number-only').keypress(function(e) {
    if(isNaN(this.value+""+String.fromCharCode(e.charCode))) return false;
})
    .on("cut copy paste",function(e){
        e.preventDefault();
    });

function numbersP(oToCheckField, oKeyEvent) {
    return oKeyEvent.charCode === 0 ||
        /\d/.test(String.fromCharCode(oKeyEvent.charCode));
}

var more_less = $("#more_less").val();
$("#more_less").change(function () {
    more_less = $("#more_less").val();
});

var counts = $("#counts").val();
$("#counts").change(function () {
    counts = $("#counts").val();
});

$("#btn_view_filter").click(function (e) {
    e.preventDefault();
$.ajax({
    url:"function.php",
    type:"POST",
    data:{
        typePrice:typePrice,
        minPrice:minPrice,
        maxPrice:maxPrice,
        more_less:more_less,
        counts:counts,
        method: "getResult"
    },
    success:function (data) {
        $("#table_container").html("");
        $("#table_container").html(data);
    }
});


});




