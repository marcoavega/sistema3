<?php
require_once __DIR__ . '/Database.php';

class ExchangeRateModel {
    private $pdo;

    public function __construct(?PDO $pdo = null) {
        $this->pdo = $pdo ?? (new Database())->getConnection();
    }

    /* --- MONEDAS --- */

    public function getCurrencies(): array {
        $stmt = $this->pdo->query("SELECT * FROM currencies ORDER BY currency_code ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCurrency(int $id) {
        $stmt = $this->pdo->prepare("SELECT * FROM currencies WHERE currency_id = ? LIMIT 1");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function createCurrency(string $code, string $name, string $country): int {
        $stmt = $this->pdo->prepare("INSERT INTO currencies (currency_code, currency_name, country) VALUES (?, ?, ?)");
        $stmt->execute([strtoupper($code), $name, $country]);
        return (int)$this->pdo->lastInsertId();
    }

    public function updateCurrency(int $id, string $code, string $name): bool {
        $stmt = $this->pdo->prepare("UPDATE currencies SET currency_code = ?, currency_name = ? WHERE currency_id = ?");
        return $stmt->execute([strtoupper($code), $name, $id]);
    }

    public function deleteCurrency(int $id): bool {
        // 1. VALIDACIÓN PREVIA: Verificar si tiene tasas asociadas
        $check = $this->pdo->prepare("SELECT COUNT(*) FROM exchange_rates WHERE currency_id = ?");
        $check->execute([$id]);
        if ($check->fetchColumn() > 0) {
            // Si hay registros, devolvemos false para impedir el borrado
            return false;
        }

        // 2. Si no hay historial, procedemos a borrar
        $stmt = $this->pdo->prepare("DELETE FROM currencies WHERE currency_id = ?");
        return $stmt->execute([$id]);
    }

    /* --- TIPOS DE CAMBIO --- */

    // Modificado para aceptar filtros de fecha
    public function getRates(int $limit = 500, ?string $start = null, ?string $end = null): array {
        $sql = "SELECT er.rate_id, er.currency_id, er.rate, er.rate_date, er.notes, c.currency_code 
                FROM exchange_rates er
                JOIN currencies c ON c.currency_id = er.currency_id
                WHERE 1=1";
        
        $params = [];

        if (!empty($start)) {
            $sql .= " AND er.rate_date >= ?";
            $params[] = $start;
        }

        if (!empty($end)) {
            $sql .= " AND er.rate_date <= ?";
            $params[] = $end;
        }

        $sql .= " ORDER BY er.rate_date DESC, er.created_at DESC LIMIT " . (int)$limit;

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getLastRates(): array {
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

    public function createRate(int $currency_id, $rate, string $rate_date, ?string $notes = null): int {
        $stmt = $this->pdo->prepare("INSERT INTO exchange_rates (currency_id, rate, rate_date, notes) VALUES (?, ?, ?, ?)");
        $stmt->execute([$currency_id, $rate, $rate_date, $notes]);
        return (int)$this->pdo->lastInsertId();
    }

    public function updateRate(int $id, float $rate, string $date, string $notes): bool {
        $stmt = $this->pdo->prepare("UPDATE exchange_rates SET rate = ?, rate_date = ?, notes = ? WHERE rate_id = ?");
        return $stmt->execute([$rate, $date, $notes, $id]);
    }

    public function deleteRate(int $id): bool {
        $stmt = $this->pdo->prepare("DELETE FROM exchange_rates WHERE rate_id = ?");
        return $stmt->execute([$id]);
    }
}