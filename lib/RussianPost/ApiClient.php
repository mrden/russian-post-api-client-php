<?php

namespace RussianPost;

use RussianPost\Http\Client;

class ApiClient
{
    protected $client;

    /**
     * ApiClient constructor.
     * @param $accessToken
     * @param $login
     * @param $password
     */
    public function __construct($accessToken, $login, $password)
    {
        $this->client = new Client($accessToken, $login, $password);
    }

    /**
     * @param $orders
     * @return bool|Response\ApiResponse|string
     */
    public function createOrders($orders)
    {
        if (is_null($orders) || !is_array($orders)) {
            throw new \InvalidArgumentException(
                'Parameter `orders` must contains a data'
            );
        }

        return $this->client->makeRequest('user/backlog', Client::METHOD_PUT, json_encode($orders));
    }

    /**
     * @param $query
     * @return bool|Response\ApiResponse|string
     */
    public function searchOrder($query)
    {
        if (is_null($query) || empty($query)) {
            throw new \InvalidArgumentException(
                'Parameter `query` must contains a data'
            );
        }

        return $this->client->makeRequest('backlog/search', Client::METHOD_GET, array('query' => $query));
    }

    /**
     * @param $id
     * @return bool|Response\ApiResponse|string
     */
    public function getOrder($id)
    {
        if (is_null($id) || empty($id)) {
            throw new \InvalidArgumentException(
                'Parameter `id` must contains a data'
            );
        }

        return $this->client->makeRequest(sprintf('backlog/%s', $id), Client::METHOD_GET);
    }

    /**
     * @param $id
     * @param $order
     * @return bool|Response\ApiResponse|string
     */
    public function updateOrder($id, $order)
    {
        if (is_null($id) || empty($id)) {
            throw new \InvalidArgumentException(
                'Parameter `id` must contains a data'
            );
        }

        if (is_null($order) || !is_array($order)) {
            throw new \InvalidArgumentException(
                'Parameter `order` must contains a data'
            );
        }

        return $this->client->makeRequest(sprintf('backlog/%s', $id), Client::METHOD_PUT, json_encode($order));
    }

    /**
     * @param $orderIds
     * @return bool|Response\ApiResponse|string
     */
    public function deleteOrder($orderIds)
    {
        if (is_null($orderIds) || !is_array($orderIds)) {
            throw new \InvalidArgumentException(
                'Parameter `orderIds` must contains a data'
            );
        }

        return $this->client->makeRequest('backlog', Client::METHOD_DELETE, json_encode($orderIds));
    }

    /**
     * @param $orderIds
     * @return bool|Response\ApiResponse|string
     */
    public function moveOrder($orderIds)
    {
        if (is_null($orderIds) || !is_array($orderIds)) {
            throw new \InvalidArgumentException(
                'Parameter `orderIds` must contains a data'
            );
        }

        return $this->client->makeRequest('user/backlog', Client::METHOD_POST, json_encode($orderIds));
    }

    /**
     * @param $orderIds
     * @param null|\DateTime $sendingDate
     * @return bool|Response\ApiResponse|string
     */
    public function createShipment($orderIds, $sendingDate = null)
    {
        if (is_null($orderIds) || !is_array($orderIds)) {
            throw new \InvalidArgumentException(
                'Parameter `orderIds` must contains a data'
            );
        }

        $params = '';

        if ($sendingDate instanceof \DateTime) {
            $params = http_build_query(array(
                'sending-date' => $sendingDate->format('Y-m-d')
            ));
        }

        return $this->client->makeRequest(
            sprintf('user/shipment%s', $params),
            Client::METHOD_POST,
            json_encode($orderIds)
        );
    }

    /**
     * @param $name
     * @param \DateTime $date
     * @return bool|Response\ApiResponse|string
     */
    public function changeShipmentDate($name, \DateTime $date)
    {
        if (is_null($name) || empty($name)) {
            throw new \InvalidArgumentException(
                'Parameter `name` must contains a data'
            );
        }

        return $this->client->makeRequest(sprintf(
            'batch/%s/sending/%s/%s/%s',
            $name,
            $date->format('Y'),
            $date->format('m'),
            $date->format('d')
        ), Client::METHOD_POST);
    }

