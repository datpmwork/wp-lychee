# Required
    - Wordpress with KingComposer Plugin (Already include in project or download it here https://kingcomposer.com/
    - Composer Installed - https://getcomposer.org/download/
    
# How to use
#### Install composer vendors     
    composer install
#### Add this line to in wp-load.php
    require __DIR__.'/vendor/autoload.php'; 

# Helpers
#### Theme assets url
    function asset($path): 
#### Register and retrieve value    
    function attr($key, $type, $label): Register and retrieve value     
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
# Blade Directives
#### Register and echo text field
    @text('text1', 'Heading') 
#### Register and echo image url
    @image('image1', 'Image 1') 
#### Register and echo html editor field    
    @editor('editor1', 'Editor 1')
#### Register and echo textarea field     
    @textarea('area1', 'Area 1')
#### Register and assign link to $button    
    @link('link1, 'Link 1', $button)
#### Register and assign category slug to $category
    @category('category1', 'Category 1', $category)             