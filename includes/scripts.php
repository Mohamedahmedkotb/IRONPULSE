<?php

declare(strict_types=1);

?>
<script>
(function () {
  var btn = document.getElementById("sidebar-toggle");
  var side = document.getElementById("app-sidebar");
  var bd = document.getElementById("sidebar-backdrop");
  if (!btn || !side || !bd) return;
  function close() {
    side.classList.remove("is-open");
    bd.classList.remove("is-visible");
    document.body.style.overflow = "";
  }
  btn.addEventListener("click", function () {
    side.classList.toggle("is-open");
    bd.classList.toggle("is-visible");
    document.body.style.overflow = side.classList.contains("is-open") ? "hidden" : "";
  });
  bd.addEventListener("click", close);
  side.querySelectorAll("a").forEach(function (a) { a.addEventListener("click", close); });
})();
</script>
<?php if (!empty($includeChartScript)): ?>
<script src="<?= ip_h(ip_url('assets/js/ip-charts.js')) ?>"></script>
<?php endif; ?>
</body>
</html>
