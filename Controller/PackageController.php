<?php
namespace AppBundle\Controller;

use AppBundle\AppBundle;
use AppBundle\Repository\Reporting\AccountRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Reporting\Campaign;
use AppBundle\Service\XplentyService;
use AppBundle\Repository\Reporting\PackageRepository;


class PackageController extends BaseController
{

    private $xplentyPublisherCodes = array ('adwords' => 'adwords', 'ganalytics' => 'analytics', 'bing' => 'bingads');

    public function __construct(){
        parent::__construct();
        $this->accountRepository = new AccountRepository();
        $this->xplentyService = new XplentyService($this->settings['xplenty']);
        $this->packageRepository = new PackageRepository();
    }

    /**
 * @Route("/package/list", name="packagelist")
 */
    public function listAction()
    {
        //not implemented as of now
    }

    /**
     * Create a packages
     * @param $publisher
     * @param $publisherData
     * @param $account
     */
    public function createAction($publisher, $publisherData, $account){
        //create a connection for the package, if publisher requires it
        if($publisher == 'adwords' || $publisher == 'ganalytics' || $publisher == 'bing'){
            //if connection is already selected then don't create one but use the incoming connection
            if(is_numeric($publisherData['connection']) and $publisherData['connection'] > 0){
                $connection = $this->xplentyService->getConnectionInformation($this->xplentyPublisherCodes[$publisher], $publisherData['connection']);
            }else{
                $connection = $this->createConnectionForPackage($publisher, $account);
            }
        }else{
            $connection = false;
        }

        $packageVariables = $this->createPackageVariables($publisher, $publisherData, $account->getIndriAccountID());
        $masterPackagesForPublisherCSV = $this->settings['xplenty']['templatePackages'][$publisher];
        $masterPackagesForPublisher = explode('#',$masterPackagesForPublisherCSV);
        foreach($masterPackagesForPublisher as $basePackageID){
            $newPackage = $this->xplentyService->createPackage($basePackageID);
            $alteredPackage = $this->buildPackage($newPackage->id, $packageVariables, $account, $publisher, $connection);
            $modifiedPackage = $this->xplentyService->updatePackage($newPackage->id,$alteredPackage);
            $package['indri_account_id'] = $account->getIndriAccountID();
            $package['publisher_name'] = $publisher;
            $package['publisher_account_info'] = json_encode($packageVariables);
            $package['xplenty_package_id'] = $modifiedPackage->id;
            $package['xplenty_package_data'] = addslashes(json_encode($modifiedPackage));
            $package['xplenty_master_template_id'] = $basePackageID;
            if($connection != false) {
                $package['xplenty_connection_data'] = addslashes(json_encode($connection));
                $package['xplenty_connection_id'] = $connection->id;
            }else{
                $package['xplenty_connection_data'] = '';
                $package['xplenty_connection_id'] = 0;
            }
            $this->packageRepository->create($package);
        }
        return;
    }

    /**
     * Build a package
     * @param $basePackageID
     * @param $packageVariables
     * @param $account
     * @param $publisher
     * @param $connection
     * @return mixed
     */
    public function buildPackage($basePackageID, $packageVariables, $account, $publisher, $connection){

        $packageInfos = $this->xplentyService->getPackageInformation($basePackageID);
        $dataFlow = json_decode($packageInfos->data_flow_json, true);
        //if connection is not false (it can be false if the package is of REST type and do not need a connection)
        // then replaces the connection in the package flow JSON
        if($connection != false) {
            $newFlow = $this->replaceConnection($dataFlow, $connection->id);
            $packageInfos->data_flow_json = json_encode($newFlow);
        }
        //replace the variables that we need to change
        $variables = $packageInfos->variables;
        foreach($packageVariables as $key => $name){
            $variables->$key = "'" . $name . "'";
        }
        $packageInfos->variables = $variables;
        $packageInfos->name = $account->getAccountName() . " " . ucfirst($publisher) . " ARM Package";
        return $packageInfos;
    }

    /**
     * Replaces the connection in the the package data flows
     * @param $dataFlow
     * @param $connectionID
     * @return array
     */
    public function replaceConnection($dataFlow, $connectionID){
        $newFlow = array();
        foreach ($dataFlow['components'] as $component) {
            foreach ($component as $flowName => $flowItem) {
                $flowItem['cloud_storage_connection_id'] = $connectionID;
                $newFlowItem = $flowItem;
                $newComponent[$flowName] = $newFlowItem;
                $newFlow['components'][] = $newComponent;
                unset($newFlowItem);
                unset($newComponent);
            }
        }
        $newFlow['edges'] = $dataFlow['edges'];
        return $newFlow;
    }

