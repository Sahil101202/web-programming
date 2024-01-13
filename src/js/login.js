const myForm = document.querySelector('.form');

myForm.addEventListener('submit', event =>{
    event.preventDefault();
    //now getting the data from the form
    const formData = new FormData(myForm);
    const arrayData = {
        "email" : formData.get('email'),
        "password" : formData.get('psw')
    };
    //converting array to jason object
    var data = JSON.stringify(arrayData);

    fetch('http://localhost:8080/php/login.php', {
        method : 'POST',
        headers : {
            'content-Type' : 'application/json'
        },
        body : data
    })
    .then(response => {
        console.log(response.status);

        if(response.ok){
            return response.json();
        }else{
            throw new Error('Network response was not ok.');
        }
    })
    .then(data => {
        if(data.success){
            if(data.message === "Admin Login successful!"){
                const token1 = data.token;
                localStorage.setItem("token_admin", token1);
                // Redirect to the admin page
                window.location.href = '../html/admin/dashboard.html';
                // alert("Admin Login Successful!!");
            }else{
                const token1 = data.token;
                // console.log(token1);
                localStorage.setItem("token_user", token1);
                // Redirect to the user page
                window.location.href = '../html/index.html';
                // alert("Login Successful!!");
            }
        }else{
            alert(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
});