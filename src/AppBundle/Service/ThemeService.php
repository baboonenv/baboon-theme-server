<?php

namespace AppBundle\Service;

use AppBundle\Params\AssetTypes;
use Symfony\Component\Yaml\Yaml;

/**
 * Class EnableThemeService
 * @package Baboon\PanelBundle\Service
 */
class ThemeService
{
    /**
     * @var ToolsService
     */
    private $tools;

    /**
     * @var ValidateConfigurationService
     */
    private $confValidator;

    /**
     * @var string
     */
    private $themeZipUri;

    /**
     * @var string
     */
    private $themeRealDir;

    /**
     * @var string
     */
    private $themeDir;

    /**
     * @var string
     */
    private $cloneThemePath;

    public $errors = [];

    /**
     * EnableThemeService constructor.
     *
     * @param ToolsService $toolsService
     * @param ValidateConfigurationService $confValidator
     */
    public function __construct(ToolsService $toolsService, ValidateConfigurationService $confValidator)
    {
        $this->tools            = $toolsService;
        $this->confValidator    = $confValidator;
    }

    /**
     * @param string $zipUri
     * @return bool|mixed
     */
    public function downloadAndValidate(string $zipUri)
    {
        $this->setupVars($zipUri);
        $zipFileContent = $this->tools->getContent($this->themeZipUri);
        $this->tools->createDir($this->themeDir);

        file_put_contents($this->cloneThemePath, $zipFileContent);
        $zip = new \ZipArchive();
        $res = $zip->open($this->cloneThemePath);
        if ($res === TRUE) {
            $zip->extractTo($this->themeDir);
            $zip->close();
            unlink($this->cloneThemePath);
        }
        $this->normalizeThemeDir();

        $this->confValidator->setConfigFile($this->themeDir.'.baboon.yml');
        $this->errors = $this->confValidator->validate();

        if(count($this->errors)>0){
            return false;
        }

        $baboonData = Yaml::parse(file_get_contents($this->themeDir.'.baboon.yml'));

        return $baboonData;
    }

    /**
     * @param string $zipUri
     */
    private function setupVars(string $zipUri)
    {
        $this->themeZipUri = $zipUri;
        $this->themeDir = $this->tools->getThemesDir().rand(0, 999).'/';
        $this->themeRealDir = $this->themeDir;
        $this->cloneThemePath = $this->themeDir.'theme_clone.zip';
    }

    private function setupSiteDirs()
    {
        $this->tools->deleteFile($this->tools->getSiteDir().'data.json');
        $this->tools->cleanDir($this->tools->getSourceDir());
        $this->tools->cleanDir($this->tools->getRenderDir());
    }

    /**
     * @return bool
     */
    private function generateDataFile()
    {
        $baboonData = Yaml::parse(file_get_contents($this->tools->getSourceDir().'.baboon.yml'));
        $baboonData['assets'] = $this->normalizeConfigurationAssets($baboonData['assets']);

        file_put_contents($this->tools->getSiteDir().'data.json', json_encode($baboonData));

        return true;
    }

    private function normalizeConfigurationAssets($assets, $path = '[assets]')
    {
        foreach ($assets as $assetKey => $asset){
            $asset['path'] = $path.'['.$assetKey.']';
            if($asset['type'] == AssetTypes::TREE){

                $randString = $this->generateRandomString(5);
                $itemPath = $asset['path'].'[assets]['.$randString.']';
                $asset['multiple'] = true;
                $fieldAssets = $asset['assets'];
                $asset['original_assets'] = $fieldAssets;
                $asset['assets'] = null;
                $asset['assets'][$randString] = $this->normalizeConfigurationAssets($fieldAssets, $itemPath);
            }else{
                $asset['value'] = $asset['default'];
                $asset['isDefaultValue'] = true;
            }
            $assets[$assetKey] = $asset;
        }

        return $assets;
    }

    /**
     * @return bool
     */
    private function normalizeThemeDir()
    {
        if($this->tools->haveBaboonConfiguration($this->themeDir)){
            return true;
        }
        $scanThemeDir = scandir($this->themeDir);
        foreach ($scanThemeDir as $item){
            if(is_dir($this->themeDir.$item) && $item != '.' && $item != '..'){
                $this->themeDir .=  $item;
                if($this->tools->haveBaboonConfiguration($this->themeDir)){
                    $this->tools->moveFilesToDir($this->themeDir, $this->themeRealDir);
                    $this->themeDir = $this->themeRealDir;

                    return true;
                }else{
                    throw new \LogicException('Theme must to specify .baboon.yml configuration file!');
                }
            }
        }

        return false;
    }

    public function generateRandomString($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}
