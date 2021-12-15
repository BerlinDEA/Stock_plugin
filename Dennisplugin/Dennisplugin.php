<?php


namespace Dennisplugin;

use Shopware\Components\Plugin;


class Dennisplugin extends Plugin


{
    /**
     * @return array
     */
    public function getInfo()
    {
        return [
            'version' => '1.0.0',
            'Label' => 'Dennis Reinhardt Plugins'
        ];
    }

    /**
     * @return bool
     */

        public function install($installContext)
        {
            // Add the csv directory
            if(!is_dir("public/export_artikel/")){
                mkdir("public/export_artikel/", 0755, $recursive = TRUE);
            }

            // Change the icon menu
            Shopware()->Db()->query("
                UPDATE s_core_menu
                SET class = 'sprite-database-export'
                WHERE name = 'Export Atrikel'");
         parent::install($installContext);
        return TRUE;
    }
    public function uninstall()
    {
        return TRUE;
    }

    public static function getSubscribedEvents()
    {
        return [
            'Enlight_Controller_Dispatcher_ControllerPath_Backend_Artikel_Export' => 'onGetBackendController'
        ];
    }

    /**
     * @return string
     */
    public function onGetBackendController()
    {
        return __DIR__ . '/Controllers/Backend/ArtikelExport.php';
    }
}

