<?php
namespace App\Query;

use App\Model\Wallet;
use App\Slack;
use ArrayHelpers\Arr;
use Carbon\Carbon;

class WalletQuery extends Query
{

    private string $table = 'wallets';

    public function createNewWallet(array $values): Wallet
    {
        foreach ($values as $key => $value) {
            if ($value instanceof Carbon) {
                $values[$key] = $value->format('Y-m-d H:i:s');
            }
        }

        $result = $this->db->insert($this->table, $values);
        if (!$result) {
            throw new \Exception('Wallet not created.');
        }

        return $this->getWalletByIdentifier(Arr::get($values, 'identifier'));
    }

    public function updateWalletByIdentifierAndAddress(string $identifier, string $address, array $values)
    {
        foreach ($values as $key => $value) {
            if ($value instanceof Carbon) {
                $values[$key] = $value->format('Y-m-d H:i:s');
            }
        }

        $result = $this->db
            ->where('identifier', $identifier)
            ->where('address', $address)
            ->update($this->table, $values);
        if (!$result) {
            $slack = new Slack();
            $slack->sendErrorMessage('Wallet for identifier `' . $identifier . '` not updated.');
        }

        return $this->getWalletByIdentifier(Arr::get($values, 'identifier'));
    }

    public function has(string $identifier): bool
    {
        return (bool) $this->db
            ->where('identifier', $identifier)
            ->get($this->table);
    }

    public function doesIdentifierAndWalletExist(string $identifier, string $address): bool
    {
        return (bool) $this->db
            ->where('identifier', $identifier)
            ->where('address', $address)
            ->get($this->table);
    }

    public function doesIdentifierExist(string $identifier): bool
    {
        return (bool) $this->db
            ->where('identifier', $identifier)
            ->getOne($this->table);
    }

    public function getWalletByIdentifier(string $identifier): ?Wallet
    {
        $results = $this->db
            ->where('identifier', $identifier)
            ->getOne($this->table);

        if ($results) {
            return (new Wallet())->fromArray($results);
        }

        return null;
    }

    public function getWalletByIdentifierAndAddress(string $identifier, string $address): ?Wallet
    {
        $results = $this->db
            ->where('identifier', $identifier)
            ->where('address', $address)
            ->getOne($this->table);

        if ($results) {
            return (new Wallet())->fromArray($results);
        }

        return null;
    }
}
