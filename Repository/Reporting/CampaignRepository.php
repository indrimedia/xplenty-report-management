<?php

namespace AppBundle\Repository\Reporting;

use AppBundle\Entity\Reporting\Campaign;

/**
 * CampaignRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class CampaignRepository extends BaseRepository
{
    public $table = "xp_campaigns";

    public $nameField = "campaign_id";

    /**
     * Find all campaigns
     * @return array|bool
     */
    public function findAllCampaigns(){
        $campaignArray = $this->findAllEntities();
        $campaignObjects = $this->instantiateObjects($campaignArray);
        return $campaignObjects;
    }

    /**
     * Instantiate campaign objects
     * @param $campaignArray
     * @return array|bool
     */
    private function instantiateObjects($campaignArray){
        if(is_array($campaignArray)) {
            foreach ($campaignArray as $item) {
                $campaigns[] = $this->createCampaignObject($item);
            }
            return $campaigns;
        }
        return false;
    }

    /**
     * Find campaigns by account
     * @param $accountID
     * @return array|bool
     */
    public function findByAccount($accountID){
        $campaignArray = $this->findByProperty($this->table, '*', 'indri_account_id', $accountID, 'campaign_id', ' ASC', 500);
        $campaignObjects = $this->instantiateObjects($campaignArray);
        return $campaignObjects;
    }

    /**
     * Find campaigns by ID
     * @param $id
     * @return Campaign
     */
    public function findByCampaignID($id){
        $campaignArray = $this->findByProperty($this->table, '*', 'campaign_id', $id);
        return $this->createCampaignObject($campaignArray[0]);
    }

    /**
     * Find campaigns by ID and Indri Account ID
     * @param $campaignID
     * @param $indriAccountID
     * @return Campaign
     */
    public function findByCampaignIDAndIndriAccountID($campaignID, $indriAccountID){
        $properties = array(0 => 'campaign_id', 1 => 'indri_account_id');
        $values[0] = $campaignID;
        $values[1] = $indriAccountID;
        $campaignArray = $this->findByMultipleProperties($this->table, '*', $properties, $values);
        return $this->createCampaignObject($campaignArray[0]);
    }

    /**
     * Find campaigns by ID and Indri Account ID
     * @param $campaignID
     * @param $indriAccountID
     * @param $publisher
     * @return Campaign
     */
    public function findByCampaignIDAndIndriAccountIDAndPublisher($campaignID, $indriAccountID, $publisher){
        $properties = array(0 => 'campaign_id', 1 => 'indri_account_id', 2 => 'publisher');
        $values[0] = $campaignID;
        $values[1] = $indriAccountID;
        $values[2] = $publisher;
        $campaignArray = $this->findByMultipleProperties($this->table, '*', $properties, $values);
        return $this->createCampaignObject($campaignArray[0]);
    }

    /**
     * Update campaign
     * @param $campaign
     * @return bool
     */
    public function update($campaign){
        $item['label1'] = $campaign->getLabel1();
        $item['label2'] = $campaign->getLabel2();
        $item['label3'] = $campaign->getLabel3();
        $item['active_reporting'] = $campaign->getActiveReporting();
        $whereFields = array (0 => 'campaign_id', 1 => 'indri_account_id', 2 => 'publisher');
        $whereValues = array (0 => $campaign->getCampaignID(), 1 => $campaign->getIndriAccountID(), 2 => $campaign->getPublisherName());
        return $this->updateSQLByMultipleCriteria($this->table, $item, $whereFields, $whereValues);
    }

    /**
     * Create campaign object
     * @param $item
     * @return Campaign
     */
    public function createCampaignObject($item){
        $campaign = new Campaign();
        if(isset($item['campaign_id'])) {
            $campaign->setCampaignID($item['campaign_id']);
        }
        $campaign->setIndriAccountID($item['indri_account_id']);
        $campaign->setPublisherName($item['publisher']);
        $campaign->setAccountGroup($item['account_group_label']);
        $campaign->setLabel1($item['label1']);
        $campaign->setLabel2($item['label2']);
        $campaign->setLabel3($item['label3']);
        $campaign->setActiveReporting($item['active_reporting']);
        $campaign->setCampaignName($item['campaign_name']);

        return $campaign;
    }
}
