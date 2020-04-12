// Turns the stack of cards into one column if the screen is less than 540px wide

// Selecting all cards and storing in a handle
const cards = document.querySelectorAll('#stack');

const cardAdapt = () => {
  console.log(window.innerWidth);
  // Converts to one column if the width is less than 540px
  if(window.innerWidth < 540) {
    cards.forEach(card => {
      card.classList.remove('col');
    });
  } else { // Converts to two columns otherwise
    cards.forEach(card => {
      card.classList.add('col');
    });
  }
}

// Runs the cardAdapt function everytime the window is resized
window.addEventListener('resize', cardAdapt);
// Function call for the first time the user opens the page
cardAdapt();

///////////////////////////////////////////////////////////////////////////////////////////////////

// Decides at what width to turn the navbar into a dropdown depending on the length of the member's names

// Setting handle and width for the navbar
const navbar = document.querySelector('#nav-mobile');
const navbarWidth = navbar.offsetWidth;

// Setting handle for the dropdown
const adaptDrop = document.querySelector('.adaptive-drop');

// Setting handle for all the different types of logos
const logoImg1 = document.querySelector('.logo-link1');
const logoImg2 = document.querySelector('.logo-link2');
const logoImg3 = document.querySelector('.logo-link3');

let logoImgWidth;

const navAdapt = () => {
  // Setting the logoImgWidth based on what logo is being shown
  if(window.innerWidth <= 380) {
    logoImgWidth = logoImg3.offsetWidth;
    console.log('Logo width: ' + logoImgWidth);
  } else if(window.innerWidth <= 549) {
    logoImgWidth = logoImg2.offsetWidth + 20;
    console.log('Logo width: ' + logoImgWidth);
  } else {
    logoImgWidth = logoImg1.offsetWidth;
    console.log('Logo width: ' + logoImgWidth);
  }

  // Setting the max width of the navbar based on the width of the logo and the window
  let maxNavWidth = window.innerWidth - (logoImgWidth + 25);
  console.log('Max width: ' + maxNavWidth);

  // Hides the navbar if its width exceeds the max width; shows the dropdown
  if(navbarWidth >= maxNavWidth) {
    navbar.classList.add('hide');
    adaptDrop.classList.add('appear');
  } else if(navbarWidth <= maxNavWidth) { // Does the opposite if the navbar width is under the max width
    navbar.classList.remove('hide');
    adaptDrop.classList.remove('appear');
  }
}

// Executes the function everytime the window is resized
window.addEventListener('resize', navAdapt);
// Executes the function for the first time when the window is reloaded 
navAdapt();
