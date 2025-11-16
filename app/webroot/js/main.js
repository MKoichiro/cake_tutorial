const accordion = document.querySelector('#header-menu-acc-container"');
const menuOpenBtn = document.querySelector('#header-btn btn-toggle-header-menu');

menuOpenBtn.addEventListener('click', () => {
  accordion.classList.toggle('open');
});
