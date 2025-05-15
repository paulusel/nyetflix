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
        if(setProfile()) {
            window.location.href = 'home.php';
        }
        else {
            console.log('failed to set profile');
        }
    } catch (error) {
        console.error('server response error:');
        return;
    }
}

async function setProfile() {
    const response = await fetch('/nyetflix/api/getAllProfiles.php', {
        method : 'POST'
    });
    const json = await response.json();
    response = await fetch('/nyetflix/api/setProfile.php', {
        method : 'POST',
        headers : {
            'Content-Type' : 'application/json',
            'Authorization' : 'Bearer ' + localStorage.getItem('token')
        },
        body : JSON.stringify(json.profiles[0].profile_id)
    });
    json = await response.json();
    if(json.ok) {
        localStorage.setItem('token', json.token);
    }

    return json.ok;
}
