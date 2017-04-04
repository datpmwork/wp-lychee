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

<body <?php body_class(); ?>>
<div class="m-site">
    <div class="s-body">

        @yield('content')

    </div>
    <footer class="col-md-12">
        <div class="fttop">
            <h3 class="s-title">What is expected of us now.</h3>
            <a href="/contact" class="s-button s-animal"><span>contact us</span></a>
        </div>
        <ul class="copyright">
            <li><a href="/policy">PRIVACY POLICY</a> <i>|</i></li>
            <li>© SAKAI CHEMICAL VIETNAM CO.,LTD.</li>
        </ul>
    </footer>
</div>
<?php wp_footer(); ?>

</body>
</html>
