<?php

namespace AppBundle\Repository\Reporting;


/**
 * PackageRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PackageRepository extends BaseRepository
{
    public $table = "xp_publisher_data";

    public $nameField = "indri_account_id";

    /**
     * Finds all packages
     * @return mixed
     */
    public function findAllPackages(){
        $packages = $this->customQuery('select * from publisher_data');
        return $packages;
    }

    /**
     * Find by IndriAccountID
     * @param $id
     * @return mixed
     */
    public function findByIndriAccountID($id){
        $packages = $this->findByProperty($this->table, '*', 'indri_account_id', $id,""," ASC",99);
        return $packages;
    }

    /**
     * Find by publisher
     * @param $publisher
     * @return mixed
     */
    public function findByPublisher($publisher){
        $packages = $this->findByProperty($this->table, '*', 'publisher_name', $publisher,""," ASC",1000);
        return $packages;
    }

    /**
     * Find by master template
     * @param $id
     * @return mixed
     */
    public function findByMasterTemplate($id){
        $packages = $this->findByProperty($this->table, '*', 'xplenty_master_template_id', $id,""," ASC",1000);
        return $packages;
    }

    /**
     * Find publisher and Indri Account ID
     * @param $publisher
     * @param $indriAccountID
     * @return Campaign
     */
    public function findByPublisherAndIndriAccountID($publisher, $indriAccountID){
        $properties = array(0 => 'publisher_name', 1 => 'indri_account_id');
        $values[0] = $publisher;
        $values[1] = $indriAccountID;
        $packages = $this->findByMultipleProperties($this->table, '*', $properties, $values,'',' ASC',99);
        return $packages;
    }

    /**
     * Update a package
     * @param $packageData
     */
    public function update($packageData){
        $this->updateSQL($this->table, $packageData, 'xplenty_package_id', $packageData['xplenty_package_id']);
        return;
    }

    /**
     * Creates a package
     * @param $package
     */
    public function create($package){
        $fields = 'indri_account_id, publisher_name, publisher_account_info, xplenty_package_id, xplenty_connection_id, 
        xplenty_package_data, xplenty_connection_data, disabled, xplenty_master_template_id';
        $values = "'" . $package['indri_account_id'] . "','" . $package['publisher_name'] . "','" .
            $package['publisher_account_info'] . "','" . $package['xplenty_package_id'] . "','" . $package['xplenty_connection_id']
            . "','" . $package['xplenty_package_data']  . "','" . $package['xplenty_connection_data']
            . "',0," . $package['xplenty_master_template_id'];
        $this->insertSQL($this->table, $fields, $values);
        return;
    }


}