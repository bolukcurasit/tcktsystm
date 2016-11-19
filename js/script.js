$(document).ready(function(){
    $(".beSure").click(function(){
        if(!confirm("Emin misiniz?"))
            return false;
    });
});