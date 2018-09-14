<?php
/**
 * @author Juriy Panasevich <juriy.panasevich@gmail.com>
 */

namespace common\delivery;


use common\exception\DeliveryException;
use common\models\Prize;
use common\models\prize\Money;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;

class Bank extends Delivery
{
    public $url;

    protected $client;

    /**
     * Account constructor.
     * @param array $config
     * @throws \InvalidArgumentException
     */
    public function __construct($config = [])
    {
        parent::__construct($config);

        $guzzle = [
            'base_uri' => $this->url,
            RequestOptions::TIMEOUT => 60,
            RequestOptions::VERIFY => false,
            RequestOptions::HTTP_ERRORS => false,
        ];
        $this->client = new Client($guzzle);
    }


    /**
     * @param Prize|Money $prize
     * @return bool
     */
    public function deliver(Prize $prize): bool
    {
        try {
            $requestData = [
                'amount' => $prize->amount,
            ];

            $response = $this->client->post('paid/' . $prize->user_id, [
                RequestOptions::JSON => $requestData,
            ]);
            $contents = $response->getBody()->getContents();

            if ($response->getStatusCode() !== 200) {
                throw new DeliveryException($contents);
            }

            if (!$contents = json_decode($contents, true)) {
                throw new DeliveryException('Invalid response: ' . $response->getBody()->getContents());
            }

            if (!array_key_exists('status', $contents)) {
                throw new DeliveryException('Invalid response');
            }

            if (false === strpos($contents['status'], 'ok')) {
                throw new DeliveryException($contents['error'] ?? 'Operation failed');
            }

        } catch (DeliveryException $e) {
            \Yii::error($e->getMessage());

            return false;
        }
        return true;
    }

    public function description(): string
    {
        return 'Bank account';
    }
}
