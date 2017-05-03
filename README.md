# Required
    - Wordpress with KingComposer Plugin (Already include in project or download it here https://kingcomposer.com/
    - Composer Installed - https://getcomposer.org/download/
    
# How to use
#### Install composer vendors     
    composer install
# Helpers
#### Theme assets url
    function asset($path): 
# Blade Directives
#### Register and echo text field
    @text('text1', 'Heading') 
#### Register and echo image url
    @image('image1', 'Image 1', [300, 300, 'bfi_thumb' => true])
    Third parameter is optional.
#### Register and assign array of images url to $slides
    $slides = @images('slide1', 'Slides', [300, 300, 'bfi_thumb' => true])
    Third parameter is optional.
#### Register and echo html editor field    
    @editor('editor1', 'Editor 1')
#### Register and echo textarea field     
    @textarea('area1', 'Area 1')
#### Register and assign link to $button    
    $button = @link('link1, 'Link 1')
    $button has 3 children property.
        - $button->caption: Anchor Caption
        - $button->href: Anchor Link
        - $button->target: Anchor target
    Usage Example:
        <a href="{{ $button->href }}" target="{{ $button->target }}">{{ $button->caption }}</a>
#### Register and assign category slug to $category
    $category = @category('category1', 'Category 1')
                 
# Wordpress Directives

#### query_posts
    @wpquery($params)
    
    @endwpquery
    
    **Example:**
     
    ~~~~@wpquery(["category_name" => "News"])
    
    @endwpquery~~~~
    
    Will be translated to
        
    ~~~~query_posts(["category_name" => "News"])
    
    wp_reset_query();~~~~
#### Loop  
    
    @loop: while (have_posts()): the_post()
        
    @endloop: endwhile;
    
    @haspost: if (have_posts()): the_post()
    
    @endhaspost: endif;
    
    
    
    
                                                     