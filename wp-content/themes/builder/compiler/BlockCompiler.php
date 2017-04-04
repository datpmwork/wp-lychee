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

}