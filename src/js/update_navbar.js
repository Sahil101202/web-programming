const token1 = localStorage.getItem("token_user");
const arrayData = {
    "token" : token1,
};
//converting array to jason object
var data = JSON.stringify(arrayData);

//function to update the navbar as per login status.
function updateNavigationBar(isLoggedIn) {
    const navbar = document.getElementById('navbar');
    const registerLink = document.querySelector('a[href="register.html"]');
    const loginLink = document.querySelector('a[href="login.html"]');
    const profileLink = document.createElement('li');
    const logoutLink = document.createElement('li');
    const cart = document.createElement('li');

    if (isLoggedIn) {
        // User is logged in, replace Register and Login with Profile and Logout
        if (registerLink) {
            navbar.removeChild(registerLink.parentElement);
        }
        if (loginLink) {
            navbar.removeChild(loginLink.parentElement);
        }

        profileLink.innerHTML = '<a href="profile.html">Profile</a>';
        logoutLink.innerHTML = '<a onclick="logout()">Logout</a>';
        cart.innerHTML = '<a href="cart.html">Favourites</a>';

        console.log(logoutLink);

        // Add profile and logout to the right side
        navbar.appendChild(logoutLink);
        navbar.appendChild(profileLink);
        navbar.appendChild(cart);

    }
}

fetch('http://localhost:8080/php/test_token.php', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
    },
    body: data,
})
.then(response => {
    console.log(response.status); // Log the status code
    return response.json(); // or response.json() if you want to see the entire response
})
.then(data => {
    // Log the parsed JSON response
    console.log(data);

    // Check if registration was successful
    if (data.success) {
        isLoggedIn = data.success;
        console.log(isLoggedIn);
        window.addEventListener('load', updateNavigationBar(isLoggedIn));
    }
})
.catch(error => {
    console.error('Error:', error);
});