    /**
     * @param $orderIds
     * @param $name
     * @return bool|Response\ApiResponse|string
     */
    public function moveShipment($orderIds, $name)
    {
        if (is_null($orderIds) || !is_array($orderIds)) {
            throw new \InvalidArgumentException(
                'Parameter `orderIds` must contains a data'
            );
        }

        if (is_null($name) || empty($name)) {
            throw new \InvalidArgumentException(
                'Parameter `name` must contains a data'
            );
        }

        return $this->client->makeRequest(sprintf('batch/%s/shipment', $name), Client::METHOD_POST,
            json_encode($orderIds));
    }

    /**
     * @param $name
     * @return bool|Response\ApiResponse|string
     */
    public function searchBatchByName($name)
    {
        if (is_null($name) || empty($name)) {
            throw new \InvalidArgumentException(
                'Parameter `name` must contains a data'
            );
        }

        return $this->client->makeRequest(sprintf('batch/%s', $name), Client::METHOD_GET);
    }

    /**
     * @param $query
     * @return bool|Response\ApiResponse|string
     */
    public function searchShipment($query)
    {
        if (is_null($query) || empty($query)) {
            throw new \InvalidArgumentException(
                'Parameter `query` must contains a data'
            );
        }

        return $this->client->makeRequest('shipment/search', Client::METHOD_GET, array('query' => $query));
    }

    /**
     * @param $name
     * @param $order
     * @return bool|Response\ApiResponse|string
     */
    public function addOrderToShipment($name, $order)
    {
        if (is_null($name) || empty($name)) {
            throw new \InvalidArgumentException(
                'Parameter `name` must contains a data'
            );
        }

        if (is_null($order) || !is_array($order)) {
            throw new \InvalidArgumentException(
                'Parameter `order` must contains a data'
            );
        }

        return $this->client->makeRequest(sprintf('batch/%s/shipment', $name), Client::METHOD_PUT, json_encode($order));
    }

    /**
     * @param $orderIds
     * @return bool|Response\ApiResponse|string
     */
    public function deleteOrderFromShipment($orderIds)
    {
        if (is_null($orderIds) || !is_array($orderIds)) {
            throw new \InvalidArgumentException(
                'Parameter `orderIds` must contains a data'
            );
        }

        return $this->client->makeRequest('shipment', Client::METHOD_DELETE, json_encode($orderIds));
    }

    /**
     * @param $name
     * @param null $size
     * @param null $sort
     * @param null $page
     * @return bool|Response\ApiResponse|string
     */
    public function getShipments($name, $size = null, $sort = null, $page = null)
    {
        if (is_null($name) || empty($name)) {
            throw new \InvalidArgumentException(
                'Parameter `name` must contains a data'
            );
        }

        $parameters = array();
        if (!is_null($size)) {
            $parameters['size'] = $size;
        }

        if (!is_null($sort)) {
            $parameters['sort'] = $sort;
        }

        if (!is_null($page)) {
            $parameters['page'] = $page;
        }

        return $this->client->makeRequest(sprintf('batch/%s/shipment', $name), Client::METHOD_GET, $parameters);
    }

    /**
     * @param null $mailType
     * @param null $size
     * @param null $sort
     * @param null $page
     * @return bool|Response\ApiResponse|string
     */
    public function searchBatches($mailType = null, $size = null, $sort = null, $page = null)
    {
        $parameters = array();
        if (!is_null($mailType)) {
            $parameters['mailType'] = $mailType;
        }

        if (!is_null($size)) {
            $parameters['size'] = $size;
        }

        if (!is_null($sort)) {
            $parameters['sort'] = $sort;
        }

        if (!is_null($page)) {
            $parameters['page'] = $page;
        }

        return $this->client->makeRequest('batch', Client::METHOD_GET, $parameters);
    }

    /**
     * @param $id
     * @return bool|Response\ApiResponse|string
     */
    public function searchShipmentById($id)
    {
        if (is_null($id) || empty($id)) {
            throw new \InvalidArgumentException(
                'Parameter `id` must contains a data'
            );
        }

        return $this->client->makeRequest(sprintf('shipment/%s', $id), Client::METHOD_GET);
    }

