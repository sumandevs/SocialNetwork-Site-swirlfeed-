$(document).ready(function(){
    //show the register form
    $("#signup").click(function(){
        $("#first").slideUp(800,function(){
            $("#second").slideDown(800);
        });
    })
    //show the login form
    $("#signin").click(function(){
        $("#second").slideUp(800,function(){
            $("#first").slideDown(800);
        });
    })
});