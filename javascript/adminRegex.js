function sjekkAdmin() {
    var regEx = /^\b(?!\badmin\b)\w+\b/;
            var ok = regEx.test(document.reg_login.lageBrukernavn.value);
    if (!ok) {
        document.getElementById("error_create").innerHTML = "Username is invalid";
        return false;
    }else{
        document.getElementById("error_update").innerHTML = "";
        document.getElementById("error_create").innerHTML = "";  
        return true;
    }
}