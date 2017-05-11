function sjekkAdmin() {
    var regEx = /^\b(?!\bword\b)\w+\b/;
            var ok = regEx.test(document.reg_login.lageBrukernavn.value);
    if (!ok) {
        document.getElementById("error_create").innerHTML = "Username is invalid";
        console.log("hei");
        return false;
    }else{
        document.getElementById("error_update").innerHTML = "";
        document.getElementById("error_create").innerHTML = "";
        console.log("herro");   
        return true;
    }
}