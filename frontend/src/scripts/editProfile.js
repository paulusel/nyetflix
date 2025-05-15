document.addEventListener('DOMContentLoaded', () => {
    const profileForm = document.querySelector('.profile-form');
    const profileAvatar = document.getElementById('profileAvatar');
    const imageOverlay = document.querySelector('.image-overlay');
    const displayNameInput = document.getElementById('displayName');
    const passwordInput = document.getElementById('password');
    const newPasswordInput = document.getElementById('newPassword');
    const confirmPasswordInput = document.getElementById('confirmPassword');
    const saveButton = document.querySelector('.btn-save');
    const cancelButton = document.querySelector('.btn-cancel');
    const deleteProfileButton = document.querySelector('.btn-delete-profile');
    const header = document.querySelector('.simple-header');

    loadUserProfile();
    imageOverlay.addEventListener('click', openAvatarSelector);
    
    saveButton.addEventListener('click', saveProfile);
    cancelButton.addEventListener('click', () => {
        if (confirm('Discard changes?')) {
            window.location.href = 'profile.php';
        }
    });
    
    deleteProfileButton.addEventListener('click', confirmDeleteProfile);

    window.addEventListener('scroll', () => {
        if (window.scrollY > 0) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }
    });
    if (displayNameInput) {
        displayNameInput.addEventListener('input', validateDisplayName);
    }
    
    if (newPasswordInput) {
        newPasswordInput.addEventListener('input', validatePassword);
    }
    
    if (confirmPasswordInput) {
        confirmPasswordInput.addEventListener('input', validatePasswordMatch);
    }

    function loadUserProfile() {
        const profileId = sessionStorage.getItem('editProfileId');
        const profileName = sessionStorage.getItem('editProfileName');
        const profileImage = sessionStorage.getItem('editProfileImage');
        
        if (profileId && profileName) {
            const titleElement = document.querySelector('h1');
            if (titleElement) {
                titleElement.textContent = `Edit Profile: ${profileName}`;
            }
            if (displayNameInput) {
                displayNameInput.value = profileName;
            }
            
            if (profileAvatar && profileImage) {
                profileAvatar.src = profileImage;
            }
            if (profileForm) {
                profileForm.setAttribute('data-profile-id', profileId);
            }
        } else {
            setTimeout(() => {
                const userData = {
                    displayName: 'Name',
                    avatarUrl: '../assets/images/Avatars/default.png'
                };

                if (displayNameInput) {
                    displayNameInput.value = userData.displayName;
                }
                if (profileAvatar) {
                    profileAvatar.src = userData.avatarUrl;
                }
            }, 500);
        }
    }

    function openAvatarSelector() {
        const fileInput = document.createElement('input');
        fileInput.type = 'file';
        fileInput.accept = 'image/*';
        fileInput.addEventListener('change', handleAvatarChange);
        fileInput.click();
    }

    function handleAvatarChange(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                profileAvatar.src = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    }

    function validateDisplayName() {
        const value = displayNameInput.value.trim();
        const isValid = value.length >= 2;
        
        setFieldValidity(displayNameInput, isValid, 'Name must be at least 2 characters');
        return isValid;
    }
    
    function validatePassword() {
        if (!newPasswordInput.value) return true;
        
        const value = newPasswordInput.value;
        const isValid = value.length >= 6;
        
        setFieldValidity(newPasswordInput, isValid, 'Password must be at least 6 characters');
        return isValid;
    }

    function validatePasswordMatch() {
        if (!newPasswordInput.value) return true; 
        
        const isValid = newPasswordInput.value === confirmPasswordInput.value;
        
        setFieldValidity(confirmPasswordInput, isValid, 'Passwords do not match');
        return isValid;
    }

    function setFieldValidity(field, isValid, errorMessage) {
        const existingError = field.parentElement.querySelector('.error-message');
        if (existingError) {
            existingError.remove();
        }

        if (!isValid) {
            const errorElement = document.createElement('div');
            errorElement.className = 'error-message';
            errorElement.textContent = errorMessage;
            errorElement.style.color = '#e50914';
            errorElement.style.fontSize = '0.8rem';
            errorElement.style.marginTop = '5px';
            field.parentElement.appendChild(errorElement);
            field.style.borderColor = '#e50914';
        } else {
            field.style.borderColor = '';
        }
    }
    
    function validateForm() {
        const isDisplayNameValid = displayNameInput ? validateDisplayName() : true;
        const isPasswordValid = newPasswordInput ? validatePassword() : true;
        const isPasswordMatchValid = confirmPasswordInput ? validatePasswordMatch() : true;
        
        return isDisplayNameValid && isPasswordValid && isPasswordMatchValid;
    }

    function saveProfile() {
        if (!validateForm()) {
            alert('Please fix the errors before saving.');
            return;
        }

        const profileId = profileForm.getAttribute('data-profile-id') || 'default';
        const newName = displayNameInput ? displayNameInput.value.trim() : '';
        const newImageSrc = profileAvatar.src;
        
        const profileData = {
            profileId: profileId,
            displayName: newName
        };

        if (newPasswordInput && newPasswordInput.value) {
            profileData.currentPassword = passwordInput.value;
            profileData.newPassword = newPasswordInput.value;
        }
        
        console.log('Saving profile data:', profileData);

        saveButton.textContent = 'Saving...';
        saveButton.disabled = true;

        sessionStorage.setItem('editProfileId', profileId);
        sessionStorage.setItem('editProfileName', newName);
        sessionStorage.setItem('editProfileImage', newImageSrc);
        
        const profileUpdates = JSON.parse(localStorage.getItem('profileUpdates') || '{}');
        profileUpdates[profileId] = {
            name: newName,
            image: newImageSrc
        };
        localStorage.setItem('profileUpdates', JSON.stringify(profileUpdates));

        setTimeout(() => {
            saveButton.textContent = 'Save';
            saveButton.disabled = false;
            
            alert('Profile updated successfully!');
            window.location.href = 'profile.php';
        }, 1500);
    }

    function confirmDeleteProfile() {
        const modal = document.createElement('div');
        modal.className = 'delete-profile-modal';
        
        modal.innerHTML = `
            <div class="delete-profile-modal-content">
                <h2>Delete Your Profile?</h2>
                <p>This will permanently delete your profile and all associated data.</p>
                <p>You will lose your watch history, preferences, and saved lists.</p>
                <p class="warning">This action cannot be undone.</p>
                
                <div class="confirm-password">
                    <label for="deleteConfirmPassword">Enter your password to confirm:</label>
                    <input type="password" id="deleteConfirmPassword" placeholder="Password">
                </div>
                
                <div class="modal-actions">
                    <button class="btn-confirm-delete">Delete Profile</button>
                    <button class="btn-cancel-delete">Cancel</button>
                </div>
            </div>
        `;
        
        document.body.appendChild(modal);
        
        const cancelDeleteBtn = modal.querySelector('.btn-cancel-delete');
        const confirmDeleteBtn = modal.querySelector('.btn-confirm-delete');
        const passwordInput = modal.querySelector('#deleteConfirmPassword');
        
        cancelDeleteBtn.addEventListener('click', () => {
            document.body.removeChild(modal);
        });
        
        confirmDeleteBtn.addEventListener('click', () => {
            if (!passwordInput.value) {
                alert('Please enter your password to confirm deletion.');
                return;
            }
            
            confirmDeleteBtn.textContent = 'Deleting...';
            confirmDeleteBtn.disabled = true;
            
            setTimeout(() => {
                document.body.removeChild(modal);
                alert('Your profile has been deleted.');
                window.location.href = 'profile.php';
            }, 1500);
        });
    }
}); 