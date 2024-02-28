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
                'Authorization': 'Bearer ' + getCookie('pak') 
            },
            body: JSON.stringify(formData)
        })
        .then(response => response.json())
        .then(data => {
            console.log(data);
        })
    });
});