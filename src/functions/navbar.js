document.addEventListener('DOMContentLoaded', (event) => {
    const menuButton = document.getElementById('menu-button');
    const navbarDropdown = document.getElementById('navbar-dropdown');
    
    menuButton.addEventListener('click', () => {
        navbarDropdown.classList.toggle('hidden');
    });
});

document.addEventListener('DOMContentLoaded', (event) => {
    const dropdownButton = document.getElementById('dropdownInformationButton');
    const dropdown = document.getElementById('dropdownInformation');
    
    dropdownButton.addEventListener('click', () => {
        dropdown.classList.toggle('hidden');
    });
});

