<?php

namespace easybill\SDK;

use easybill\SDK\Collection\CustomerGroups;
use easybill\SDK\Exception\AuthenticationFailed;
use easybill\SDK\Exception\CompanyPositionNotFound;
use easybill\SDK\Exception\CustomerContactNotFound;
use easybill\SDK\Exception\CustomerGroupNotFound;
use easybill\SDK\Exception\CustomerNotFound;
use easybill\SDK\Exception\ModelDataNotValid;
use easybill\SDK\Exception\ServerException;
use easybill\SDK\Model\CompanyPosition;
use easybill\SDK\Model\Customer;
use easybill\SDK\Model\CustomerContact;
use easybill\SDK\Model\CustomerGroup;

class Client
{
    private $soapClient;

    function __construct($wsdlPath, $apiKey)
    {
        ini_set('soap.wsdl_cache_enabled', '0');
        $this->soapClient = new \SoapClient($wsdlPath, array(
            'trace'      => 1,
            'exceptions' => 1,
            'classmap'   => array(
                'SearchCustomersType'               => '\easybill\SDK\Collection\Customers',
                'GetContactsByCustomerResponseType' => '\easybill\SDK\Collection\CustomerContacts',
                'AllCustomerGroupsResponseType'     => '\easybill\SDK\Collection\CustomerGroups',
                'SearchCompanyPositionsType'        => '\easybill\SDK\Collection\CompanyPositions',
                'customertype'                      => '\easybill\SDK\Model\Customer',
                'customerContactType'               => '\easybill\SDK\Model\CustomerContact',
                'customergrouptype'                 => '\easybill\SDK\Model\CustomerGroup',
                'companypositiontype'               => '\easybill\SDK\Model\CompanyPosition',
            )
        ));
        $header = new \SoapHeader('http://www.easybill.de/webservice', 'UserAuthKey', $apiKey);
        $this->soapClient->__setSoapHeaders($header);
    }

    /**
     * @param string $term
     *
     * @return \easybill\SDK\Collection\Customers
     * @throws \SoapFault
     * @throws \easybill\SDK\Exception\AuthenticationFailed
     * @throws \easybill\SDK\Exception\ServerException
     */
    public function searchCustomers($term)
    {
        try {
            return $this->soapClient->searchCustomers((string)$term);
        } catch (\SoapFault $soapFault) {
            $this->handleSoapFault($soapFault);
        }
    }

    /**
     * @param integer $customerID
     *
     * @return \easybill\SDK\Model\Customer|null
     * @throws \SoapFault
     * @throws \easybill\SDK\Exception\AuthenticationFailed
     * @throws \easybill\SDK\Exception\ServerException
     */
    public function findCustomer($customerID)
    {
        try {
            return $this->soapClient->getCustomer((int)$customerID);
        } catch (\SoapFault $soapFault) {
            $this->handleSoapFault($soapFault, array(101));
            return null;
        }
    }

    /**
     * @param string $customerNumber
     *
     * @return \easybill\SDK\Model\Customer|null
     * @throws \SoapFault
     * @throws \easybill\SDK\Exception\AuthenticationFailed
     * @throws \easybill\SDK\Exception\ServerException
     */
    public function findCustomerByCustomerNumber($customerNumber)
    {
        try {
            return $this->soapClient->getCustomerByCustomerNumber($customerNumber);
        } catch (\SoapFault $soapFault) {
            $this->handleSoapFault($soapFault, array(101));
            return null;
        }
    }

    /**
     * @param \easybill\SDK\Model\Customer $customer
     *
     * @return \easybill\SDK\Model\Customer
     * @throws \SoapFault
     * @throws \easybill\SDK\Exception\AuthenticationFailed
     * @throws \easybill\SDK\Exception\CustomerNotFound
     * @throws \easybill\SDK\Exception\ModelDataNotValid
     * @throws \easybill\SDK\Exception\ServerException
     */
    public function createCustomer(Customer $customer)
    {
        return $this->updateCustomer($customer);
    }

