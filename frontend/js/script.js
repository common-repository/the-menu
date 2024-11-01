const targetElement = document.querySelector('.tm-scrolling');

function setOpacity(opacity) {
  if (targetElement) {
    targetElement.style.opacity = opacity;
  }
}

let scrollTimeout = null;

if (targetElement) {
  window.addEventListener('scroll', () => {
    setOpacity(0.2);
    clearTimeout(scrollTimeout);
    scrollTimeout = setTimeout(() => {
      setOpacity(1);
    }, 150);
  });
}

document.addEventListener("DOMContentLoaded", function() {
    const icon = document.querySelector('.tm-featured');
    const menuPopup = document.querySelector('.tm-addon-menu-wrapper');

    if (icon && menuPopup) {
        icon.addEventListener('click', function() {
            const isShown = menuPopup.classList.contains('show');
            if (!isShown) {
                menuPopup.classList.add('show');
                menuPopup.style.display = 'flex';
                menuPopup.style.animation = 'slideIn 0.5s forwards';
                icon.classList.add('active');
            } else {
                menuPopup.style.animation = 'slideOut 0.5s forwards';
                icon.classList.remove('active');
                setTimeout(() => {
                    menuPopup.classList.remove('show');
                    menuPopup.style.display = 'none';
                }, 500);
            }
        });

        document.addEventListener('click', function(event) {
            if (!icon.contains(event.target) && !menuPopup.contains(event.target) && menuPopup.classList.contains('show')) {
                menuPopup.style.animation = 'slideOut 0.5s forwards';
                icon.classList.remove('active');
                setTimeout(() => {
                    menuPopup.classList.remove('show');
                    menuPopup.style.display = 'none';
                }, 500);
            }
        });
    }

    const links = document.querySelectorAll('.the-menu a');

    links.forEach(function(link) {
        link.addEventListener('click', function(e) {
            const href = this.getAttribute('href');

            if (href.includes('#')) {
                e.preventDefault();
                return;
            }

            var pageLoader = document.getElementById('tm-pageLoader');
            if (pageLoader) {
                pageLoader.style.display = 'block';
            }

            e.preventDefault();

            setTimeout(function() {
                window.location.href = href;
            }, 100);
        });
    });

    const featured = document.querySelector('.tm-featured');
    const featuredBg = document.querySelector('.tm-featured-bg');

    if (featured && featuredBg) {
        featured.addEventListener('click', function() {
            featuredBg.classList.toggle('expanded');
        });
    }
});