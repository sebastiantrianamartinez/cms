const usernameInput = document.getElementById('user_name');
const usernameInfo = document.getElementById('username-info');
const usernameImg = document.getElementById('username-status-img');
var timeout;

function handleFormSubmit(event, service) {
    event.preventDefault();
    joinApiRequest(api_keys[service]);
}

function nameApiRequest(api_key){
    usernameInfo.style.color = "#FF5757";
    if(usernameInput.value.length < 4){
        usernameImg.style.display = "none";
        usernameInfo.textContent = "Username should be bigger";
        return 0;
    }
    if(/\s/.test(usernameInput.value)){
        usernameImg.style.display = "none";
        usernameInfo.textContent = "You can't use spaces";
        return 0;
    } 
    if (!/^[a-zA-Z0-9_.]*$/.test(usernameInput.value)) {
        usernameImg.style.display = "none";
        usernameInfo.textContent = "You only can use '_' or '.'";
        return 0;
    }

    var requestData = {
        "user_name": usernameInput.value
    }
    const jsonData = JSON.stringify(requestData)
    fetch(website + "/endpoints/join.php", {
        method: "PUT",
        body: jsonData,
        headers: {
            "Authorization": "Bearer " + api_key
        }
    })
    .then(response => response.json())
    .then(data => {
        usernameInfo.style.display = "block";
        usernameInfo.textContent = data.message;
        usernameImg.style.display = "block";
        if(data.status == 200){
            usernameInfo.style.color = "#7ED957";
            usernameImg.setAttribute("src", website + "/assets/media/images/ok.png");
        }
        else{
            usernameImg.setAttribute("src", website + "/assets/media/images/error.png");
        }
    });
}


function joinApiRequest(api_key){
    const form = document.getElementById("join-form");
    const formData = new FormData(form);

    fetch(website + "/endpoints/join.php", {
        method: "POST",
        body: formData,
        headers: {
            "Authorization": "Bearer " + api_key
        }
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message);
    });
}

usernameInput.addEventListener("input", function(event){
    clearTimeout(timeout);
    timeout = setTimeout(function(){
        nameApiRequest(api_keys["3"]);
    }, 700);
});