    /**
     * @param \easybill\SDK\Model\Customer $customer
     *
     * @return \easybill\SDK\Model\Customer
     * @throws \SoapFault
     * @throws \easybill\SDK\Exception\AuthenticationFailed
     * @throws \easybill\SDK\Exception\CustomerNotFound
     * @throws \easybill\SDK\Exception\ModelDataNotValid
     * @throws \easybill\SDK\Exception\ServerException
     */
    public function updateCustomer(Customer $customer)
    {
        try {
            return $this->soapClient->setCustomer($customer);
        } catch (\SoapFault $soapFault) {
            $this->handleSoapFault($soapFault);
        }
    }

    /**
     * @param integer $customerID
     *
     * @throws \SoapFault
     * @throws \easybill\SDK\Exception\AuthenticationFailed
     * @throws \easybill\SDK\Exception\CustomerNotFound
     * @throws \easybill\SDK\Exception\ServerException
     */
    public function deleteCustomer($customerID)
    {
        try {
            $this->soapClient->deleteCustomer($customerID);
        } catch (\SoapFault $soapFault) {
            $this->handleSoapFault($soapFault);
        }
    }

    /**
     * @return \easybill\SDK\Collection\CustomerContacts
     * @throws \SoapFault
     * @throws \easybill\SDK\Exception\AuthenticationFailed
     * @throws \easybill\SDK\Exception\ServerException
     */
    public function findCustomerContacts()
    {
        try {
            return $this->soapClient->getCustomerContacts();
        } catch (\SoapFault $soapFault) {
            $this->handleSoapFault($soapFault);
        }
    }

    /**
     * @param $customerID
     *
     * @return \easybill\SDK\Collection\CustomerContacts
     * @throws \SoapFault
     * @throws \easybill\SDK\Exception\AuthenticationFailed
     * @throws \easybill\SDK\Exception\CustomerNotFound
     * @throws \easybill\SDK\Exception\ServerException
     */
    public function findCustomerContactsByCustomer($customerID)
    {
        try {
            return $this->soapClient->getContactsByCustomer($customerID);
        } catch (\SoapFault $soapFault) {
            $this->handleSoapFault($soapFault);
        }
    }

    /**
     * @param $contactID
     *
     * @return \easybill\SDK\Model\Customer|null
     * @throws \SoapFault
     * @throws \easybill\SDK\Exception\AuthenticationFailed
     * @throws \easybill\SDK\Exception\CustomerContactNotFound
     * @throws \easybill\SDK\Exception\ServerException
     */
    public function findCustomerContact($contactID)
    {
        try {
            return $this->soapClient->getCustomerContact($contactID);
        } catch (\SoapFault $soapFault) {
            $this->handleSoapFault($soapFault, array(104));
            return null;
        }
    }

    /**
     * @param \easybill\SDK\Model\CustomerContact $contact
     *
     * @return \easybill\SDK\Model\CustomerContact
     * @throws \SoapFault
     * @throws \easybill\SDK\Exception\AuthenticationFailed
     * @throws \easybill\SDK\Exception\CustomerNotFound
     * @throws \easybill\SDK\Exception\ModelDataNotValid
     * @throws \easybill\SDK\Exception\ServerException
     */
    public function createCustomerContact(CustomerContact $contact)
    {
        return $this->updateCustomerContact($contact);
    }

    /**
     * @param \easybill\SDK\Model\CustomerContact $contact
     *
     * @return CustomerContact
     * @throws \SoapFault
     * @throws \easybill\SDK\Exception\AuthenticationFailed
     * @throws \easybill\SDK\Exception\CustomerNotFound
     * @throws \easybill\SDK\Exception\ModelDataNotValid
     * @throws \easybill\SDK\Exception\ServerException
     */
    public function updateCustomerContact(CustomerContact $contact)
    {
        try {
            return $this->soapClient->setCustomerContact($contact);
        } catch (\SoapFault $soapFault) {
            $this->handleSoapFault($soapFault);
        }
    }

