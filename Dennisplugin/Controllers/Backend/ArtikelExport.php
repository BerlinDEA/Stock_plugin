<?php

use Doctrine\DBAL\Driver\PDOStatement;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\Query\Expr\Join;
use Shopware\Components\CSRFWhitelistAware;
// Der CSRF Token wurde Deaktiviert
use Shopware\Models\Article\Repository as ArticleRepo;
use Shopware\Models\Article\SupplierRepository;
use Shopware\Models\Emotion\Repository as EmotionRepo;
use Shopware\Models\Form\Repository as FormRepo;


class Shopware_Controllers_Backend_ArtikelExport extends Enlight_Controller_Action implements CSRFWhitelistAware
{
    /**
     * @var ArticleRepo
     */
    protected $supplierRepository = null;
    /**
     * Emotion Repository. Deklariert für einen schnellen Zugriff auf das Emotion Repository.
     *
     * @var EmotionRepo
     * @access private
     */
    public static $emotionRepository = null;
    /**
     * @var ArticleRepo
     */
    protected $formRepository = null;

    public function preDispatch()
    {
        $this->get('template')->addTemplateDir(__DIR__ . '/../../Resources/views/');
    }

    public static function getSubscribedEvents()
    {
        return [
            'Enlight_Controller_Action_PreDispatch_Backend_Detail' => 'onEnlightControllerActionPreDispatchBackendDetail'
        ];
    }


    public function postDispatch()
    {
        $csrfToken = $this->container->get('BackendSession')->offsetGet('X-CSRF-Token');
        $this->View()->assign([ 'csrfToken' => $csrfToken ]);
    }
    /**
     * Interne Hilfsfunktion, um Zugriff auf das Formular-Repository zu erhalten.
     *
     * @return SupplierRepository
     */
    private function getSupplierRepository()
    {
        if ($this->supplierRepository === null) {
            $this->supplierRepository = $this->getModelManager()->getRepository('Shopware\Models\Article\Supplier');
        }

        return $this->supplierRepository;
    }
    /*
     * @return downloadAsCsv
     */
    public function getWhitelistedCSRFActions()
    {
        return ['index', 'export', 'download'];
    }
    /**
     * @return FormRepo
     */
    private function getFormRepository()
    {
        if ($this->formRepository === null) {
            $this->formRepository = $this->getModelManager()->getRepository('Shopware\Models\Config\Form');
        }

        return $this->formRepository;
    }
    /**
     * Hilfsfunktion, um Zugriff auf das statische deklarierte Repository zu erhalten
     *
     * @return EmotionRepo
     */
    protected function getEmotionRepository()
    {
        if (self::$emotionRepository === null) {
            self::$emotionRepository = $this->getModelManager()->getRepository('Shopware\Models\Emotion\Emotion');
        }

        return self::$emotionRepository;
    }
    // Wählen Sie die letzte lagerbestend für 12 Monate für das Formular
    public function indexAction()
    {
        // Select the last order ID for 3 months for the form
        $orders = Shopware()->Db()->fetchAll("
                            SELECT supplierID, changetime
FROM s_articles
WHERE changetime >= NOW() - INTERVAL 12 MONTH
  AND supplierID != '0'
ORDER BY supplierID DESC
                            ");
        $this->View()->assign(['orders' => $orders]);
    }
    // Vorbereiten der Exportdatei
    public function exportAction()
    {
        $buffer = array();
        $buffer['log'] = "";

        $ordernumber = $_POST["lagerselect_form"];

        $file = 'public/article_export/article_export_' . date("YmdHis"). ".csv";

        $header = "supplierID;datum;changetime;laststock;odernumber;instock\n";

        file_put_contents($file, $header, FILE_APPEND);

        $orders =  Shopware()->Db()->fetchAll("
                            SELECT o.status AS ostatus, o.*, od.*, oba.*, cc.*, u.*, oa.*, cpi.*, a.*, sd.*
FROM s_articles_details sd, s_articles a, s_order o, s_order_details od, s_order_billingaddress oba, s_core_countries cc, s_user u, s_order_attributes oa, s_core_payment_instance cpi
WHERE o.id = od.orderID
  AND o.id = oba.orderID
  AND oba.countryID = cc.id
  AND o.userID = u.id
  AND o.id = oa.orderID
  AND o.id = cpi.order_id
  AND sd.instock = a.laststock
  AND o.ordernumber >= $ordernumber
LIMIT 200
                            ");




        foreach ($orders as $order)
        {
            $line = '"'.$order['supplierID'].'";"'
                       .$order['datum'].'";"'
                       .$order['changetime'].'";"'
                       .$order['laststock'].'";"'
                       .$order['ordernumder'].'";"'
                       .$order['instock'].'";"'
                       .$order["\n"];


            $buffer['log'] .= $line;
            file_put_contents($file, $line, FILE_APPEND);
       }

        $this->View()->assign(['buffer' => $buffer]);
        $this->View()->assign(['file' => basename($file)]);
    }
    // Herunterladen der exportierten Datei
    public function downloadAction()
    {

        $file = $this->Request()->getParam('file');
        $filepath = "public/article_export/".$file;
        @set_time_limit(0);
        header('content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="'.$file.'"');
        header('Content-Length: ' . filesize($filepath));
        readfile($filepath);
        exit;
        $this->view->setTemplate();

    }



}
?>