<?php

namespace Source\Support;

class Payment
{
    private $apiUrl;
    private $apiKey;
    private $endPoint;
    private $build;
    private $callback;

    public function __construct()
    {
        $this->apiUrl = "https://sandbox.asaas.com/api/v3";
        // $this->apiUrl = "https://www.asaas.com/api/v3";
        $this->apiKey = ASAAS_API_KEY;
    }

    public function createClient($data)
    {
        $this->endPoint = "/customers";
        $this->build = [
            "name" => $data['desperson'],
            "email" => $data['desemail'],
            "mobilePhone" => $data['nrphone'],
            "address" => $data['desaddress'],
            "addressNumber" => $data['desnumber'],
            "complement" => $data['descomplement'],
            "province" => $data['desdistrict'],
            "postalCode" => $data['deszipcode'],
            "cpfCnpj" => $data['descpf'],
            "personType" => "FISICA",
            "city" => $data['descity'],
            "state" => $data['desstate'],
            "country" => $data['descountry'],
            "observations" => "Cliente do portal."
        ];

        $this->post();
        return $this;
    }
    public function listPayments()
    {
        $this->endPoint = "/payments";
        $this->get();
        return $this;
    }
    public function listPaymentsWithBoleto()
    {
        $this->endPoint = "/payments?billingType=BOLETO";
        $this->get();
        return $this;
    }
    public function listPaymentsWithCard()
    {
        $this->endPoint = "/payments?billingType=CREDIT_CARD";
        $this->get();
        return $this;
    }
    public function withBoleto($customer): Payment
    {

        $this->endPoint = "/payments";
        $this->build = [
            "customer" => $customer['customer'],
            "billingType" => $customer['billingType'],
            "dueDate" => $customer['dueDate'],
            "value" => $customer['value'],
            "description" => $customer['description'],
            "externalReference" => $customer['externalReference'],
            "postalService" => $customer['postalService']
        ];

        // var_export($this->build);exit;
        $this->post();
        return $this;
    }
    public function withCard($customer, $creditCard, $creditCardHolderInfo): Payment
    {
        $this->endPoint = "/payments";
        $this->build = [
            "customer" => $customer['customer'],
            "billingType" => $customer['billingType'],
            "dueDate" => $customer['dueDate'],
            "value" => $customer['value'],
            "description" => $customer['description'],
            "externalReference" => $customer['externalReference'],
            //
            "creditCard" => $creditCard,
            "creditCardHolderInfo" => $creditCardHolderInfo
        ];
        // echo '<pre>';var_export($this->build);exit;
        $this->post();
        return $this;
    }

    public function callback()
    {
        return $this->callback;
    }
    public function post()
    {
        $url = $this->apiUrl . $this->endPoint;
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($this->build));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Content-Type: application/json",
            "access_token: $this->apiKey"
        ));

        $this->callback = json_decode(curl_exec($ch));
        // echo '<pre>';var_export($this->callback);exit;
        curl_close($ch);
    }

    public function get()
    {
        $url = $this->apiUrl . $this->endPoint;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);

        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "access_token: $this->apiKey"
        ));

        $this->callback = json_decode(curl_exec($ch));
        curl_close($ch);
    }
 
}
