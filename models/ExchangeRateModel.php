<?php
// models/ExchangeRateModel.php
require_once __DIR__ . '/Database.php';

class ExchangeRateModel
{
    private $pdo;

    public function __construct(?PDO $pdo = null)
    {
        if ($pdo instanceof PDO) {
            $this->pdo = $pdo;
        } else {
            $this->pdo = (new Database())->getConnection();
        }
    }

    /* MONEDAS */
    public function getCurrencies(): array
    {
        $stmt = $this->pdo->query("SELECT currency_id, currency_code, currency_name, country FROM currencies ORDER BY currency_code");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCurrency(int $id)
    {
        $stmt = $this->pdo->prepare("SELECT currency_id, currency_code, currency_name, country FROM currencies WHERE currency_id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function createCurrency(string $code, string $name, string $country): int
    {
        $stmt = $this->pdo->prepare("INSERT INTO currencies (currency_code, currency_name, country) VALUES (:code, :name, :country)");
        $stmt->execute([':code' => $code, ':name' => $name, ':country' => $country]);
        return (int)$this->pdo->lastInsertId();
    }

    public function deleteCurrency(int $id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM currencies WHERE currency_id = :id");
        return $stmt->execute([':id' => $id]);
    }

    /* TIPOS DE CAMBIO */
    public function getRates(int $limit = 500): array
    {
        $stmt = $this->pdo->prepare("
            SELECT er.rate_id, er.currency_id, er.rate, er.rate_date, er.notes, er.created_at, c.currency_code, c.currency_name
            FROM exchange_rates er
            JOIN currencies c ON c.currency_id = er.currency_id
            ORDER BY er.rate_date DESC, er.created_at DESC
            LIMIT :limit
        ");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getLastRates(): array
    {
        $stmt = $this->pdo->prepare("
            SELECT er.currency_id, er.rate, er.rate_date, er.created_at
            FROM exchange_rates er
            JOIN (
              SELECT currency_id, MAX(created_at) AS max_created
              FROM exchange_rates
              GROUP BY currency_id
            ) grouped ON grouped.currency_id = er.currency_id AND grouped.max_created = er.created_at
        ");
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $out = [];
        foreach ($rows as $r) $out[$r['currency_id']] = $r;
        return $out;
    }

    public function createRate(int $currency_id, $rate, string $rate_date, ?string $notes = null): int
    {
        $stmt = $this->pdo->prepare("INSERT INTO exchange_rates (currency_id, rate, rate_date, notes, created_at) VALUES (:cid, :rate, :rdate, :notes, NOW())");
        $stmt->execute([
            ':cid' => $currency_id,
            ':rate' => $rate,
            ':rdate' => $rate_date,
            ':notes' => $notes
        ]);
        return (int)$this->pdo->lastInsertId();
    }

    public function deleteRate(int $id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM exchange_rates WHERE rate_id = :id");
        return $stmt->execute([':id' => $id]);
    }
}
