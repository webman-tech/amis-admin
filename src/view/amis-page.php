<?php
/** @var string|null $title */
/** @var array $assets */
/** @var array $amisJSON */

?>
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
<?php foreach ($assets['js'] as $js): ?>
    <script src="<?= $js ?>"></script>
<?php endforeach; ?>
<script type="text/javascript">
    (function () {
        let amis = amisRequire('amis/embed');

        let amisJSON = Object.assign({
            type: 'page',
        }, <?= json_encode($amisJSON) ?>);
        window.amisApp = amis.embed(
            '#root',
            amisJSON,
            {},
            {
                theme: '<?= $assets['theme'] ?? 'cxd' ?>',
            }
        );
    })();
</script>
</body>
</html>
