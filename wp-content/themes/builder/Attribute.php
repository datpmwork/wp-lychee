<?php

/**
 * Created by csepmdat.
 * Date: 3/20/2017
 * Time: 4:35 PM
 */
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Blade;
use Illuminate\View\Compilers\BladeCompiler;
use Illuminate\View\Engines\CompilerEngine;
use Illuminate\View\Engines\EngineResolver;
use Illuminate\View\Engines\PhpEngine;
use Illuminate\View\Factory;
use Illuminate\View\FileViewFinder;

require 'Str.php';
require 'compiler/BlockCompiler.php';
require 'compiler/AssetCompiler.php';
class Attribute
{

    const KING_TEXT = 'text';
    const KING_IMAGES = 'attach_images';
    const KING_IMAGE = 'attach_image';
    const KING_EDITOR = 'editor';
    const KING_TEXTAREA = 'textarea';
    const KING_TEXTAREA_HTML = 'textarea_html';
    const KING_LINK = 'link';
    const KING_CATEGORY = 'post_taxonomy';

    protected $frontMode = 0;

    protected $section_queue = [];

    protected $pointer = null;

    protected $filesViewFinder;

    protected $files;

    protected $cache = __DIR__ . '/cache/';

    public $factory;

    public $compiler;

    public function __construct()
    {
        $this->files = new Filesystem();

        $this->registerViewFinder();

        $resolver = new EngineResolver;

        // Next we will register the various engines with the resolver so that the
        // environment can resolve the engines it needs for various views based
        // on the extension of view files. We call a method for each engines.
        foreach (['php', 'blade', 'scss'] as $engine) {
            $this->{'register'.ucfirst($engine).'Engine'}($resolver);
        }

        $this->factory = new Factory($resolver, $this->filesViewFinder, new \Illuminate\Events\Dispatcher());
        $this->factory->addExtension('scss', 'scss');

        $this->registerWordpressDirective();
    }

    /**
     * Register the PHP engine implementation.
     *
     * @param  \Illuminate\View\Engines\EngineResolver  $resolver
     * @return void
     */
    public function registerPhpEngine($resolver)
    {
        $resolver->register('php', function () {
            return new PhpEngine();
        });
    }

    /**
     * Register the view finder implementation.
     *
     * @return void
     */
    public function registerViewFinder()
    {
        $paths = [__DIR__];

        $this->filesViewFinder =  new FileViewFinder($this->files, $paths, ['blade.php', 'php', 'scss']);
    }

    public function registerBladeEngine(EngineResolver $resolver)
    {
        // The Compiler engine requires an instance of the CompilerInterface, which in
        // this case will be the Blade compiler, so we'll first create the compiler
        // instance to pass into the engine so it can compile the views properly.
        $this->compiler = new BlockCompiler($this->files, $this->cache);
        // Create Cache Folder If there is not
        if (!$this->files->exists($this->cache)) {
            $this->files->makeDirectory($this->cache);
        }
        $resolver->register('blade', function () {
            return new CompilerEngine($this->compiler);
        });
    }

    public function registerScssEngine(EngineResolver $resolver)
    {
        // The Compiler engine requires an instance of the CompilerInterface, which in
        // this case will be the Blade compiler, so we'll first create the compiler
        // instance to pass into the engine so it can compile the views properly.
        $resolver->register('scss', function () {
            return new CompilerEngine(new AssetCompiler($this->files, $this->cache));
        });
    }

    # Attribute Core Functions
    # Start running front mode, which change get function to get attribute instead of register
    public function start_front() {
        # Change Flag
        $this->frontMode = 1;

        # Register Short Code
        $this->register_short_code();

        # Register King
        $this->register_king_composer();
    }

    # Start a new section
    public function start_section($section_path) {
        # Section name
        $section_name = $this->getShortCodeName($section_path);
        # Start Output Buffering
        ob_start();
        # Enqueue Section
        $this->section_queue[$section_name] = [];
        $this->pointer = &$this->section_queue[$section_name];

        $this->pointer["_templateLocation"] = $section_path;
        # Include Section
        $this->compiler->scanBlock($section_path);

        # Clean up Buffer
        ob_get_clean();
    }

