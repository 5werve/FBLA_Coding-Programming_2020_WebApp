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

// Runs the cardAdapt function every time the window is resized
window.addEventListener('resize', cardAdapt);
// Function call for the first time the user opens the page
cardAdapt();
