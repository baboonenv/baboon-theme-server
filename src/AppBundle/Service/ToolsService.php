<?php

namespace AppBundle\Service;

use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Class ToolsService
 * @package AppBundle\Service
 */
class ToolsService
{
    /**
     * @var KernelInterface
     */
    public $kernel;

    /**
     * ToolsService constructor.
     * @param KernelInterface $kernel
     */
    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * @param string $path
     *
     * @return bool
     */
    public function deleteDir(string $path) : bool
    {
        if (!is_dir($path)) {
            return true;
        }
        if (substr($path, strlen($path) - 1, 1) != '/') {
            $path .= '/';
        }
        $files = array_diff(scandir($path), array('.', '..'));
        foreach ($files as $file) {
            if (is_dir($path.$file)) {
                self::deleteDir($path.$file);
            } else {
                unlink($path.$file);
            }
        }
        rmdir($path);

        return true;
    }

    /**
     * @param string $path
     *
     * @return bool
     */
    public function deleteFile(string $path) : bool
    {
        if (!is_file($path)) {
            return true;
        }
        unlink($path);

        return true;
    }

    /**
     * @param string $path
     * @param array $excludeDirs
     * @param bool $removeTopParent
     * @param array $excludeFiles
     *
     * @return bool
     */
    public function cleanDir(string $path, array $excludeDirs = ['.git'], $removeTopParent = false, array $excludeFiles = ['.gitkeep']) : bool
    {
        if (is_dir($path)) {
            $objects = scandir($path);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (is_dir($path."/".$object)){
                        if(!in_array($object, $excludeDirs)){
                            $this->cleanDir($path."/".$object, [], true);
                        }
                    }else{
                        if(!in_array($object, $excludeFiles)){
                            unlink($path."/".$object);
                        }
                    }
                }
            }
            if($removeTopParent){
                rmdir($path);
            }
        }

        return true;
    }

    /**
     * @param string $path
     * @return bool
     */
    public function createDir(string $path) : bool
    {
        if(is_dir($path)){
            return true;
        }
        mkdir($path, 0777, true);

        return true;
    }

    /**
     * @param string $path
     * @return string
     */
    public function getContent(string $path)
    {
        return file_get_contents($path);
    }

    /**
     * @param string $path
     *
     * @return bool
     */
    public function haveBaboonConfiguration(string $path) : bool
    {
        $scanThemeDir = scandir($path);
        foreach ($scanThemeDir as $item){
            if($item == '.baboon.yml'){
                return true;
            }
        }

        return false;
    }

    /**
     * @param string $dir
     * @param array $results
     *
     * @return array
     */
    public function scanDirContentRecursive(string $dir, &$results = array())
    {
        $files = scandir($dir);
        foreach($files as $key => $value){
            $path = realpath($dir.DIRECTORY_SEPARATOR.$value);
            if(!is_dir($path)) {
                $results[] = $path;
            } else if($value != "." && $value != "..") {
                self::scanDirContentRecursive($path, $results);
                $results[] = $path;
            }
        }

        return $results;
    }

    /**
     * @param string $path1
     * @param string $path2
     * @param bool $removeFirstDir
     *
     * @return bool
     */
    public function moveFilesToDir(string $path1, string $path2, $removeFirstDir = true)
    {
        $path1 = realpath($path1);
        $path2 = realpath($path2);
        $path1ScanDir = $this->scanDirContentRecursive($path1);
        foreach ($path1ScanDir as $itemPath){
            if(is_file($itemPath)){
                $copyPath = str_replace($path1, $path2, $itemPath);
                $copyPathInfo = pathinfo($copyPath);
                $this->createDir($copyPathInfo['dirname']);
                copy($itemPath, $copyPath);
            }
        }
        if($removeFirstDir){
            $this->deleteDir($path1);
        }

        return true;
    }

    public function getSiteDir()
    {
        return $this->kernel->getCacheDir().'/_site/';
    }

    public function getRenderDir()
    {
        return $this->getSiteDir().'_render/';
    }

    public function getSourceDir()
    {
        return $this->getSiteDir().'_source/';
    }

    public function getThemesDir()
    {
        return $this->kernel->getCacheDir().'/_themes/';
    }
}