    # Set value to a variable
    public function set($section, $key, $value) {
        # Set value for key in section
        $this->section_queue[$section][$key]["value"] = $value;
        # Find Type of $key
        $type = array_filter($this->section_queue[$section], function($item) use ($key) {
            return isset($item["key"]) && $item["key"] == $key;
        });
        $type = array_shift($type);
        if ($type == null) $type = Attribute::KING_TEXT;
        else $type = $type["type"];
        # Find Options of $key
        $options = array_filter($this->section_queue[$section], function($item) use ($key) {
            return isset($item["key"]) && $item["key"] == $key;
        });
        $options = array_shift($options);
        if ($options == null) $options = [];
        else $options = $options["options"];

        $this->section_queue[$section][$key]["type"] = $type;
        $this->section_queue[$section][$key]["options"] = $options;
    }

    # Retrieve variable in section
    public function get($key, $type = self::KING_TEXT, $label = '', $options = []) {
        if (is_null($this->pointer)) {
            throw new Exception("Pointer is pointing to null section");
        }
        # Register Mode
        if (!$this->frontMode) {
            # Push attribute to collection
            $duplicate = array_filter($this->pointer, function ($item) use ($key) {
                return isset($item['key']) && $item['key'] == $key;
            });
            if (empty($duplicate)) {
                $this->pointer[] = ["type" => $type, "key" => $key, "label" => $label, "options" => $options];
            }
            if ($type == Attribute::KING_LINK) {
                return (object)["caption" => "", "href" => "", "target" => ""];
            }
            if ($type == Attribute::KING_CATEGORY) {
                return 'uncategorized';
            }
            return true;
        }
        # Front Mode
        else {
            # Return Value Of $key at current section
            if ($this->pointer[$key]["type"] == Attribute::KING_CATEGORY) {
                $slug = str_replace("post:", "", $this->pointer[$key]["value"]);
                return $slug;
            }
            if ($this->pointer[$key]["type"] == Attribute::KING_LINK) {
                $data = explode("|", $this->pointer[$key]["value"]);
                $button = ["href" => $data[0], "caption" => isset($data[1]) ? $data[1] : '', "target" => isset($data[2]) ? $data[2] : ''];
                return (object)$button;
            }
            if ($this->pointer[$key]["type"] == Attribute::KING_IMAGE) {
                $options = $this->pointer[$key]["options"];
                $attachment = $this->pointer[$key]["value"];
                echo wp_get_attachment_image_url($attachment, $options);
                return;
            }
            if ($this->pointer[$key]["type"] == Attribute::KING_IMAGES) {
                $options = $this->pointer[$key]["options"];
                $attachments = explode(",", $this->pointer[$key]["value"]);
                $images = [];
                foreach ($attachments as $attachment) {
                    $images[] = wp_get_attachment_image_url($attachment, $options);
                }
                return $images;
            }
            echo $this->pointer[$key]["value"];
        }
    }

    # Start Registering Short Code with Wordpress
    public function register_short_code() {
        # Loop through sections and register short code
        foreach ($this->section_queue as $short_code_name => $params) {
            $short_code_callback = function($attributes, $content, $short_code) {
                global $attribute;

                foreach ($attributes as $key => $value) {
                    $attribute->set($short_code, $key, $value);
                }
                $attribute->pointer = &$attribute->section_queue[$short_code];
                $this->factory->incrementRender();
                echo $this->factory->make('blocks.' . $short_code . '.index', array_except(get_defined_vars(), array('__data', '__path')))->render();
                $this->factory->decrementRender();
            };

            add_shortcode($short_code_name, $short_code_callback);
        }
    }

    # Start Registering Short Code with King Composer
    public function register_king_composer(){
        //TODO: Register With King Composer use $this->section_queue
        if (function_exists('kc_add_map'))
        {
            foreach ($this->section_queue as $short_code_name => $params) {
                $params = array_filter($params, function($key) {
                    return $key !== '_templateLocation';
                }, ARRAY_FILTER_USE_KEY);
                $params = array_map(function($param) {
                    return [
                        "name" => $param["key"],
                        "type" => $param["type"],
                        "label" => (!isset($param["label"]) || empty($param["label"])) ? $param["key"] : $param["label"],
                        "admin_label" => $param["type"] == Attribute::KING_TEXT
                    ];
                }, $params);

                kc_add_map(
                    array(
                        $short_code_name => array(
                            'name' => $short_code_name,
                            'icon' => 'sl-plus',
                            'category' => 'Content',
                            'container' => true,
                            'params' => $params
                        ),  // End of elemnt kc_icon
                    )
                ); // End add map
            }
        } // End if
    }

    # Normalize Block Name to Short Code Name
    public function getShortCodeName($path) {
        $path = str_replace("/", DIRECTORY_SEPARATOR, $path);
        $path = str_replace("\\", DIRECTORY_SEPARATOR, $path);
        $path = explode(DIRECTORY_SEPARATOR, $path);
        array_walk($path, function(&$item) {
            $item = str_replace("blade.php", "", $item);
            $item = str_replace(".php", "", $item);
        });
        $path = array_filter($path, function($item) {
            return !str_contains($item, "index");
        });
        return $path[count($path) - 1];
    }

