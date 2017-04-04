<?php
use Leafo\ScssPhp\Compiler;

/**
 * Created by csepmdat.
 * Date: 3/31/2017
 * Time: 11:22 AM
 */
class AssetCompiler extends \Illuminate\View\Compilers\Compiler implements \Illuminate\View\Compilers\CompilerInterface
{
    protected $global = [];

    public function compile($path = null)
    {
        # Đang compile block => compile scss, js
        # SCSS location
        $randomID = preg_replace('/[0-9]+/', '', sha1($path));
        $exist = $this->files->exists($path);
        $scssCompiler = new Compiler();

        $scssCompiler->addImportPath(pathinfo($path, PATHINFO_DIRNAME));
        $compiledCss = $scssCompiler->compile("
                            ".$this->getGlobal()."\n
                            .{$randomID} {
                                ".file_get_contents($path)."
                            }
                        ");
        $this->files->put($this->getCompiledPath($path), $compiledCss);
    }

    /**
     * @param mixed $global
     */
    public function setGlobal($global)
    {
        $this->global[] = $global;
    }

    /**
     * @return string
     */
    public function getGlobal()
    {
        $content = "";
        foreach ($this->global as $item) {
            $content .= file_get_contents($item);
        }
        return $content;
    }

    /**
     * Determine if the view at the given path is expired.
     *
     * @param  string  $path
     * @return bool
     */
    public function isExpired($path)
    {
        $compiled = $this->getCompiledPath($path);

        // If the compiled file doesn't exist we will indicate that the view is expired
        // so that it can be re-compiled. Else, we will verify the last modification
        // of the views is less than the modification times of the compiled views.
        if (! $this->files->exists($compiled)) {
            return true;
        }

        if ($this->files->lastModified($path) >= $this->files->lastModified($compiled))
            return true;

        # Check if all the scss is is not modified
        $dir = pathinfo($path, PATHINFO_DIRNAME);
        $files = glob($dir . "/*.scss");
        foreach ($files as $file) {
            if ($this->files->lastModified($file) >= $this->files->lastModified($compiled))
                return true;
        }

        return false;
    }
}