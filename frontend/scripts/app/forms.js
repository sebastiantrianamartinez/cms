function showPassword(element, parent){
    rotatePasswordImage(element.id);
    const passwordInput = document.getElementById(parent);
    var atribute = (passwordInput.getAttribute('type') == "password") ? "text" : "password";
    passwordInput.setAttribute('type', atribute);
}

function rotatePasswordImage(id){
    id = (id == null) ? 'show-password-image' : id;
    const image = document.getElementById(id);
    var deg = image.style.transform;
    deg = (deg == "rotate(180deg)") ? '0deg' : '180deg'; 
    image.style.transform = 'rotate(' + deg + ')';
}