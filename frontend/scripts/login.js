function handleFormSubmit(event, service) {
    event.preventDefault();
    loginApiRequest(api_keys[service]);
}

function loginApiRequest(api_key){
    const form = document.getElementById("login-form");
    const formData = new FormData(form);
    const panel = document.getElementById("form-info-panel");
    const info = document.getElementById("form-info-panel-msg");

    fetch(website + "/endpoints/login.php", {
        method: "POST",
        body: formData,
        headers: {
            "Authorization": "Bearer " + api_key
        }
    })
    .then(response => response.json())
    .then(data => {
        info.textContent = "ⓘ " + data.message;
        if(data.status == 200){
            if(data.data.redirect == "root"){
                panel.style.backgroundColor = '#58EB81';
                setTimeout(function(){
                    window.location.href = website;
                }, 3000);
            }
        }
        else{
            panel.style.backgroundColor = "#EB6C50"
        }
    });
}