async function signin() {
    const body = {
        'email' : document.getElementById('email-input').value,
        'password' : document.getElementById('password-input').value
    };

    const response = await fetch('/nyetflix/api/authenticate.php', {
        method : 'POST',
        headers : {
            'Content-Type' : 'application/json'
        },
        body : JSON.stringify(body)
    });

    const json = await response.json();
    if(!json.ok) {
        console.log('api call failed: ' + json.message);
        return;
    }

    localStorage.setItem('token', json.token);
    handleProfile();
}

async function handleProfile() {
    let response = await fetch('/nyetflix/api/getAllProfiles.php', {
        method : 'POST',
        headers : {
            'Authorization' : 'Bearer ' + localStorage.getItem('token')
        }
    });

    let json = await response.json();
    if(json.profiles.length > 1) {
        window.location.href = 'profile.php';
    }
    else {
        const response = await fetch('/nyetflix/api/setProfile.php', {
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
            window.location.href = 'home.php';
        }
        else {
            console.log('setting profile failed: ' + json.message);
        }
    }
}
