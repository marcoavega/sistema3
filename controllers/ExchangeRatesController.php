<?php
// controllers/ExchangeRatesController.php
require_once __DIR__ . '/../models/ExchangeRateModel.php';

class ExchangeRatesController {
    private $model;

    public function __construct($pdo = null) {
        $this->model = new ExchangeRateModel($pdo);
    }

    public function getViewData(): array {
        return [
            'currencies' => $this->model->getCurrencies(),
            'lastRates'  => $this->model->getLastRates(),
            'rates'      => $this->model->getRates(500)
        ];
    }
}