<?php
namespace AppBundle\Controller;

use AppBundle\AppBundle;
use AppBundle\Repository\Reporting\AccountRepository;
use AppBundle\Repository\Reporting\CampaignRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Reporting\Campaign;

class CampaignController extends BaseController
{
    public function __construct(){
        $this->campaignRepository = new CampaignRepository();
        $this->accountRepository = new AccountRepository();
    }
    /**
     * @Route("/campaign/list", name="campaignlist")
     */
    public function listAction(Request $request)
    {
        $filterAccount = $request->get('account');
        $this->saveCampaignStates($request);
        if(isset($filterAccount) and $filterAccount != ''){
            $campaigns = $this->campaignRepository->findByAccount($filterAccount);
            $viewFilterAccount = $filterAccount;
        }else{
            $campaigns = $this->campaignRepository->findAllCampaigns();
            $viewFilterAccount = '';
        }
        $accounts = $this->accountRepository->findAllAccounts();
        $viewVariables['campaigns'] = $campaigns;
        $viewVariables['accounts'] = $accounts;
        $viewVariables['filteraccount'] = $viewFilterAccount;
        return $this->render('campaign/list.html.twig', $viewVariables);
    }

    /**
     * Saves the campaigns state
     * @param $request
     */
    public function saveCampaignStates($request){
        $campaignStates = $request->get('active');
        $previousStates = $request->get('previousstate');
        if(!isset($campaignStates) || !isset($previousStates)){
            return;
        }

        foreach($previousStates as $accountID => $publisher) {
            foreach ($publisher as $publisherName => $publisherCampaigns) {
                foreach ($publisherCampaigns as $campaignID => $campaignState) {
                    //checkbox isn't set
                    if (!isset($campaignStates[$accountID][$publisherName][$campaignID])) {
                        //previously was active, so we have a change
                        if ($previousStates[$accountID][$publisherName][$campaignID] == 1) {
                            $updateValue = 0;
                        }
                        //checkbox is set
                    } else {
                        //previously was inactive, so we have a change
                        if ($previousStates[$accountID][$publisherName][$campaignID] == 0) {
                            $updateValue = 1;
                        }
                    }
                    if (isset($updateValue) and is_numeric($updateValue)) {
                        $this->saveCampaign($accountID, $campaignID, $publisherName, $updateValue);
                    }
                    if (isset($updateValue)) {
                        unset($updateValue);
                    }
                }
            }
        }
        $this->addFlash(
            'notice',
            'Campaign states were saved!'
        );
        return;
    }

    /**
     * Actually save the campaign
     * @param $accountID
     * @param $campaignID
     * @param $updateValue
     */
    public function SaveCampaign($accountID, $campaignID, $publisherName, $updateValue){

        $campaignObject = $this->campaignRepository->findByCampaignIDAndIndriAccountIDAndPublisher($campaignID, $accountID, $publisherName);
        $campaignObject->setActiveReporting($updateValue);
        $this->campaignRepository->update($campaignObject);
        return;
    }

    /**
     * @Route("/campaign/edit", name="campaignedit")
     * @return Response
     */
    public function editAction(Request $request){
        $campaignObject = $this->campaignRepository->findByCampaignID($request->query->get('campaign'));
        return $this->render('campaign/edit.html.twig', array('campaign' => $campaignObject));
    }

    /**
     * @Route("/campaign/update", name="campaignupdate")
     */
    public function updateAction(Request $request){
        $indriAccountID = $request->get('indriAccountID');
        $campaignID = $request->get('campaignID');
        $campaign = $this->campaignRepository->findByCampaignIDAndIndriAccountID($campaignID, $indriAccountID);

        $campaign->setLabel1(addslashes($request->get('label1')));
        $campaign->setLabel2(addslashes($request->get('label2')));
        $campaign->setLabel3(addslashes($request->get('label3')));
        $this->campaignRepository->update($campaign);
        $this->addFlash(
            'notice',
            'Your changes were saved!'
        );
        return $this->redirectToRoute('campaignlist');
    }
}