    /**
     * @param integer $contactID
     *
     * @throws \SoapFault
     * @throws \easybill\SDK\Exception\AuthenticationFailed
     * @throws \easybill\SDK\Exception\CustomerContactNotFound
     * @throws \easybill\SDK\Exception\ServerException
     */
    public function deleteCustomerContact($contactID)
    {
        try {
            $this->soapClient->deleteCustomerContact((int)$contactID);
        } catch (\SoapFault $soapFault) {
            $this->handleSoapFault($soapFault);
        }
    }

    /**
     * @return CustomerGroups
     * @throws \SoapFault
     * @throws \easybill\SDK\Exception\AuthenticationFailed
     * @throws \easybill\SDK\Exception\ServerException
     */
    public function findCustomerGroups()
    {
        try {
            return $this->soapClient->getAllCustomerGroups();
        } catch (\SoapFault $soapFault) {
            $this->handleSoapFault($soapFault);
        }
    }

    /**
     * @param integer $groupID
     *
     * @return CustomerGroup
     * @throws \SoapFault
     * @throws \easybill\SDK\Exception\AuthenticationFailed
     * @throws \easybill\SDK\Exception\ServerException
     */
    public function findCustomerGroup($groupID)
    {
        try {
            return $this->soapClient->getCustomerGroup((int)$groupID);
        } catch (\SoapFault $soapFault) {
            $this->handleSoapFault($soapFault, array(102));
        }
    }

    /**
     * @param \easybill\SDK\Model\CustomerGroup $group
     *
     * @return \easybill\SDK\Model\CustomerGroup
     * @throws \SoapFault
     * @throws \easybill\SDK\Exception\AuthenticationFailed
     * @throws \easybill\SDK\Exception\ModelDataNotValid
     * @throws \easybill\SDK\Exception\ServerException
     */
    public function createCustomerGroup(CustomerGroup $group)
    {
        $this->updateCustomerGroup($group);
    }

    /**
     * @param \easybill\SDK\Model\CustomerGroup $group
     *
     * @return \easybill\SDK\Model\CustomerGroup
     * @throws \SoapFault
     * @throws \easybill\SDK\Exception\AuthenticationFailed
     * @throws \easybill\SDK\Exception\ModelDataNotValid
     * @throws \easybill\SDK\Exception\ServerException
     */
    public function updateCustomerGroup(CustomerGroup $group)
    {
        try {
            return $this->soapClient->setCustomerGroup($group);
        } catch (\SoapFault $soapFault) {
            $this->handleSoapFault($soapFault);
        }
    }

    /**
     * @param integer $groupID
     *
     * @return void
     * @throws \SoapFault
     * @throws \easybill\SDK\Exception\AuthenticationFailed
     * @throws \easybill\SDK\Exception\ServerException
     */
    public function deleteCustomerGroup($groupID)
    {
        try {
            $this->soapClient->deleteCustomerGroup((int)$groupID);
        } catch (\SoapFault $soapFault) {
            $this->handleSoapFault($soapFault);
        }
    }

    /**
     * @param string $term
     *
     * @return \easybill\SDK\Collection\CompanyPositions
     * @throws \SoapFault
     * @throws \easybill\SDK\Exception\AuthenticationFailed
     * @throws \easybill\SDK\Exception\ServerException
     */
    public function searchCompanyPositions($term)
    {
        try {
            return $this->soapClient->searchCompanyPositions((string)$term);
        } catch (\SoapFault $soapFault) {
            $this->handleSoapFault($soapFault);
        }
    }

