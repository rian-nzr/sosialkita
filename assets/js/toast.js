// Toast: tombol tutup + hilang otomatis setelah 5 detik.
document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('.toast').forEach(function (toast) {
    var close = toast.querySelector('.toast-close');
    var hide = function () {
      toast.classList.add('toast-hide');
      setTimeout(function () { toast.remove(); }, 350);
    };
    if (close) close.addEventListener('click', hide);
    setTimeout(hide, 5000);
  });
});
