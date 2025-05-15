document.addEventListener('DOMContentLoaded', () => {
    const manageProfilesBtn = document.getElementById('manage-profiles-btn');
    const profilesList = document.getElementById('profiles-list');
    let isEditMode = false;

    if (manageProfilesBtn && profilesList) {
        manageProfilesBtn.addEventListener('click', toggleEditMode);
        applyProfileUpdates();
    }
    
    function applyProfileUpdates() {
        const profileUpdates = JSON.parse(localStorage.getItem('profileUpdates') || '{}');
        
        if (Object.keys(profileUpdates).length > 0) {
            const profileItems = profilesList.querySelectorAll('.user-profile');
            profileItems.forEach(profile => {
                const profileImg = profile.querySelector('.profile-avatar');
                const profileName = profile.querySelector('.profile-name');
                if (profileImg && profileName) {
                    const profileId = profileImg.alt;
                    if (profileUpdates[profileId]) {
                        const updates = profileUpdates[profileId];
                        
                        // Update name if it changed
                        if (updates.name) {
                            profileName.textContent = updates.name;
                        }
                        
                        // Update image if it changed (for a real implementation)
                        // This would require actual file uploads to work properly
                        // if (updates.image && !updates.image.includes('blob:')) {
                        //     profileImg.src = updates.image;
                        // }
                    }
                }
            });
        }
    }

    function toggleEditMode() {
        isEditMode = !isEditMode;
        if (isEditMode) {
            manageProfilesBtn.textContent = 'Done';
            document.body.classList.add('edit-mode');
            
            const profileItems = profilesList.querySelectorAll('.user-profile');
            profileItems.forEach(profile => {
                const profileLink = profile.querySelector('.profile-link');
                profileLink.setAttribute('data-href', profileLink.href);
                profileLink.href = 'javascript:void(0)';
                const profileImage = profile.querySelector('.profile-avatar');
                if (profileImage) {
                    const editIconOverlay = document.createElement('div');
                    editIconOverlay.className = 'image-overlay';
                    
                    const editIcon = document.createElement('i');
                    editIcon.setAttribute('data-feather', 'edit-2');
                    editIcon.className = 'edit-icon';
                    
                    editIconOverlay.appendChild(editIcon);
                    const rect = profileImage.getBoundingClientRect();
                    
                    editIconOverlay.style.position = 'absolute';
                    editIconOverlay.style.top = '0';
                    editIconOverlay.style.left = '0';
                    editIconOverlay.style.width = profileImage.offsetWidth + 'px';
                    editIconOverlay.style.height = profileImage.offsetHeight + 'px';
                    editIconOverlay.style.borderRadius = '4px';
                    
                    profileImage.style.position = 'relative';
                    profileImage.parentNode.insertBefore(editIconOverlay, profileImage.nextSibling);
                    profileImage.setAttribute('data-has-overlay', 'true');
                }
                profile.addEventListener('click', () => {
                    if (isEditMode) {
                        const profileImg = profile.querySelector('.profile-avatar');
                        const profileName = profile.querySelector('.profile-name').textContent;
                        const profileId = profileImg.alt; // Using alt attribute as profile ID
                        
                        sessionStorage.setItem('editProfileId', profileId);
                        sessionStorage.setItem('editProfileName', profileName);
                        sessionStorage.setItem('editProfileImage', profileImg.src);
                        
                        window.location.href = 'editProfile.php';
                    }
                });
            });
            
            addEditModeStyles();
            
            if (typeof feather !== 'undefined') {
                feather.replace();
            }
        } else {
            manageProfilesBtn.textContent = 'Manage Profiles';
            document.body.classList.remove('edit-mode');
            const profileItems = profilesList.querySelectorAll('.user-profile');
            
            profileItems.forEach(profile => {
                const profileLink = profile.querySelector('.profile-link');
                if (profileLink.hasAttribute('data-href')) {
                    profileLink.href = profileLink.getAttribute('data-href');
                    profileLink.removeAttribute('data-href');
                }
                
                const profileImage = profile.querySelector('.profile-avatar');
                if (profileImage && profileImage.getAttribute('data-has-overlay') === 'true') {
                    const overlay = profileImage.nextElementSibling;
                    if (overlay && overlay.classList.contains('image-overlay')) {
                        overlay.parentNode.removeChild(overlay);
                    }
                    profileImage.style.position = '';
                    profileImage.removeAttribute('data-has-overlay');
                }
                profile.replaceWith(profile.cloneNode(true));
            });
            removeEditModeStyles();
        }
    }

    function addEditModeStyles() {
        let styleEl = document.getElementById('edit-mode-styles');
        if (!styleEl) {
            styleEl = document.createElement('style');
            styleEl.id = 'edit-mode-styles';
            document.head.appendChild(styleEl);
        }
        styleEl.textContent = `
            .image-overlay {
                background-color: rgba(0, 0, 0, 0.5);
                display: flex;
                justify-content: center;
                align-items: center;
                z-index: 10;
                cursor: pointer;
            }
            
            .edit-icon {
                color: #fff;
                font-size: 24px;
                stroke-width: 2;
            }
            
            .user-profile {
                cursor: pointer;
            }
            
            .edit-mode .profile-name {
                color: #fff;
            }
            
            @media screen and (min-width: 1666px) {
                .edit-icon {
                    font-size: 32px;
                }
            }
            
            @media screen and (max-width: 800px) {
                .edit-icon {
                    font-size: 18px;
                }
            }
        `;
    }
    function removeEditModeStyles() {
        const styleEl = document.getElementById('edit-mode-styles');
        if (styleEl) {
            styleEl.textContent = '';
        }
    }
}); 