    /**
     * @param $name
     * @return bool|Response\ApiResponse|string
     */
    public function getForms($name)
    {
        if (is_null($name) || empty($name)) {
            throw new \InvalidArgumentException(
                'Parameter `name` must contains a data'
            );
        }

        return $this->client->makeRequest(sprintf('forms/%s/zip-all', $name), Client::METHOD_GET);
    }

    /**
     * @param $id
     * @return bool|Response\ApiResponse|string
     */
    public function getF7($id)
    {
        if (is_null($id) || empty($id)) {
            throw new \InvalidArgumentException(
                'Parameter `id` must contains a data'
            );
        }

        return $this->client->makeRequest(sprintf('forms/%s/f7pdf', $id), Client::METHOD_GET);
    }

    /**
     * @param $id
     * @return bool|Response\ApiResponse|string
     */
    public function getF112($id)
    {
        if (is_null($id) || empty($id)) {
            throw new \InvalidArgumentException(
                'Parameter `id` must contains a data'
            );
        }

        return $this->client->makeRequest(sprintf('forms/%s/f112pdf', $id), Client::METHOD_GET);
    }

    /**
     * @param $id
     * @param \DateTime $sendingDate
     * @return bool|Response\ApiResponse|string
     */
    public function getFormsByOrder($id, \DateTime $sendingDate)
    {
        if (is_null($id) || empty($id)) {
            throw new \InvalidArgumentException(
                'Parameter `id` must contains a data'
            );
        }

        return $this->client->makeRequest(sprintf('forms/%s/forms', $id), Client::METHOD_GET);
    }

    /**
     * @param $name
     * @return bool|Response\ApiResponse|string
     */
    public function getF103($name)
    {
        if (is_null($name) || empty($name)) {
            throw new \InvalidArgumentException(
                'Parameter `name` must contains a data'
            );
        }

        return $this->client->makeRequest(sprintf('forms/%s/f103pdf', $name), Client::METHOD_GET);
    }

    /**
     * @param $name
     * @param bool $sendEmail
     * @return bool|Response\ApiResponse|string
     */
    public function sendF103($name, $sendEmail = true)
    {
        if (is_null($name) || empty($name)) {
            throw new \InvalidArgumentException(
                'Parameter `name` must contains a data'
            );
        }

        return $this->client->makeRequest(
            sprintf('batch/%s/checkin?%s', $name,
                http_build_query(array('sendEmail' => $sendEmail ? 'true' : 'false'))),
            Client::METHOD_POST
        );
    }

    /**
     * @return bool|Response\ApiResponse|string
     */
    public function getShippingPoints()
    {
        return $this->client->makeRequest('user-shipping-points', Client::METHOD_GET);
    }

    /**
     * @return bool|Response\ApiResponse|string
     */
    public function getSettings()
    {
        return $this->client->makeRequest('settings', Client::METHOD_GET);
    }

    /**
     * @param $address
     * @return bool|Response\ApiResponse|string
     */
    public function getCleanAddress($address)
    {
        if (is_null($address) || !is_array($address)) {
            throw new \InvalidArgumentException(
                'Parameter `address` must contains a data'
            );
        }

        return $this->client->makeRequest('clean/address', Client::METHOD_POST, json_encode($address));
    }

    /**
     * @param $physical
     * @return bool|Response\ApiResponse|string
     */
    public function getCleanPhysical($physical)
    {
        if (is_null($physical) || !is_array($physical)) {
            throw new \InvalidArgumentException(
                'Parameter `physical` must contains a data'
            );
        }

        return $this->client->makeRequest('clean/physical', Client::METHOD_POST, json_encode($physical));
    }

    /**
     * @param $phone
     * @return bool|Response\ApiResponse|string
     */
    public function getCleanPhone($phone)
    {
        if (is_null($phone) || !is_array($phone)) {
            throw new \InvalidArgumentException(
                'Parameter `phone` must contains a data'
            );
        }

        return $this->client->makeRequest('clean/phone', Client::METHOD_POST, json_encode($phone));
    }

    /**
     * @param $calc
     * @return bool|Response\ApiResponse|string
     */
    public function getTariff($calc)
    {
        if (is_null($calc) || !is_array($calc)) {
            throw new \InvalidArgumentException(
                'Parameter `calc` must contains a data'
            );
        }

        return $this->client->makeRequest('tariff', Client::METHOD_POST, json_encode($calc));
    }
}