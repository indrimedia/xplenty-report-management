<?php

namespace AppBundle\Repository\Reporting;
use AppBundle\AppBundle;
use AppBundle\Repository\Reporting\BaseRepository;
use AppBundle\Entity\Reporting\Account;

/**
 * AccountRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class AccountRepository extends BaseRepository
{
    public $table = "xp_accounts";

    public $nameField = "account_name";

    /**
     * Finds all accounts
     * @return array|bool
     */
    public function findAllAccounts(){
        $accountArray = $this->findAllEntities();
        $accountObjects = $this->instantiateObjects($accountArray);
        return $accountObjects;
    }

    /**
     * instantiate account objects
     * @param $accountArray
     * @return array|bool
     */
    private function instantiateObjects($accountArray){
        if(is_array($accountArray)) {
            foreach ($accountArray as $item) {
                $accounts[] = $this->createAccountObject($item);
            }
            return $accounts;
        }
        return false;
    }

    /**
     * Find by customer
     * @param $customerID
     * @return array|bool
     */
    public function findByCustomer($customerID){
        $accountArray = $this->findByProperty($this->table, '*', 'customer_id', $customerID, 'account_name', ' ASC', 100);
        $accountObjects = $this->instantiateObjects($accountArray);
        return $accountObjects;
    }

    /**
     * Find by account id
     * @param $id
     * @return Account
     */
    public function findByIndriAccountID($id){
        $accountArray = $this->findByProperty($this->table, '*', 'indri_account_id', $id);
        return $this->createAccountObject($accountArray[0]);
    }

    /**
     * update account
     * @param $account
     */
    public function update($account){
        $item['indri_account_id'] = $account->getIndriAccountID();
        $item['account_name'] = $account->getAccountName();
        $item['account_group'] = $account->getAccountGroup();
        $item['customer_id'] = $account->getCustomerID();
        $item['external_account_id'] = $account->getExternalAccountID();
        $item['external_account_name'] = $account->getExternalAccountName();
        $this->updateSQL($this->table, $item, 'indri_account_id', $account->getIndriAccountID());
        return;
    }

    /**
     * Create account
     * @param $account
     */
    public function create($account){
        $fields = 'indri_account_id, account_name, account_group, customer_id, external_account_id, external_account_name';
        $values = "'" . $account->getIndriAccountID() . "','" . $account->getAccountName() . "','" .
            $account->getAccountGroup() . "','" . $account->getCustomerID() . "','" . $account->getExternalAccountID()
            . "','" . $account->getExternalAccountName() ."'";
        $this->insertSQL($this->table, $fields, $values);
        return;
    }

    /**
     * Create account object. As Doctrine doesn't work with Redshift because of keys we instantiate the object ourselves
     * @param $item
     * @return Account
     */
    public function createAccountObject($item){
        $account = new Account();
        if(isset($item['account_name'])) {
            $account->setAccountName($item['account_name']);
        }
        if(isset($item['indri_account_id'])){
            $account->setIndriAccountID($item['indri_account_id']);
        }
        if(isset($item['account_group'])) {
            $account->setAccountGroup($item['account_group']);
        }
        if(isset($item['customer_id'])) {
            $account->setCustomerID($item['customer_id']);
        }
        $account->setExternalAccountID($item['external_account_id']);
        $account->setExternalAccountName($item['external_account_name']);
        $account->setActive($item['active']);
        return $account;
    }
}
