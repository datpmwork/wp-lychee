<div>
    @images('slide1', 'Slides', $slides, [300, 300, 'bfi_thumb' => true])
    <div>@text('text1', 'Heading')</div>
    <img src="@image('image1', 'Image 1', [300, 300, 'bfi_thumb' => true])" alt="">
    @editor('editor1', 'Editor 1')
    @link('link1, 'Link 1', $button')
    @textarea('area1', 'Area 1')
    @category('category1', 'Category 1', $category)

    @wpquery(["category_name" => $category])
    <h1>Posts in {{ $category }} category</h1>
    <ul>
    @loop
        <li>{{ the_title() }}</li>
    @endloop
    </ul>
    @endwpquery
</div>