function validateForm(){
    let user=document.getElementById("username").value.trim();
    let pass=document.getElementById("password").value.trim();
    let valid=true;

    document.getElementById("userError").innerHTML="";
    document.getElementById("passError").innerHTML="";

    if(user===""){
        document.getElementById("userError").innerHTML="Username is required";
        valid=false;
    }

    if(pass===""){
        document.getElementById("passError").innerHTML="Password is required";
        valid=false;
    }else if(pass.length<4){
        document.getElementById("passError").innerHTML="Minimum 4 characters";
        valid=false;
    }

    return valid;
}