// Like tanpa reload + double-tap untuk like (ala Instagram).
// - Klik tombol hati: fetch ke like.php, hati langsung merah + jumlah langsung berubah, halaman TIDAK reload.
// - Ketuk 2x cepat pada foto/kotak teks postingan: like (kalau belum), plus animasi hati besar di tengah.
// - Kalau belum login, tombol hati adalah link ke login.php (bukan tombol mati).
document.addEventListener('DOMContentLoaded', function () {
  var HEART_SVG = '<svg viewBox="0 0 24 24" fill="#fff"><path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.6l-1-1a5.5 5.5 0 0 0-7.8 7.8l1 1L12 21.2l7.8-7.8 1-1a5.5 5.5 0 0 0 0-7.8z"/></svg>';

  function updateLikeUI(article, liked, count) {
    var btn = article.querySelector('[data-like-btn]');
    if (btn) {
      btn.classList.toggle('liked', liked);
      var svg = btn.querySelector('svg');
      if (svg) {
        svg.setAttribute('fill', liked ? '#ed4956' : 'none');
        svg.setAttribute('stroke', liked ? '#ed4956' : 'currentColor');
      }
    }
    var likesLine = article.querySelector('.post-likes');
    if (likesLine) {
      likesLine.textContent = count + ' suka';
      likesLine.hidden = count <= 0;
    }
    var counts = article.querySelectorAll('.post-actions .action-count');
    if (counts.length) counts[0].textContent = count;
  }

  function sendLike(form, done) {
    var data = new FormData(form);
    data.append('ajax', '1');
    fetch(form.getAttribute('action'), { method: 'POST', body: data, credentials: 'same-origin' })
      .then(function (r) { return r.json(); })
      .then(function (res) {
        if (!res || !res.ok) return;
        var article = form.closest('article');
        if (article) updateLikeUI(article, res.liked, res.count);
        if (done) done(res);
      })
      .catch(function () { form.submit(); }); // kalau fetch gagal, fallback ke cara lama (reload)
  }

  function showBigHeart(target) {
    var heart = document.createElement('span');
    heart.className = 'dbltap-heart';
    heart.innerHTML = HEART_SVG;
    target.appendChild(heart);
    setTimeout(function () { heart.remove(); }, 950);
  }

  // Tombol hati: like/unlike tanpa reload.
  document.querySelectorAll('form.like-form').forEach(function (form) {
    form.addEventListener('submit', function (e) {
      e.preventDefault();
      sendLike(form);
    });
  });

  // Double-tap pada foto / kotak teks postingan.
  // Dua jalur: pasangan 2 klik cepat (tap 2x di HP) + event dblclick native.
  // Guard lastDoubleTapAt mencegah aksi ganda kalau kedua jalur sama-sama jalan.
  var lastDoubleTapAt = 0;
  function handleDoubleTap(el) {
    var now = Date.now();
    if (now - lastDoubleTapAt < 600) return;
    lastDoubleTapAt = now;
    showBigHeart(el);
    var article = el.closest('article');
    var form = article ? article.querySelector('form.like-form') : null;
    if (!form) return; // belum login: tidak ada form like
    var btn = form.querySelector('[data-like-btn]');
    if (btn && btn.classList.contains('liked')) return; // sudah like: cukup animasi
    sendLike(form);
  }
  document.querySelectorAll('[data-like-target]').forEach(function (el) {
    var lastTap = 0;
    el.addEventListener('click', function () {
      var now = Date.now();
      if (now - lastTap < 350) {
        lastTap = 0;
        handleDoubleTap(el);
      } else {
        lastTap = now;
      }
    });
    el.addEventListener('dblclick', function () { handleDoubleTap(el); });
  });
});
