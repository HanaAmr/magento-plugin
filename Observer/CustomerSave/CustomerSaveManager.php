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

            $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/CustomerSave.log');
            $logger = new \Zend\Log\Logger();
            $logger->addWriter($writer);
           
            $customerEmail = $this->customer->getEmail();
            $customerId = $this->customer->getId();
            $customerFirstName = $this->customer->getfirstname();
            $customerLastName = $this->customer->getlastname();
            $createdAt = date('Y-m-d', strtotime($this->customer->getCreatedAt()));
            $customerData = $this->customerModel->load($customerId);
            $adressesCollection =$customerData->getAddressesCollection();
            if($adressesCollection) {$telephone = $adressesCollection->getFirstitem()->getTelephone();}            $customerDisplayName = $customerFirstName . ' ' . $customerLastName;
            $gender = $this->getGenderChar($this->customer->getgender());

            $logger->info('3 ' . $customerEmail);
            $logger->info('4 ' . $customerId);
            $logger->info('5 ' . $customerFirstName);
            $logger->info('6 ' . $customerLastName);
            $logger->info('7 ' . $createdAt);
            $logger->info('8 ' . $telephone);
            $logger->info('9 ' . $customerDisplayName);
            $logger->info('10 ' . $gender);

            $logger->info('api key');
            $logger->info($this->clientKeys->getApiKey());


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

            $logger->info($res->body);

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
