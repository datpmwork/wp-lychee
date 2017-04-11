# Required
    - Wordpress with KingComposer Plugin (Already include in project or download it here https://kingcomposer.com/
    - Composer Installed - https://getcomposer.org/download/
    
# How to use
    - Run 
        `composer install`
    - Add this line to in wp-load.php
        require __DIR__.'/vendor/autoload.php'; 

# Helpers
    - function asset($path): Return theme assets url
    - function attr($key, $type, $label): Register and retrieve value     
        $key: Value Key, require unique in each block template
        $type: Defined Type of attribute
            const KING_TEXT = 'text';
            const KING_IMAGE_URL = 'attach_image_url';
            const KING_EDITOR = 'editor';
            const KING_TEXTAREA = 'textarea';
            const KING_TEXTAREA_HTML = 'textarea_html';
            const KING_COLOR_PICKER = 'color_picker';
            const KING_GOOGLE_MAPS = 'google_map';
            const KING_LINK = 'link';
            const KING_CATEGORY = 'post_taxonomy';
        $label: Field Label     