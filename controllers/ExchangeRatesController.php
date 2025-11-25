<?php
// controllers/ExchangeRatesController.php
require_once __DIR__ . '/../models/ExchangeRateModel.php';

class ExchangeRatesController
{
    private $model;

    public function __construct($pdo = null)
    {
        $this->model = new ExchangeRateModel($pdo);
    }

    /**
     * Devuelve datos necesarios para la vista (currencies, lastRates, rates)
     */
    public function getViewData(): array
    {
        $currencies = $this->model->getCurrencies();
        $lastRates  = $this->model->getLastRates();
        $rates      = $this->model->getRates(500);

        return [
            'currencies' => $currencies,
            'lastRates'  => $lastRates,
            'rates'      => $rates
        ];
    }
}
