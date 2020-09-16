<?php

namespace GbPlugin\Integration\Observer\CustomerSave;

require_once BP . '/vendor/autoload.php';

use Exception;

class CustomerSaveManager
{
    private $customer;
    protected $clientKeys;
    protected $customerModel;

    public function __construct(
    \GbPlugin\Integration\Observer\Shared\ClientkeysTable $clientKeys,
    \Magento\Customer\Model\Customer $customerModel)
    {
        $this->clientKeys = $clientKeys;
        $this->customerModel = $customerModel;
    }

    public function execute($customer)
    {
        try {
            $this->customer = $customer;
           
            $customerEmail = $this->customer->getEmail();
            $customerId = $this->customer->getId();
            $customerFirstName = $this->customer->getfirstname();
            $customerLastName = $this->customer->getlastname();
            $createdAt = date('Y-m-d', strtotime($this->customer->getCreatedAt()));
            $customerData = $this->customerModel->load($customerId);
            $adressesCollection =$customerData->getAddressesCollection();
            if($adressesCollection) {$telephone = $adressesCollection->getFirstitem()->getTelephone();}            $customerDisplayName = $customerFirstName . ' ' . $customerLastName;
            $gender = $this->getGenderChar($this->customer->getgender());



            $gameball = new \Gameball\GameballClient($this->clientKeys->getApiKey(), $this->clientKeys->getTransactionKey());
            $playerAttributes = new \Gameball\Models\PlayerAttributes();
            if ($customerDisplayName != "") {$playerAttributes->displayName = $customerDisplayName;}
            if ($customerFirstName != "") {$playerAttributes->firstName = $customerFirstName;}
            if ($customerLastName != "") {$playerAttributes->lastName = $customerLastName;}
            if ($customerEmail != "") {$playerAttributes->email = $customerEmail;}
            if ($gender != "") {$playerAttributes->gender = $gender;}
            if ($telephone != "") {$playerAttributes->mobileNumber = $telephone;}
            if ($createdAt != "") {$playerAttributes->joinDate = $createdAt;}
            if ($customerId != "") {
                $playerUniqueID = $customerId;
            }

            $playerRequest = \Gameball\Models\PlayerRequest::factory($playerUniqueID, $playerAttributes);
            $res = $gameball->player->initializePlayer($playerRequest);

        } catch (Exception $e) {
        }
    }

    private function getGenderChar($gender)
    {
        if ($gender == 1) {
            return "M";
        } else if ($gender == 2) {
            return "F";
        }
        return "";
    }
}
