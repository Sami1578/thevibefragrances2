    </div>
  </div>
</div>
<script>
  document.getElementById('sideBtn').addEventListener('click', function () {
    document.getElementById('side').classList.toggle('open');
  });
  document.querySelectorAll('[data-confirm]').forEach(function (f) {
    f.addEventListener('submit', function (e) {
      if (!confirm(f.getAttribute('data-confirm'))) e.preventDefault();
    });
  });
  document.querySelectorAll('[data-preview]').forEach(function (inp) {
    inp.addEventListener('change', function () {
      var box = document.querySelector(inp.getAttribute('data-preview'));
      if (!box) return;
      box.innerHTML = '';
      Array.prototype.forEach.call(inp.files, function (f) {
        var img = document.createElement('img');
        img.src = URL.createObjectURL(f);
        box.appendChild(img);
      });
    });
  });
</script>
</body>
</html>
