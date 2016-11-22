<?php

namespace AppBundle\Entity\Reporting;
use Doctrine\ORM\Mapping as ORM;

/**
 * Customer
 */

/**
 * @ORM\Entity
 * @ORM\Table(name="customers")
 */

class Customer
{

    /**
     * @var string
     */
    private $customerID;

    /**
     * @var string
     */
    private $customerName;

    /**
     * @var string
     */
    private $externalCustomerCode;

    /**
     * @var string
     */
    private $externalCustomerName;

    /**
     * @return mixed
     */
    public function getCustomerID()
    {
        return $this->customerID;
    }

    /**
     * @param mixed $customerID
     */
    public function setCustomerID($customerID)
    {
        $this->customerID = $customerID;
    }

    /**
     * @return string
     */
    public function getCustomerName()
    {
        return $this->customerName;
    }

    /**
     * @param string $customerName
     */
    public function setCustomerName($customerName)
    {
        $this->customerName = $customerName;
    }

    /**
     * @return string
     */
    public function getExternalCustomerCode()
    {
        return $this->externalCustomerCode;
    }

    /**
     * @param string $externalCustomerCode
     */
    public function setExternalCustomerCode($externalCustomerCode)
    {
        $this->externalCustomerCode = $externalCustomerCode;
    }

    /**
     * @return string
     */
    public function getExternalCustomerName()
    {
        return $this->externalCustomerName;
    }

    /**
     * @param string $externalCustomerName
     */
    public function setExternalCustomerName($externalCustomerName)
    {
        $this->externalCustomerName = $externalCustomerName;
    }

}

