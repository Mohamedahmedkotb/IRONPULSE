<?php

declare(strict_types=1);

/** @var list<string> $authJsModules e.g. ['login'] or ['signup'] */
$authJsModules = $authJsModules ?? ['login'];
?>
<div id="app-footer"></div>
<script type="module" src="<?= ip_h(ip_url('assets/js/layout.js')) ?>"></script>
<?php foreach ($authJsModules as $mod): ?>
<script type="module" src="<?= ip_h(ip_url('assets/js/pages/' . $mod . '.js')) ?>"></script>
<?php endforeach; ?>
</body>
</html>
