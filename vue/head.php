<?php
function echoHead($title, $pathToRoot) {
    ?>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo $title ?></title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.1/font/bootstrap-icons.css" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
                rel="stylesheet"
                integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
                crossorigin="anonymous">

        <link rel="shortcut icon" href="<?php echo $pathToRoot ?>/img/ES.gif" type="image/x-icon">
        <link rel="stylesheet" href="<?php echo $pathToRoot ?>/style/generalStyle.css">
    </head>
    <?php
}
