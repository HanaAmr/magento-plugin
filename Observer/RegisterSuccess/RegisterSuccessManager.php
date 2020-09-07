<?php
declare (strict_types = 1);

namespace GbPlugin\Integration\Observer\RegisterSuccess;

require_once BP . '/app/code/GbPlugin/vendor/autoload.php';

use Exception;

class RegisterSuccessManager
{

    private $customer;

    protected $_customerRepositoryInterface;
    protected $_coreSession;
    protected $request;
    protected $clientKeys;
    protected $customerModel;


    public function __construct(
        $customer, $customerRepositoryInterface, $coreSession, $request,$clientKeys,$customerModel
    ) {
        $this->customer = $customer;
        $this->_customerRepositoryInterface = $customerRepositoryInterface;
        $this->_coreSession = $coreSession;
        $this->request = $request;
        $this->clientKeys = $clientKeys;
        $this->customerModel = $customerModel;

    }

    public function execute()
    {
        try
        {
            
            //************************* Getting values *********************************

            $customerEmail = $this->customer->getEmail();
            $customerId = $this->customer->getId();
            $customerFirstName = $this->customer->getfirstname();
            $customerLastName = $this->customer->getlastname();
            $createdAt = date('Y-m-d', strtotime($this->customer->getCreatedAt()));
            $customerData =$this->customerModel->load($customerId);
            $adressesCollection =$customerData->getAddressesCollection();
            if($adressesCollection) {$telephone = $adressesCollection->getFirstitem()->getTelephone();}            $customerDisplayName = $customerFirstName . ' ' . $customerLastName;
            $gender = $this->getGenderChar($this->customer->getgender());

            //********************* Sending values to SDK ***********************

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

            //***************** Adding ReferralCode Attribute if exists******************
            $referral_code = $this->_coreSession->getGameballReferralCode();
            if ($referral_code != "") {
                $customerNew = $customerData->getDataModel();
                $customerNew->setCustomAttribute('referral_code', $referral_code);
                $this->_customerRepositoryInterface->save($customerNew);
                // $value =$this->_coreSession->unsGameballReferralCode();
                $playerCode = $referral_code;
                $res = $gameball->referral->createReferral($playerCode, $playerRequest);
                $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/testSuccess.log');
                $logger = new \Zend\Log\Logger();
                $logger->addWriter($writer);
                $logger->info($res->body);
            } else {
                $res = $gameball->player->initializePlayer($playerRequest);
                $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/testSuccess.log');
                $logger = new \Zend\Log\Logger();
                $logger->addWriter($writer);
                $logger->info($res->body);
            }

            $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/CustomerRegisterEvent.log');
            $logger = new \Zend\Log\Logger();

            $logger->addWriter($writer);
            $logger->info('Customer Data !');
            $logger->info('Customer Id');
            $logger->info($customerId);
            $logger->info('Customer Email');
            $logger->info($customerEmail);
            $logger->info('display name ');
            $logger->info($customerDisplayName);
            $logger->info('customerFisrtName ');
            $logger->info($customerFirstName);
            $logger->info('customerlastName ');
            $logger->info($customerLastName);
            $logger->info('gender ');
            $logger->info($gender);
            $logger->info('createdAt ');
            $logger->info($createdAt);
            $logger->info('telephone ');
            $logger->info($telephone);
            $logger->info('Referral_code ');
            $logger->info($referral_code);

            $logger->info('api key');
            $logger->info($this->clientKeys->getApiKey());


        } catch (Exception $e) {
        }
    }

    /**
     * @method printDataToLogFile
     * @param $gender
     * @return @genderChar
     */
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
