<?php

/**
 * Created by csepmdat.
 * Date: 4/3/2017
 * Time: 4:26 PM
 */
class ViewHandler
{
    protected $bladedTemplate = false;

    /**
     * @param $template
     */
    public function template_include_blade($template) {

        if( $this->bladedTemplate )
            return $this->bladedTemplate;

        if( ! $template )
            return $template; // Noting to do here. Come back later.

        # In case template was failed
        if (empty($template)) {
            $template = __DIR__ . "/templates/default.blade.php";
            $this->bladedTemplate = $template;
        }

        global $attribute;
        $resolver = $attribute->factory->getEngineResolver()->resolve('blade');
        $compiler = $resolver->getCompiler();
        if ($compiler->isExpired($template)) {
            $compiler->compile($template);
        }

        $this->bladedTemplate = $compiler->getCompiledPath($template);
        return $this->bladedTemplate;
    }

    /**
     * Return a call of templateinclude blade passing template path.
     * @param { str }
     * @return { str } path of the compiled view
     */
    function get_query_template( $template ) {
        return $this->template_include_blade( $template );
    }

    public function single_template() {
        global $post;
        $terms = get_the_terms( $post->ID, 'category' );
        if ( empty( $terms ) ) $terms = array();
        $term_list = wp_list_pluck( $terms, 'slug' );

        if (in_array("mau-nha", $term_list)) {
            return $this->template_include_blade(__DIR__ . "/templates/house.blade.php");
        }

        return $this->template_include_blade(__DIR__ . "/templates/post.blade.php");
    }
}