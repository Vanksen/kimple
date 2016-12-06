<?php

/*
 * This file is part of the Kimple package.
 *
 * (c) Vanksen <devlux@vanksen.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vanksen\Kimple;

use Twig_Loader_Filesystem;
use Twig_Environment;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\VarDumper;
use Detection;
use Exception;

/**
 * Class App
 * @package Vanksen\Kimple
 */
class App
{

    /**
     * App' settings
     * @var array
     */
    protected $settings = [];

    /**
     * App's translations
     * @var array
     */
    protected $translations = [];

    /**
     * Current language of the app
     * @var
     */
    protected $language;

    /**
     * Type of device used to access to the app.
     * @var (desktop || mobile)
     */
    protected $device;


    /**
     * App constructor.
     */
    public function __construct()
    {
        $this->setSettings();
        $this->setTranslations();
        $this->setDevice();
        $this->setLanguage();
    }

    /**
     * Sets the app language according to the url.
     */
    private function setLanguage()
    {
        $q = !empty($_GET['q']) ? $_GET['q'] : '';
        $default_language = $this->settings['default_language'];

        // for multilingual apps
        if ($this->isMultilingual()) {

            $authorized_languages = $this->settings['languages'];
            if (in_array($q, $authorized_languages)) {
                $this->language = $q;
            } else {
                $this->language = $default_language;
                header("Location: /" . $default_language);
            }
        } else {
            // sets the app's language at the default value
            $this->language = $default_language;

            // refuse any arguments in the url
            if (!empty($q)) {
                header("Location: /");
            }
        }
    }

    /**
     * Retrieve and set the app' settings.
     */
    private function setSettings()
    {
        $this->settings = Yaml::parse(file_get_contents(APP_ROOT . '/settings.yml', FILE_USE_INCLUDE_PATH));
        $this->settings['system_tpl_folder'] = __DIR__;
    }

    /**
     * Retrieve and set the app's translations.
     */
    private function setTranslations()
    {
        $this->translations = Yaml::parse(file_get_contents(APP_ROOT . '/translations.yml', FILE_USE_INCLUDE_PATH));
    }

    /**
     * Checks & sets the type of device used to access to the app.
     */
    private function setDevice()
    {
        try {
            if (class_exists('Detection\MobileDetect')) {
                $device = 'desktop';
                $mobile_detect = new Detection\MobileDetect();
                if ($mobile_detect->isMobile()) {
                    $device = 'mobile';
                }
                $this->device = $device;
            } else {
                throw new Exception('Error while instantiating the "Mobile_Detect" class.');
            }
        } catch (Exception $e) {
            dump($e);
        }
    }

    /**
     * Checks if the app is configured to be multilingual.
     * @return bool
     */
    private function isMultilingual()
    {
        if (sizeof($this->settings['languages']) > 1) {
            return true;
        }
        return false;
    }

    /**
     * Prepares the variables for the usage of Twig.
     * @param $currentTranslations
     * @return array
     */
    protected function prepareTwigContext($currentTranslations)
    {
        return $context = [
            'device' => $this->device,
            'title' => $currentTranslations['title'],
            'og_url' => '//' . $_SERVER['SERVER_NAME'] . $currentTranslations['og']['url'],
            'og_type' => $currentTranslations['og']['type'],
            'og_title' => $currentTranslations['og']['title'],
            'og_description' => $currentTranslations['og']['description'],
            'og_image' => '//' . $_SERVER['SERVER_NAME'] . '/' . APP_IMG . $currentTranslations['og']['image'],
            'favicon_url' => '//' . $_SERVER['SERVER_NAME'] . '/' . APP_ASSETS . $this->settings['favicon'],
            'kimple_apiKey' => $currentTranslations['kimple']['apiKey'],
            'kimple_dataID' => $currentTranslations['kimple']['data_id'],
        ];
    }

    /**
     * Render the HTML via the Twig template engine.
     */
    public function render($template = 'app.twig', $tplFolder = __DIR__, $context = '')
    {
        try {
            // sets the file system for the templates
            $loader = new Twig_Loader_Filesystem($tplFolder . '/views/');
            // defines the folder for the cache
            $twig = new Twig_Environment($loader, array(
                //'cache' => 'cache/'
            ));

            // Global context
            if (empty($context)) {
                // gets the current translations
                $currentTranslations = $this->translations[$this->language];
                $context = $this->prepareTwigContext($currentTranslations);
                print $twig->render($template, $context);
            }
            // Specific template
            else {
                // renders the HTML
                return $twig->render($template, $context);
            }

        } catch (Exception $e) {
            dump($e);
        }

    }

}