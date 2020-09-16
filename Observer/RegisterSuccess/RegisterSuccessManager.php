<?php
declare (strict_types = 1);

namespace GbPlugin\Integration\Observer\RegisterSuccess;

require_once BP . '/vendor/autoload.php';

use Exception;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Session\SessionManagerInterface;

class RegisterSuccessManager
{

    private $customer;

    protected $_customerRepositoryInterface;
    protected $_coreSession;
    protected $request;
    protected $clientKeys;
    protected $customerModel;


    public function __construct(
    CustomerRepositoryInterface $customerRepositoryInterface, 
    SessionManagerInterface $coreSession, 
    Http $request,
    \GbPlugin\Integration\Observer\Shared\ClientkeysTable $clientKeys,
    \Magento\Customer\Model\Customer $customerModel
    ) {
        $this->_customerRepositoryInterface = $customerRepositoryInterface;
        $this->_coreSession = $coreSession;
        $this->request = $request;
        $this->clientKeys = $clientKeys;
        $this->customerModel = $customerModel;
    }

    public function execute($customer)
    {
        try
        {
            $this->customer = $customer;
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
                $playerCode = $referral_code;
                $res = $gameball->referral->createReferral($playerCode, $playerRequest);
     
            } else {
                $res = $gameball->player->initializePlayer($playerRequest);
      
            }

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
