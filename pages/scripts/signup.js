async function signup() {
    // extract data from form
    const body = {
        'name' : document.getElementById('name-input').value,
        'email' : document.getElementById('email-input').value,
        'password' : document.getElementById('password-input').value
    };

    if(!body.name || !body.email | !body.password) {
        // TODO: show message?
        console.log('fields missing');
        return;
    }

    const response = await fetch('/nyetflix/api/subscribe.php', {
        method : 'POST',
        headers : {
            'Content-Type' : 'application/json'
        },
        body : JSON.stringify(body)
    });

    try {
        const result = await response.json();
        if(!result.ok) {
            // TODO: show message?
            console.log('request failed: ' + result.message);
            return;
        }

        localStorage.setItem('token', result.token);
        window.location.href = 'profile.php';
    } catch (error) {
        console.error('server response error:');
        return;
    }
}
