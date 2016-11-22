<?php

namespace AppBundle\Entity\Reporting;
use Doctrine\ORM\Mapping as ORM;

/**
 * Campaign
 */

/**
 * @ORM\Entity
 * @ORM\Table(name="xp_campaigns")
 */

class Campaign
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $campaignID;

    /**
     * @var string
     */
    private $label1;

    /**
     * @var string
     */
    private $label2;

    /**
     * @var string
     */
    private $label3;

    /**
     * @var string
     */
    private $indriAccountID;

    /**
     * @var string
     */
    private $publisherName;

    /**
     * @var string
     */
    private $accountGroup;

    /**
     * @var integer
     */
    private $active_reporting;

    /**
     * @var string
     */
    private $campaign_name;



    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set campaignID
     *
     * @param string $campaignID
     *
     * @return Campaign
     */
    public function setCampaignID($campaignID)
    {
        $this->campaignID = $campaignID;

        return $this;
    }

    /**
     * Get campaignID
     *
     * @return string
     */
    public function getCampaignID()
    {
        return $this->campaignID;
    }

    /**
     * Set label1
     *
     * @param string $label1
     *
     * @return Campaign
     */
    public function setLabel1($label1)
    {
        $this->label1 = $label1;

        return $this;
    }

    /**
     * Get label1
     *
     * @return string
     */
    public function getLabel1()
    {
        return $this->label1;
    }

    /**
     * Set label2
     *
     * @param string $label2
     *
     * @return Campaign
     */
    public function setLabel2($label2)
    {
        $this->label2 = $label2;

        return $this;
    }

    /**
     * Get label2
     *
     * @return string
     */
    public function getLabel2()
    {
        return $this->label2;
    }

    /**
     * Set label3
     *
     * @param string $label3
     *
     * @return Campaign
     */
    public function setLabel3($label3)
    {
        $this->label3 = $label3;

        return $this;
    }

    /**
     * Get label3
     *
     * @return string
     */
    public function getLabel3()
    {
        return $this->label3;
    }

    /**
     * Set publisherName
     *
     * @param string $publisherName
     *
     * @return Campaign
     */
    public function setPublisherName($publisherName)
    {
        $this->publisherName = $publisherName;

        return $this;
    }

    /**
     * Get publisherName
     *
     * @return string
     */
    public function getPublisherName()
    {
        return $this->publisherName;
    }

    /**
     * Set accountGroup
     *
     * @param string $accountGroup
     *
     * @return Campaign
     */
    public function setAccountGroup($accountGroup)
    {
        $this->accountGroup = $accountGroup;

        return $this;
    }

    /**
     * Get accountGroup
     *
     * @return string
     */
    public function getAccountGroup()
    {
        return $this->accountGroup;
    }

    /**
     * @return string
     */
    public function getIndriAccountID()
    {
        return $this->indriAccountID;
    }

    /**
     * @param string $indriAccountID
     */
    public function setIndriAccountID($indriAccountID)
    {
        $this->indriAccountID = $indriAccountID;
    }

    /**
     * @return mixed
     */
    public function getActiveReporting()
    {
        return $this->active_reporting;
    }

    /**
     * @param mixed $active_reporting
     */
    public function setActiveReporting($active_reporting)
    {
        $this->active_reporting = $active_reporting;
    }

    /**
     * @return string
     */
    public function getCampaignName()
    {
        return $this->campaign_name;
    }

    /**
     * @param string $campaign_name
     */
    public function setCampaignName($campaign_name)
    {
        $this->campaign_name = $campaign_name;
    }

}

