<?php
namespace AppBundle\Controller;

use AppBundle\AppBundle;
use AppBundle\Repository\Reporting\CommissionRepository;
use AppBundle\Repository\Reporting\AccountRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Entity\Reporting\Campaign;
use Symfony\Component\HttpFoundation\Request;

class CommissionController extends BaseController
{

    /**
     * CommissionController constructor.
     */
    public function __construct(){
        parent::__construct();
        $this->accountRepository = new AccountRepository();
        $this->commissionRepository = new CommissionRepository();
    }

    /**
     * @Route("/commission/list", name="commissionlist")
     */
    public function listAction(Request $request)
    {
        $filterAccount = $request->get('account');
        if(isset($filterAccount) and $filterAccount != ''){
            $commissions = $this->commissionRepository->findByAccount($filterAccount);
            $viewFilterAccount = $filterAccount;
        }else{
            $commissions = $this->commissionRepository->findAllCommissions();
            $viewFilterAccount = '';
        }

        $accounts = $this->accountRepository->findAllAccounts();
        return $this->render('commission/list.html.twig', array('commissions' => $commissions,
            'accounts' => $accounts, 'filteraccount' => $viewFilterAccount));
    }

    /**
     * @Route("/commission/update", name="commissionupdate")
     * @param $request
     */
    public function updateCommissionAction(Request $request){
        $publisher = $request->get('publisher');
        $account = $request->get('account');
        $commission = $request->get('commission');
        if($commission == '' || !is_numeric($commission)){
            $this->addFlash(
                'warning',
                'There is no commission value set or the commission value is not numeric!'
            );
            return $this->redirectToRoute('commissionlist');
        }
        if($account == 'all'){
            $changeAccounts = $this->accountRepository->findAllAccounts();
            foreach($changeAccounts as $accountObject){
                $accountIDsToBeChanged[] = $accountObject->getIndriAccountID();
            }
        }else{
            $accountIDsToBeChanged[] = $account;
        }
        if($publisher == 'all'){
            $publishersToBeChanged = array('adwords','bing','facebook','tradedesk');
        }else{
            $publishersToBeChanged[] = $publisher;
        }
        foreach($accountIDsToBeChanged as $currentAccount){
            foreach($publishersToBeChanged as $currentPublisher){
                $newCommissionValue['commission'] = $commission;
                $newCommissionValue['indri_account_id'] = $currentAccount;
                $newCommissionValue['publisher'] = $currentPublisher;
                print "<pre>";
                //check if commission exists or needs to be created // it should exist by default but you never know
                $existingCommission = $this->commissionRepository->findCommissionByAccountAndPublisher($currentAccount, $currentPublisher);
                print_r($existingCommission);
                if(is_array($existingCommission) and $existingCommission['commission'] > 0){
                    //we have a commission, let's update it
                    $this->commissionRepository->update($newCommissionValue);
                }else{
                    //we don't have a commission in the database for this account, let's create it
                    $this->commissionRepository->create($newCommissionValue);
                }
            }
        }
        $this->addFlash(
            'notice',
            'Your commissions were changed!'
        );
        return $this->redirectToRoute('commissionlist');
    }
}