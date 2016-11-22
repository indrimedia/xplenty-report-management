<?php
namespace AppBundle\Controller;

use AppBundle\Service\RedshiftService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\AppBundle;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Service\AWSService;
use Symfony\Component\Yaml\Parser;


class BaseController extends Controller{

    /**
     * BaseController constructor.
     */
    public function __construct(){
        $this->retrieveSettings();
        $this->initializeExternalServices();
    }

    /**
     * Initialize external services
     */
    public function initializeExternalServices(){
        //$this->AWS = new AWSService($this->settings['aws']);
        $this->redshiftService = new RedshiftService($this->settings['redshift']);

    }

    /**
     * Retrieve application settings from the configuration file
     */
    public function retrieveSettings(){
        $yaml = new Parser();
        $this->settings = $yaml->parse(file_get_contents(__DIR__ .'/../Configuration/arm.yml'));
        return;
    }


}