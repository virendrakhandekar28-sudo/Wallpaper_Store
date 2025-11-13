document.createElement("a")

function handleSubmit(event) {
    event.preventDefault(); // Prevent default form submission

    const formData = new FormData(document.getElementById('contactForm'));
    const attachment = document.getElementById('attachment').files[0];

    // Check if an attachment is selected and its size
    if (attachment && attachment.size > 20 * 1024 * 1024) { // 20MB in bytes
        document.getElementById('form-response').innerText = "Error: Attachment must be less than 20MB.";
        return; // Stop form submission
    }

    // Use fetch or XMLHttpRequest to send the form data to your server
    fetch('your_server_endpoint.php', { // Change to your actual server endpoint
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        document.getElementById('form-response').innerText = data; // Display server response
    })
    .catch(error => {
        console.error('Error:', error);
    });
}


function toggleProfileMenu() {
    const menu = document.getElementById("profileMenu");
    if (menu.style.display === "block") {
        menu.style.display = "none";
    } else {
        menu.style.display = "block";
    }
}

function showLogin() {
    document.getElementById('login-form').style.display = 'block';
    document.getElementById('register-form').style.display = 'none';
    document.getElementById('login-tab').classList.add('active');
    document.getElementById('register-tab').classList.remove('active');
}

function showRegister() {
    document.getElementById('login-form').style.display = 'none';
    document.getElementById('register-form').style.display = 'block';
    document.getElementById('register-tab').classList.add('active');
    document.getElementById('login-tab').classList.remove('active');
}
