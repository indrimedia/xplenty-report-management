<?php
namespace AppBundle\Entity\Reporting;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="accounts")
 */

class Account
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     */
    private $accountName;

    /**
     * @var string
     */
    private $accountGroup;

    /**
     * @var int
     */
    private $customerID;

    /**
     * @var string
     */
    private $externalAccountID;

    /**
     * @var string
     */
    private $externalAccountName;

    /**
     * @var int
     */
    private $active;

    /**
     * @var string
     */
    private $indriAccountID;


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
     * @return string
     */
    public function getAccountName()
    {
        return $this->accountName;
    }

    /**
     * @param string $accountName
     */
    public function setAccountName($accountName)
    {
        $this->accountName = $accountName;
    }

    /**
     * @return string
     */
    public function getAccountGroup()
    {
        return $this->accountGroup;
    }

    /**
     * @param string $accountGroup
     */
    public function setAccountGroup($accountGroup)
    {
        $this->accountGroup = $accountGroup;
    }

    /**
     * @return int
     */
    public function getCustomerID()
    {
        return $this->customerID;
    }

    /**
     * @param int $customerID
     */
    public function setCustomerID($customerID)
    {
        $this->customerID = $customerID;
    }

    /**
     * @return string
     */
    public function getExternalAccountID()
    {
        return $this->externalAccountID;
    }

    /**
     * @param string $externalAccountID
     */
    public function setExternalAccountID($externalAccountID)
    {
        $this->externalAccountID = $externalAccountID;
    }

    /**
     * @return string
     */
    public function getExternalAccountName()
    {
        return $this->externalAccountName;
    }

    /**
     * @param string $externalAccountName
     */
    public function setExternalAccountName($externalAccountName)
    {
        $this->externalAccountName = $externalAccountName;
    }

    /**
     * @return int
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * @param int $active
     */
    public function setActive($active)
    {
        $this->active = $active;
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


}

