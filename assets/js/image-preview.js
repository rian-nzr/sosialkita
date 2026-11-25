// Pratinjau foto sebelum diunggah (composer + edit postingan).
// Cara pakai: input file diberi atribut data-preview="id-wrap",
// di dalam wrap ada <img> dan tombol [data-preview-remove].
document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('input[type="file"][data-preview]').forEach(function (input) {
    var wrap = document.getElementById(input.getAttribute('data-preview'));
    if (!wrap) return;
    var img = wrap.querySelector('img');
    var removeBtn = wrap.querySelector('[data-preview-remove]');
    var labelText = input.closest('label') ? input.closest('label').querySelector('.file-label-text') : null;

    input.addEventListener('change', function () {
      var file = input.files && input.files[0];
      if (!file) { wrap.hidden = true; return; }
      if (!file.type || file.type.indexOf('image/') !== 0) {
        input.value = '';
        wrap.hidden = true;
        return;
      }
      var reader = new FileReader();
      reader.onload = function (e) {
        img.src = e.target.result;
        wrap.hidden = false;
      };
      reader.readAsDataURL(file);
      if (labelText) labelText.textContent = '📷 Foto dipilih: ' + file.name;
    });

    if (removeBtn) {
      removeBtn.addEventListener('click', function () {
        input.value = '';
        wrap.hidden = true;
        if (labelText) labelText.textContent = '📷 Tambah foto';
      });
    }
  });
});
