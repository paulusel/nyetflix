import MovieManager from './movieManager.js';

const nav = document.querySelector('nav');

window.addEventListener('scroll', () => {
  if (window.scrollY > 0) {
    nav.style.backgroundColor = 'rgb(20,20,20)'; 
  } else {
    nav.style.backgroundColor = 'transparent';
  }
});

const footer = document.querySelector('.service-code');

if (footer) {
  footer.addEventListener('click', () => {
    footer.innerHTML = '286-128';
  });
}

function selectProfile(profileId, element) {
  localStorage.setItem('selectedProfile', profileId);
  
  if (element) {
    const profileNameElement = element.querySelector('.profile-name');
    if (profileNameElement) {
      const profileName = profileNameElement.textContent;
      localStorage.setItem('selectedProfileName', profileName);
    }
  }
}

window.selectProfile = selectProfile;

document.addEventListener('DOMContentLoaded', () => {
  const selectedProfile = localStorage.getItem('selectedProfile');
  const selectedProfileName = localStorage.getItem('selectedProfileName');
  
  if (selectedProfile) {
    const profileIcon = document.querySelector('.nav-item.icon img');
    if (profileIcon) {
      profileIcon.src = `../assets/images/Icon/Profile/${selectedProfile}.png`;
      
      const profileNameElement = document.querySelector('.profile-dropdown-name');
      if (profileNameElement && selectedProfileName) {
        profileNameElement.textContent = selectedProfileName;
      }
    }
  }
  
  if (typeof MovieManager !== 'undefined' && MovieManager.init) {
    MovieManager.init();
  }
});