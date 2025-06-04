<?php
/** @var string|null $title */
/** @var array $assets */
/** @var array $amisJSON */
/** @var string|null $script */

$debug = $assets['debug'] ?? false;
?>
<!DOCTYPE html>
<html lang="<?= $assets['lang'] ?? 'zh' ?>">
<head>
    <meta charset="UTF-8"/>
    <title><?= $title ?? 'App Admin' ?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1"/>
    <meta http-equiv="X-UA-Compatible" content="IE=Edge"/>
    <?php foreach ($assets['css'] as $css): ?>
        <link rel="stylesheet" href="<?= $css ?>"/>
    <?php endforeach; ?>
    <style>
        html,
        body,
        .app-wrapper {
            position: relative;
            width: 100%;
            height: 100%;
            margin: 0;
            padding: 0;
        }
    </style>
</head>
<body>
<div id="root" class="app-wrapper"></div>
<?php foreach ($assets['js'] as $item): ?>
<?php if($item['type'] === 'js'): ?>
    <script src="<?= $item['content'] ?>"></script>
<?php elseif ($item['type'] === 'script'): ?>
    <script><?= $item['content'] ?></script>
<?php endif; ?>
<?php endforeach; ?>
<script type="text/javascript">
  (function () {
    <?= $script ?? '' ?>

    const amis = amisRequire('amis/embed');

    window.amisAppBeforeLoad && window.amisAppBeforeLoad(amis);

    const amisJSON = <?= json_encode($amisJSON, $debug ? JSON_PRETTY_PRINT : JSON_ERROR_NONE) ?>;
    window.amisApp = amis.embed(
      '#root',
      amisJSON,
      Object.assign({
        locale: '<?= $assets['locale'] ?? 'zh-CN' ?>',
      }, window.amisAppProps || {}),
      Object.assign({
        enableAMISDebug: <?= $debug ? 'true' : 'false' ?>,
        theme: '<?= $assets['theme'] ?? 'default' ?>',
      }, window.amisAppEnv || {})
    );

    window.amisAppLoaded && window.amisAppLoaded(window.amisApp);
  })();
</script>
</body>
</html>
