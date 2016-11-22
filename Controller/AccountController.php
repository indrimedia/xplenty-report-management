<?php
namespace AppBundle\Controller;

use AppBundle\Entity\Reporting\Account;
use AppBundle\Entity\Reporting\Customer;
use AppBundle\Repository\Reporting\AccountRepository;
use AppBundle\Repository\Reporting\CustomerRepository;
use AppBundle\Repository\Reporting\PackageRepository;
use AppBundle\Service\RedshiftService;
use AppBundle\AppBundle;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Reporting\Campaign;
use AppBundle\Controller\PackageController;
use AppBundle\Repository\Reporting\CommissionRepository;
use AppBundle\Service\XplentyService;

class AccountController extends BaseController
{

    /**
     * AccountController constructor.
     */
    public function __construct(){
        parent::__construct();
        $this->accountRepository = new AccountRepository();
        $this->customerRepository = new CustomerRepository();
        $this->packageRepository = new PackageRepository();
        $this->commissionRepository = new CommissionRepository();
        $this->xplentyService = new XplentyService($this->settings['xplenty']);
    }
    /**
     * List action for the accounts
     * @Route("/account/list", name="accountlist")
     */
    public function listAction(Request $request)
    {
        $filterCustomer = $request->get('customer');

        if(isset($filterCustomer) and $filterCustomer != ''){
            $accounts = $this->accountRepository->findByCustomer($filterCustomer);
            $viewFilter = $filterCustomer;
        }else{
            $accounts = $this->accountRepository->findAllAccounts();
            $viewFilter = '';
        }
        $customers = $this->customerRepository->findAllCustomers();
        return $this->render('account/list.html.twig', array('accounts' => $accounts,
            'customers' => $customers, 'filterCustomer' => $viewFilter));
    }

    /**
     * Edit action for the account controller
     * @Route("/account/edit", name="accountedit")
     * @return Response
     */
    public function editAction(Request $request){
        $publisherData = array();
        $packageInfos = $this->packageRepository->findByIndriAccountID($request->query->get('account'));
        if(is_array($packageInfos)) {

            foreach ($packageInfos as $packageInfo) {
                $publisherData[$packageInfo['publisher_name']] = array_merge(json_decode($packageInfo['publisher_account_info'],true),
                    array('connection' => $packageInfo['xplenty_connection_id']));
            }
        }
        $accountObject = $this->accountRepository->findByIndriAccountID($request->query->get('account'));
        return $this->render('account/edit.html.twig', array(
            'account' => $accountObject,
            'customers' => $this->customerRepository->findAllCustomers(),
            'publisherData' => $publisherData,
            'connections' => $this->getPublisherConnections()
            ));
    }

    /**
     * New action for the accounts
     * @Route("/account/new",name="accountnew")
     * @return Response
     */
    public function newAction(){
        $account = new Account();
        $commission = $this->settings['defaultCommission'];

        return $this->render('account/new.html.twig',
            array('account' => $account,
                'customers' => $this->customerRepository->findAllCustomers(),
                'commission' => $commission,
                'connections' => $this->getPublisherConnections()
            ));
    }

    /**
     * Retrieves the publisher connections
     * @return mixed
     */
    public function getPublisherConnections(){
        $publishers = array('adwords','bingads','analytics');
        foreach($publishers as $publisher){
            $connections[$publisher] = $this->xplentyService->listConnections($publisher);
        }
        return $connections;
    }

    /**
     * @Route("/account/create",name="accountcreate")
     * @param Request $request
     * @return Response
     */
    public function createAction(Request $request){
        $account = new Account();
        $account->setIndriAccountID(uniqid(sha1($request->get('accountName') . rand(0,1000))));
        $account = $this->getRequestData($account, $request, 'create');
        $this->accountRepository->create($account);
        $this->addFlash(
            'notice',
            'Your changes were saved!'
        );
        return $this->redirectToRoute('accountlist');
    }

    /**
     * @Route("/account/update", name="accountupdate")
     */
    public function updateAction(Request $request){
        $indriAccountID = $request->get('indriAccountID');
        $account = $this->accountRepository->findByIndriAccountID($indriAccountID);
        $account = $this->getRequestData($account, $request, 'update');
        $this->accountRepository->update($account);
        $this->addFlash(
            'notice',
            'Your changes were saved!'
        );
        return $this->redirectToRoute('accountlist');
    }

    /**
     * Shared function between create and update in order to reduce duplicate code, it just sets incoming data onto the object
     * @param $account
     * @param $request
     * @param $saveType
     * @return mixed
     */
    public function getRequestData($account, $request, $saveType){
        if($saveType == 'create') {
            $this->saveCommissions($account, $request, $saveType);
        }
        $account->setAccountGroup(addslashes($request->get('accountGroup')));
        $account->setAccountName(addslashes($request->get('accountName')));
        $account->setExternalAccountID(addslashes($request->get('externalAccountID')));
        $account->setExternalAccountName(addslashes($request->get('externalAccountName')));
        $account->setCustomerID(addslashes($request->get('customerID')));
        $this->processPublishers($request->get('publishers'), $account, $saveType);
        return $account;
    }

    /**
     * Creates default= commissions upon account creation
     * @param $account
     * @param $request
     * @param $saveType
     * @return bool|void
     */
    public function saveCommissions($account, $request, $saveType){
        $commission = $request->get('commission');
        if($commission == ''){
            return false;
        }
        $publishers = array('adwords','bing','facebook','tradedesk');
        foreach($publishers as $publisher) {
            $commissionItem = array();
            $commissionItem['publisher'] = $publisher;
            $commissionItem['indri_account_id'] = $account->getIndriAccountID();
            $commissionItem['commission'] = $commission;
            switch ($saveType) {
                case 'create':
                    $this->commissionRepository->create($commissionItem);
                    break;
                case 'update':
                    $this->commissionRepository->update($commissionItem);
                    break;
            }
        }

        return;
    }

    /**
     * Process incoming publisher information and create/update packages if needed
     * @param $publisherData
     * @param $account
     * @param $saveType
     */
    public function processPublishers($publisherData, $account, $saveType){
        $packageController = new PackageController();
        foreach($publisherData as $publisher => $publisherInfos){
            if($publisherInfos['enabled'] == 1){
                if($saveType == 'create'){
                    //create packages
                    $packageController->createAction($publisher, $publisherInfos, $account);
                }
                elseif($saveType == 'update'){
                    $existingPackages = $this->packageRepository->findByPublisherAndIndriAccountID($publisher, $account->getIndriAccountID());
                    if(is_array($existingPackages) AND $existingPackages[0]['xplenty_package_id'] > 0){
                        //update packages
                        $packageController->updateAction($publisher, $publisherInfos, $account);
                    }else{
                        //create package
                        $packageController->createAction($publisher, $publisherInfos, $account);
                    }
                }
            }
            else{
                if($saveType == 'create'){
                    //do nothing
                }elseif($saveType == 'update'){
                    //@todo disable packages if they already exist
                    //$packageController->disableAction($publisher, $publisherInfos, $account);
                }
            }

        }

        return;
    }
}