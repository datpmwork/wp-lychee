<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <link rel="shortcut icon" href="/wp-content/themes/builder/images/favicon.svg" type="image/x-icon"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head() ?>

    <style>
        @stack('block-styles')
    </style>
</head>

<body>

@yield('content')

<?php wp_footer(); ?>

</body>
</html>