    /**
     * @param int $positionID
     *
     * @return CompanyPosition|null
     * @throws \SoapFault
     * @throws \easybill\SDK\Exception\AuthenticationFailed
     * @throws \easybill\SDK\Exception\CompanyPositionNotFound
     * @throws \easybill\SDK\Exception\ServerException
     */
    public function findCompanyPosition($positionID)
    {
        try {
            return $this->soapClient->getCompanyPosition((int)$positionID);
        } catch (\SoapFault $soapFault) {
            $this->handleSoapFault($soapFault, array(111));
            return null;
        }
    }

    /**
     * @param \easybill\SDK\Model\CompanyPosition $position
     *
     * @return CompanyPosition
     * @throws \SoapFault
     * @throws \easybill\SDK\Exception\AuthenticationFailed
     * @throws \easybill\SDK\Exception\CompanyPositionNotFound
     * @throws \easybill\SDK\Exception\ModelDataNotValid
     * @throws \easybill\SDK\Exception\ServerException
     */
    public function createCompanyPosition(CompanyPosition $position)
    {
        return $this->updateCompanyPosition($position);
    }

    /**
     * @param \easybill\SDK\Model\CompanyPosition $position
     *
     * @return CompanyPosition
     * @throws \SoapFault
     * @throws \easybill\SDK\Exception\AuthenticationFailed
     * @throws \easybill\SDK\Exception\CompanyPositionNotFound
     * @throws \easybill\SDK\Exception\ModelDataNotValid
     * @throws \easybill\SDK\Exception\ServerException
     */
    public function updateCompanyPosition(CompanyPosition $position)
    {
        try {
            return $this->soapClient->setCompanyPosition($position);
        } catch (\SoapFault $soapFault) {
            $this->handleSoapFault($soapFault);
        }
    }

    /**
     * @param \SoapFault $soapFault
     * @param array      $ignoreCode
     *
     * @throws \SoapFault
     * @throws \easybill\SDK\Exception\AuthenticationFailed
     * @throws \easybill\SDK\Exception\CustomerNotFound
     * @throws \easybill\SDK\Exception\ModelDataNotValid
     * @throws \easybill\SDK\Exception\ServerException
     * @throws \easybill\SDK\Exception\CustomerContactNotFound
     * @throws \easybill\SDK\Exception\CustomerGroupNotFound
     * @throws \easybill\SDK\Exception\CompanyPositionNotFound
     */
    private function handleSoapFault(\SoapFault $soapFault, $ignoreCode = array())
    {
        if (property_exists($soapFault, 'faultcode')) {

            if (in_array((int)$soapFault->faultcode, $ignoreCode)) {
                return;
            }

            switch ((int)$soapFault->faultcode) {
                case 1:
                    throw new ServerException($soapFault->getMessage(), 1, $soapFault);
                case 2:
                    throw new AuthenticationFailed($soapFault->getMessage(), 2, $soapFault);
                case 3:
                    $message = $soapFault->getMessage();
                    if (
                        property_exists($soapFault, 'detail')
                        && is_object($soapFault->detail)
                        && property_exists($soapFault->detail, 'DataNotValidFault')
                        && is_object($soapFault->detail->DataNotValidFault)
                        && property_exists($soapFault->detail->DataNotValidFault, 'datafield')
                    ) {
                        $message = $soapFault->detail->DataNotValidFault->datafield;
                    }
                    throw new ModelDataNotValid($message, 3, $soapFault);
                case 101:
                    throw new CustomerNotFound($soapFault->getMessage(), 101, $soapFault);
                case 102:
                    throw new CustomerGroupNotFound($soapFault->getMessage(), 102, $soapFault);
                case 104:
                    throw new CustomerContactNotFound($soapFault->getMessage(), 104, $soapFault);
                case 111:
                    throw new CompanyPositionNotFound($soapFault->getMessage(), 111, $soapFault);
            }
        }
        throw $soapFault;
    }

    /**
     * @return \SoapClient
     */
    public function getSoapClient()
    {
        return $this->soapClient;
    }
}