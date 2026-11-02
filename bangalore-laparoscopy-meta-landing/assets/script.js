(() => {
  const menuButton = document.querySelector('.menu-toggle');
  const nav = document.querySelector('.main-nav');
  if (menuButton && nav) {
    menuButton.addEventListener('click', () => {
      const open = nav.classList.toggle('open');
      menuButton.setAttribute('aria-expanded', open ? 'true' : 'false');
    });
    nav.querySelectorAll('a').forEach(a => a.addEventListener('click', () => {
      nav.classList.remove('open'); menuButton.setAttribute('aria-expanded', 'false');
    }));
  }

  const popup = document.getElementById('consultPopup');
  const close = popup?.querySelector('.popup-close');
  const key = 'blcConsultPopupShown_v1';
  const showPopup = () => {
    if (!popup || sessionStorage.getItem(key)) return;
    popup.classList.add('show');
    popup.setAttribute('aria-hidden', 'false');
    sessionStorage.setItem(key, '1');
  };
  if (popup && !sessionStorage.getItem(key)) window.setTimeout(showPopup, 30000);
  const hidePopup = () => {
    popup?.classList.remove('show');
    popup?.setAttribute('aria-hidden', 'true');
  };
  close?.addEventListener('click', hidePopup);
  popup?.addEventListener('click', e => { if (e.target === popup) hidePopup(); });
  document.addEventListener('keydown', e => { if (e.key === 'Escape') hidePopup(); });
  document.getElementById('popupBook')?.addEventListener('click', hidePopup);
})();
