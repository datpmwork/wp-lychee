<?php
/**
 * Created by PhpStorm.
 * User: csepm_000
 * Date: 17-11-2016
 * Time: 4:43 PM
 */

use Leafo\ScssPhp\Compiler;
use Illuminate\View\Compilers\BladeCompiler;
use Symfony\Component\DomCrawler\Crawler;

class BlockCompiler extends BladeCompiler
{
    public function compile($path = null)
    {
        if ($path) {
            $this->setPath($path);
        }

        # Nếu đây là block, cần compile scss, js và append vào page
        $assets = $this->getUniqueID($path);

        if (! is_null($this->cachePath)) {
            $contents = $this->compileString($this->files->get($this->getPath()));

            # Chỉ gắn ID cho block template
            $blockPath = component_path('');
            if (starts_with($path, $blockPath)) {
                # Append Unique CSS ID To the first node if this is a block template
                # Assign Unique ID To Element
                $dom = new Crawler();
                $dom->addHtmlContent($contents);
                foreach ($dom->filter('body')->children() as $child) {
                    $class = $child->getAttribute('class');
                    $class .= " {$assets['hash']}";
                    $child->setAttribute('class', $class);
                    $blockName = trim(strrchr(pathinfo($this->getPath(), PATHINFO_DIRNAME), "/"), "/");
                    if (empty($child->getAttribute('id'))) {
                        $child->setAttribute('id', str_slug($blockName));
                    }
                }
                $html = $dom->filter('body')->html();
                $contents = htmlspecialchars_decode(rawurldecode($html));

                # Append Style to Block
                $appendCssCode = "<?php global \$attribute;
                                    \$attribute->factory->startPush('block-styles'); ?>
                                    <?php if (\$__env->exists('".$assets['asset']."')) { ?>
                                        <?php echo \$__env->make('".$assets['asset']."', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                                    <?php } ?>
                                 <?php \$attribute->factory->stopPush();";
                $contents = $contents . $appendCssCode;
            }

            # Minified HTML
            //TODO: Toggle Minify
            $contents = preg_replace(array('/<!--(.*)-->/Uis',"/[[:blank:]]+/"),array('',' '),str_replace(array("\n","\r","\t"),'',$contents));

            $this->files->put($this->getCompiledPath($this->getPath()), $contents);
        }
    }

    public function getUniqueID($path) {
        $location = pathinfo($path, PATHINFO_DIRNAME) . "/style.scss";
        $view = normalizeTemplatePath($location);
        return [
            'asset' => str_replace(".scss", "", $view),
            'hash' => preg_replace('/[0-9]+/', '', sha1($location))
        ];
    }

    public function scanBlock($path) {
        $value = $this->files->get($path);
        $result = "";
        // Here we will loop through all of the tokens returned by the Zend lexer and
        // parse each one into the corresponding valid PHP. We will then have this
        // template as the correctly rendered PHP that can be rendered natively.
        foreach (token_get_all($value) as $token) {
            $result .= is_array($token) ? $this->parseScan($token) : $token;
        }
    }

    /**
     * Parse the tokens from the template.
     *
     * @param  array  $token
     * @return string
     */
    protected function parseScan($token)
    {
        list($id, $content) = $token;

        if ($id == T_INLINE_HTML) {
            $content = $this->compileStatements($content);
        }

        return $content;
    }

}