async function signup() {
    // extract data from form
    const body = {
        'name' : '', // name here
        'email' : '', // email here
        'password' : '' // password 
    };

    const response = await fetch('api/subscribe.php', {
        method : 'POST',
        body : JSON.stringify(body)
    });

    const result = await response.json();

    if(!result.ok) {
        // failed to signup
    }

    localStorage.setItem('token', result.token);
    // redirect to profiles page
    window.location.href = 'profiles.php';
}
