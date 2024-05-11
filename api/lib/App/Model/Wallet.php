<?php

namespace App\Model;

use App\Query\WalletQuery;
use App\Slack;
use ArrayHelpers\Arr;
use Carbon\Carbon;

class Wallet extends BaseModel
{

    public ?int $id = null;

    public string $identifier;

    public string $address;

    public ?float $balance = null;

    public ?Carbon $createdAt = null;

    public ?Carbon $updatedAt = null;

    public function initNew(array $values)
    {
        $wallet = $this->fromArray($values);

        return $wallet->save();
    }

    public function fromArray(array $values): Wallet
    {
        $wallet = new $this;
        if ($id = Arr::get($values, 'id')) {
            $wallet->id = $id;
        }
        $wallet->identifier = Arr::get($values, 'identifier');
        $wallet->address = Arr::get($values, 'address');
        if ($balance = Arr::get($values, 'balance')) {
            $wallet->balance = $balance;
        }
        if ($createdAt = Arr::get($values, 'created_at')) {
            $wallet->createdAt = Carbon::parse($createdAt);
        }
        if ($updatedAt = Arr::get($values, 'updated_at')) {
            $wallet->updatedAt = Carbon::parse($updatedAt);
        }

        return $wallet;
    }

    public function toArray(): array
    {
        $array = [];

        if ($this->id) {
            $array['id'] = $this->id;
        }
        $array['identifier'] = $this->identifier;
        $array['address'] = $this->address;
        if (is_numeric($this->balance)) {
            $array['balance'] = $this->balance;
        } else {
            $array['balance'] = null;
        }
        if ($this->createdAt) {
            $array['created_at'] = $this->createdAt;
        }
        if ($this->updatedAt) {
            $array['updated_at'] = $this->updatedAt;
        }

        return $array;
    }

    /**
     * @throws \Exception
     */
    public function save()
    {
        if ($this->getQueryObject()->doesIdentifierAndWalletExist($this->identifier, $this->address)) {
            return $this->update();
        } else {

            $values = $this->toArray();
            $values['created_at'] = Carbon::now();
            $values['updated_at'] = Carbon::now();

            return $this->getQueryObject()->createNewWallet($values);
        }
    }

    /**
     * @throws \Exception
     */
    public function update()
    {
        $values = $this->toArray();
        $values['updated_at'] = Carbon::now();
        unset($values['id']);

        return $this->getQueryObject()->updateWalletByIdentifierAndAddress($this->identifier, $this->address, $values);
    }

    public function delete()
    {
        // TODO: Implement delete() method.
    }

    public function getQueryObject()
    {
        return new WalletQuery();
    }
}