    /**
     * Updates packages for a certain publisher
     * @param $publisher
     * @param $publisherData
     * @param $account
     */
    public function updateAction($publisher, $publisherData, $account){
        $packageVariables = $this->createPackageVariables($publisher, $publisherData, $account->getIndriAccountID());
        $existingPackages = $this->packageRepository->findByPublisherAndIndriAccountID($publisher, $account->getIndriAccountID());
        foreach($existingPackages as $packageData){
            $actualPackage = $this->xplentyService->getPackageInformation($packageData['xplenty_package_id']);
            //replace the variables that we need to change
            if (is_object($actualPackage)) {
                //update variables
                $variables = $actualPackage->variables;
                foreach ($packageVariables as $key => $name) {
                    $variables->$key = "'" . $name . "'";
                }
                $actualPackage->variables = $variables;
                //update connection if needed
                if(isset($publisherData['connection']) and $publisherData['connection'] > 0) {
                    $dataFlow = json_decode($actualPackage->data_flow_json, true);
                    $newFlow = $this->replaceConnection($dataFlow, $publisherData['connection']);
                    $connection = $this->xplentyService->getConnectionInformation($this->xplentyPublisherCodes[$publisher], $publisherData['connection']);
                    $actualPackage->data_flow_json = json_encode($newFlow);
                    $packageData['xplenty_connection_id'] = $publisherData['connection'];
                    $packageData['xplenty_connection_data'] = addslashes(json_encode($connection));
                }
                //update package in Xplenty
                $modifiedPackage = $this->xplentyService->updatePackage($actualPackage->id, $actualPackage);
                //then update package in local metadata repository
                $packageData['publisher_account_info'] = json_encode($packageVariables);
                $packageData['xplenty_package_data'] = addslashes(json_encode($modifiedPackage));
                $this->packageRepository->update($packageData);
            }
        }
        return;

    }

    /**
     * Start the sync process
     * @param $publisher
     */
    public function startSync($publisher){
        $publishersAvailable = array('adwords','bing','facebook','ganalytics','tradedesk');
        if($publisher == 'all'){
            $publishersToProcess = $publishersAvailable;
        }else{
            $publishersToProcess = array($publisher);
        }
        foreach($publishersToProcess as $syncPublisher){
            $this->syncPackages($syncPublisher);
        }
        return;
    }

    /**
     * Sync the packages
     * @param $publisher
     */
    public function syncPackages($publisher){
        $templatePackagesArray = explode('#',$this->settings['xplenty']['templatePackages'][$publisher]);
        //cycle through master packages
        foreach($templatePackagesArray as $masterPackageID){
            $masterPackage = $this->xplentyService->getPackageInformation($masterPackageID);
            $packagesToSync = $this->packageRepository->findByMasterTemplate($masterPackageID);
            //cycle through packages that have the master template = $masterPackageID
            foreach($packagesToSync as $packageToSync){
                $newDataFlow = $this->getNewDataFlow($masterPackage->data_flow_json, $packageToSync);
                $packageData = $this->xplentyService->getPackageInformation($packageToSync['xplenty_package_id']);
                $packageData->data_flow_json = $newDataFlow;
                $this->xplentyService->updatePackage($packageToSync['xplenty_package_id'],$packageData);
            }
        }
        return;
    }

    /**
     * Get the new dataflow for the package sync
     * @param $masterDataFlow
     * @param $packageToSync
     * @return string
     */
    public function getNewDataFlow($masterDataFlow, $packageToSync){
        //we have a package without connection, no need to change connection parameters
        if($packageToSync['xplenty_connection_id'] == 0 or $packageToSync['xplenty_connection_id'] == ''){
            $newDataFlow = $masterDataFlow;
        }else{
            $connectionID = $packageToSync['xplenty_connection_id'];
            $dataFlow = json_decode($masterDataFlow, true);
            $newFlow = $this->replaceConnection($dataFlow, $connectionID);
        }
        return json_encode($newFlow);
    }

    /**
     * @Route("/package/sync", name="packagesync")
     */
    public function packageSyncAction(Request $request)
    {
        $publisher = $request->get('publisher');
        if(isset($publisher) and $publisher != ""){
            $this->startSync($publisher);
        }
        return $this->render('package/packageSync.html.twig', array());
    }

    public function disableAction($publisher, $publisherData, $account){

    }

    /**
     * Creates a connection for an account/package
     * @param $publisher
     * @param $account
     * @return bool|mixed
     */
    public function createConnectionForPackage($publisher, $account){
        $connectionType = '';
        switch($publisher){
            case 'adwords':
                $connectionType = 'adwords';
                break;
            case 'ganalytics':
                $connectionType = 'analytics';
                break;
            case 'bing':
                $connectionType = 'bingads';
                break;
            default:
                return false;
        }
        if($connectionType != ''){
            $connectionName = $account->getAccountName() . " " . ucfirst($publisher) . " ARM Connection";
            $connection = $this->xplentyService->createConnection($connectionName,'dummy','dummy','',$connectionType);
        }
        return $connection;
    }

    /**
     * Creates the necessary variables for package creation
     * @param $publisher
     * @param $publisherData
     * @param $indriAccountID
     * @return array
     */
    public function createPackageVariables($publisher, $publisherData, $indriAccountID){
        $packageVariables = array();
        $packageVariables['IndriAccountId'] = $indriAccountID;
        switch($publisher){
            case 'adwords':
                $packageVariables['AdwordsCustomer'] = $publisherData['AdwordsCustomer'];
                break;
            case 'bing':
                $packageVariables['BingAccountId'] = $publisherData['BingAccountId'];
                break;
            case 'ganalytics':
                $packageVariables['profileids'] = $publisherData['profileids'];
                break;
            case 'facebook':
                $packageVariables['token'] = $this->settings['xplenty']['facebookToken'];
                $packageVariables['indri_account_id'] = $indriAccountID;
                $packageVariables['account_id'] = $publisherData['account_id'];
                break;
            case 'tradedesk':
                $packageVariables['AdvertiserId'] = $publisherData['AdvertiserId'];
                break;
        }
        return $packageVariables;
    }

}