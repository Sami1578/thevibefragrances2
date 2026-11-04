(function () {
  'use strict';
  var $ = function (s, c) { return (c || document).querySelector(s); };
  var $$ = function (s, c) { return Array.prototype.slice.call((c || document).querySelectorAll(s)); };
  var csrf = ($('meta[name="csrf"]') || {}).content || '';
  var base = (function () {
    var s = document.currentScript || $('script[src*="assets/js/main.js"]');
    return s ? s.src.replace(/assets\/js\/main\.js.*$/, '') : '';
  })();

  /* Toast */
  var toastEl = $('#toast'), toastT;
  function toast(msg) {
    if (!toastEl) return;
    toastEl.textContent = msg;
    toastEl.classList.add('show');
    clearTimeout(toastT);
    toastT = setTimeout(function () { toastEl.classList.remove('show'); }, 2600);
  }

  /* Header shadow + mobile menu */
  var header = $('#siteHeader');
  window.addEventListener('scroll', function () {
    header && header.classList.toggle('scrolled', window.scrollY > 20);
  }, { passive: true });
  var burger = $('#burger'), menu = $('#menu');
  burger && burger.addEventListener('click', function () {
    burger.classList.toggle('open');
    menu.classList.toggle('open');
  });

  /* Add to cart (AJAX) */
  document.addEventListener('click', function (e) {
    var btn = e.target.closest('[data-add]');
    if (!btn) return;
    e.preventDefault();
    var qtySel = btn.getAttribute('data-qty-input');
    var qty = qtySel ? (parseInt(($(qtySel) || {}).value, 10) || 1) : 1;
    var fd = new FormData();
    fd.append('action', 'add');
    fd.append('id', btn.getAttribute('data-add'));
    fd.append('qty', qty);
    fd.append('csrf', csrf);
    btn.classList.add('loading');
    fetch(base + 'cart_action.php', {
      method: 'POST', body: fd, headers: { 'X-Requested-With': 'XMLHttpRequest' }
    }).then(function (r) { return r.json(); }).then(function (d) {
      btn.classList.remove('loading');
      if (!d.ok) { toast(d.msg || 'Something went wrong'); return; }
      var c = $('#cartCount');
      if (c) {
        c.textContent = d.count;
        c.style.display = d.count ? '' : 'none';
        c.classList.remove('pop'); void c.offsetWidth; c.classList.add('pop');
      }
      toast('✓ ' + (d.msg || 'Added to cart'));
    }).catch(function () {
      btn.classList.remove('loading');
      toast('Network error, please try again');
    });
  });

  /* Quantity steppers */
  $$('[data-qty]').forEach(function (box) {
    var input = $('input', box);
    $('[data-dec]', box).addEventListener('click', function () {
      input.value = Math.max(1, (parseInt(input.value, 10) || 1) - 1);
      input.dispatchEvent(new Event('change'));
    });
    $('[data-inc]', box).addEventListener('click', function () {
      input.value = Math.min(20, (parseInt(input.value, 10) || 1) + 1);
      input.dispatchEvent(new Event('change'));
    });
  });
  $$('[data-autosubmit]').forEach(function (i) {
    i.addEventListener('change', function () { i.form.submit(); });
  });

  /* Product gallery thumbs */
  $$('.g-thumbs button').forEach(function (b) {
    b.addEventListener('click', function () {
      var main = $('#gMainImg');
      if (main) main.src = b.getAttribute('data-src');
      $$('.g-thumbs button').forEach(function (x) { x.classList.remove('on'); });
      b.classList.add('on');
    });
  });

  /* Hero slider */
  var hero = $('#hero');
  if (hero) {
    var slides = $$('.slide', hero), dots = $$('#heroDots button'), idx = 0, timer;
    var go = function (n) {
      idx = (n + slides.length) % slides.length;
      slides.forEach(function (s, i) { s.classList.toggle('active', i === idx); });
      dots.forEach(function (d, i) { d.classList.toggle('on', i === idx); });
    };
    var play = function () { clearInterval(timer); if (slides.length > 1) timer = setInterval(function () { go(idx + 1); }, 6000); };
    var prev = $('#heroPrev'), next = $('#heroNext');
    prev && prev.addEventListener('click', function () { go(idx - 1); play(); });
    next && next.addEventListener('click', function () { go(idx + 1); play(); });
    dots.forEach(function (d, i) { d.addEventListener('click', function () { go(i); play(); }); });
    var sx = 0;
    hero.addEventListener('touchstart', function (e) { sx = e.touches[0].clientX; }, { passive: true });
    hero.addEventListener('touchend', function (e) {
      var dx = e.changedTouches[0].clientX - sx;
      if (Math.abs(dx) > 50) { go(idx + (dx < 0 ? 1 : -1)); play(); }
    });
    play();
  }

  /* Scroll reveal */
  var rev = $$('.reveal');
  if ('IntersectionObserver' in window) {
    var io = new IntersectionObserver(function (es) {
      es.forEach(function (en) { if (en.isIntersecting) { en.target.classList.add('in'); io.unobserve(en.target); } });
    }, { threshold: 0.12 });
    rev.forEach(function (r) { io.observe(r); });
  } else { rev.forEach(function (r) { r.classList.add('in'); }); }
})();
