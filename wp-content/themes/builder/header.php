<?php global $q_config; ?>
<?php if(is_404()) $url = get_option('home'); else $url = ''; ?>

<?php
/**
 * The header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="content">
 *
 * @package start
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <link rel="shortcut icon" href="/wp-content/themes/builder/images/favicon.svg" type="image/x-icon"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="http://gmpg.org/xfn/11">
    <link rel="pingback" href="<?php bloginfo('pingback_url'); ?>">
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
    <?php wp_head(); ?>
    <!-- Style slick -->
    <link rel="stylesheet" type="text/css" href="/wp-content/themes/builder/assets/plugin/slick/slick/slick.css">
    <link rel="stylesheet" type="text/css" href="/wp-content/themes/builder/assets/plugin/slick/slick/slick-theme.css">

    <!-- Style awesome -->
    <link rel="stylesheet" type="text/css"
          href="/wp-content/themes/builder/assets/font/awesome/css/font-awesome.min.css"/>

    <!-- Style flexboxgrid -->
    <!-- style popup -->
    <link rel="stylesheet" href="/wp-content/themes/builder/assets/css/swipebox.css">

    <link rel="stylesheet" href="/wp-content/themes/builder/assets/css/style.css">
    <link rel="stylesheet" href="/wp-content/themes/builder/assets/css/responsive.css">
    <?php wp_head() ?>
</head>

<body <?php body_class(); ?>>
<div class="m-site">
    <div class="header-mobile">
        <div class="s-logomb">
            <a href="<?php echo esc_url(home_url('/')); ?>"><img src="/wp-content/themes/builder/images/logomb.svg"
                                                                 alt=""></a>
        </div>
        <div class="nav-mobile slicknav_btn">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </div><!-- end header-mobile -->
    <div class="menumobile">
        <ul class="menu-mobile">
            <?php
            $menu_items = wp_get_nav_menu_items('Main Menu', array('order' => 'DESC'));
            $current_mobile_id = 0;
            foreach ((array)$menu_items as $key => $menu_item) {
                if ($menu_item->menu_item_parent == 0) {
                    if ($current_mobile_id == 0) {
                        echo '<li><a href="' . $menu_item->url . '">' . $menu_item->title . '</a>';
                        echo '<ul>';

                    } else {
                        echo '</ul>';
                        echo '</li>';
                        echo '<li><a href="' . $menu_item->url . '">' . $menu_item->title . '</a>';
                        echo '<ul>';
                    }

                    $current_mobile_id = $menu_item->ID;
                } else {
                    echo '<li><a href="' . $menu_item->url . '">' . $menu_item->title . '</a></li>';
                }
            }
            echo '</ul>';
            echo '</li>';
            ?>

        </ul>
        <div class="s-headerbottom">

            <div class="s-language">
                <?php foreach(qtranxf_getSortedLanguages() as $language) { ?>
                    <a class="<?= $q_config['language'] == $language ? 'active' : '' ?>" href="<?= qtranxf_convertURL($url, $language, false, true) ?>"><?= $language ?></a>
                <?php } ?>
            </div>
            <a href="http://www.sakai-chem.co.jp/jp/" class="s-link">
                <img src="/wp-content/themes/builder/images/text.jpg" alt="">
            </a>
        </div>

    </div>
    <div class="header-fix">
        <div class="s-header header">
            <div class="header-inner">
                <!--                <div class="s-languages">--><?php //echo qtrans_SelectCode('code');?><!--</div>-->
                <a href="<?php echo esc_url(home_url('/')); ?>" class="s-logo">

                </a>
                <div class="s-menumain">
                    <ul class="global-navigation">
                        <?php
                        $menu_items = wp_get_nav_menu_items('Main Menu', array('order' => 'DESC'));
                        $current_id = 0;
                        foreach ((array)$menu_items as $key => $menu_item) {
                            if ($menu_item->menu_item_parent == 0) {
                                if ($current_id == 0) {
                                    echo '<li id="gn-about" class="primary"><a href="' . $menu_item->url . '" class="primary-bt"><span>' . $menu_item->title;
                                    echo '</span><span><img src="/wp-content/themes/builder/images/arrows.svg" alt=""></span></a>';
                                    echo '<ul>';
                                } else {
                                    echo '</ul>';
                                    echo '</li>';
                                    echo '<li id="gn-about" class="primary"><a href="' . $menu_item->url . '" class="primary-bt"><span>' . $menu_item->title;
                                    echo '</span><span><img src="/wp-content/themes/builder/images/arrows.svg" alt=""></span></a>';
                                    echo '<ul>';
                                }
                                $current_id = $menu_item->ID;
                            } else {
                                echo '<li><a href="' . $menu_item->url . '">' . $menu_item->title . '</a></li>';
                            }
                        }
                        echo '</ul>';
                        echo '</li>';
                        ?>
                    </ul>
                    <div class="s-headerbottom">
                        <div class="s-phone">
                            <a href="#"><span>tel</span>+84 (0) 650 357 7452</a>
                            <a href="#"><span>fax</span>+84 (0) 650 357 7454</a>
                            <div class="s-text">
                                <small>月ー金　9:00 - 17:00</small>
                            </div>
                        </div>
                        <div class="s-language">
                            <?php foreach(qtranxf_getSortedLanguages() as $language) { ?>
                                <a class="<?= $q_config['language'] == $language ? 'active' : '' ?>" href="<?= qtranxf_convertURL($url, $language, false, true) ?>"><?= $language ?></a>
                            <?php } ?>
                        </div>
                        <a href="http://www.sakai-chem.co.jp/jp/" class="s-link">
                            <img src="/wp-content/themes/builder/images/text.jpg" alt="">
                        </a>
                    </div>
                </div><!-- end s-menumain -->

            </div>
        </div><!-- end s-header -->
    </div><!-- end header-fix -->
    <div class="s-body">

