<div>
    @images('slide1', 'Slides', $slides, [300, 300, 'bfi_thumb' => true])
    <div>@text('text1', 'Heading')</div>
    <img src="@image('image1', 'Image 1')" alt="">
    <img src="@image('image1', 'Image 1', [300, 300, 'bfi_thumb' => true])" alt="">
    @editor('editor1', 'Editor 1')
    @link('link1, 'Link 1', $button')
    @textarea('area1', 'Area 1')
    @category('category1', 'Category 1', $category)
    @front
        {{ var_dump($slides) }}
    @endfront
</div>

slide1, Slides, $slides, [width => 1000, height => 450]