    /**
     * @return int
     */
    public function isFrontMode()
    {
        return $this->frontMode;
    }

    private function registerWordpressDirective()
    {
        $compiler = $this->factory->getEngineResolver()->resolve('blade')->getCompiler();
        $compiler->directive('wpquery', function($params) {
            return "<?php query_posts({$params}); ?>";
        });

        $compiler->directive('loop', function() {
            return "<?php while (have_posts()): the_post() ?>";
        });

        $compiler->directive('endloop', function() {
            return "<?php endwhile; ?>";
        });

        $compiler->directive('haspost', function() {
            return "<?php if (have_posts()): the_post() ?>";
        });

        $compiler->directive('endhaspost', function() {
            return "<?php endif; ?>";
        });

        $compiler->directive('endwpquery', function() {
            return "<?php wp_reset_query(); ?>";
        });

        $compiler->directive('front', function() {
            return "<?php global \$attribute; if (\$attribute->isFrontMode()): ?>";
        });

        $compiler->directive('endfront', function() {
            return "<?php endif; ?>";
        });

        # Attribute
        $compiler->directive('text', function($expression) {
            list($key, $label) = explode(',',str_replace(['(',')',"'"], '', $expression));
            global $attribute;
            if ($attribute->isFrontMode()) {
                return "<?php attr('{$key}', Attribute::KING_TEXT, '{$label}') ?>";
            } else {
                $attribute->get($key, Attribute::KING_TEXT, $label);
            }
        });
        $compiler->directive('image', function($expression) {
            list($args, $options) = explode("[", $expression);

            list($key, $label) = explode(',',str_replace(['(',')',"'"], '', $args));

            if (!is_null($options)) {
                eval("\$options = [" . $options. ";");
            } else {
                $options = [];
            }

            global $attribute;
            if ($attribute->isFrontMode()) {
                return "<?php attr('{$key}', Attribute::KING_IMAGE, '{$label}') ?>";
            } else {
                $attribute->get($key, Attribute::KING_IMAGE, $label, $options);
            }
        });
        $compiler->directive('images', function($expression) {
            list($args, $options) = explode("[", $expression);

            list($key, $label, $output) = explode(',',str_replace(['(',')',"'"], '', $args));

            if (!is_null($options)) {
                eval("\$options = [" . $options. ";");
            } else {
                $options = [];
            }

            global $attribute;
            if ($attribute->isFrontMode()) {
                return "<?php {$output} = attr('{$key}', Attribute::KING_IMAGES, '{$label}'); ?>";
            } else {
                $attribute->get($key, Attribute::KING_IMAGES, $label, $options);
            }
        });
        $compiler->directive('editor', function($expression) {
            list($key, $label) = explode(',',str_replace(['(',')',"'"], '', $expression));
            global $attribute;
            if ($attribute->isFrontMode()) {
                return "<?php attr('{$key}', Attribute::KING_EDITOR, '{$label}') ?>";
            } else {
                $attribute->get($key, Attribute::KING_EDITOR, $label);
            }
        });
        $compiler->directive('link', function($expression) {
            list($key, $label, $output) = explode(',',str_replace(['(',')',"'"], '', $expression));
            global $attribute;
            if ($attribute->isFrontMode()) {
                return "<?php {$output} = attr('{$key}', Attribute::KING_LINK, '{$label}'); ?>";
            } else {
                $attribute->get($key, Attribute::KING_LINK, $label);
            }
        });
        $compiler->directive('textarea', function($expression) {
            list($key, $label) = explode(',',str_replace(['(',')',"'"], '', $expression));
            global $attribute;
            if ($attribute->isFrontMode()) {
                return "<?php attr('{$key}', Attribute::KING_TEXTAREA, '{$label}') ?>";
            } else {
                $attribute->get($key, Attribute::KING_TEXTAREA, $label);
            }
        });
        $compiler->directive('category', function($expression) {
            list($key, $label, $output) = explode(',',str_replace(['(',')',"'"], '', $expression));
            global $attribute;
            if ($attribute->isFrontMode()) {
                return "<?php {$output} = attr('{$key}', Attribute::KING_CATEGORY, '{$label}') ?>";
            } else {
                $attribute->get($key, Attribute::KING_CATEGORY, $label);
            }
        });
    }
}