document.addEventListener("DOMContentLoaded", function() {
    // Escuchar el evento submit del formulario
    document.getElementById("app-login-form").addEventListener("submit", function(event) {
        event.preventDefault();
        
        var username = document.getElementById("login-username").value;
        var password = document.getElementById("login-password").value;
        var persist = document.getElementById("login-persist").checked;

        var formData = {
            username: username,
            password: password,
            persist: persist
        };

        var jsonData = JSON.stringify(formData);

        fetch(website + '/endpoints/login.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': 'Bearer ' + api_keys[1] 
            },
            body: JSON.stringify(formData)
        })
        .then(response => response.json())
        .then(data => {
            api_keys[1] = data.data;
            if(data.status == 200){
                let referral = getCookie('referral')
                if(referral.includes("http")){
                    window.location.href = referral;
                }
                else{
                    window.location.href = website;
                }
            }
            if(data.status >= 400) {
                document.getElementById("error-message").innerText = data.message;
                document.getElementById("error-message").style.display = "block";
            }
            // Puedes agregar más lógica aquí según la respuesta recibida
        })
    });
});
