<?php

namespace App\Model;

use App\Query\TokenHashQuery;
use ArrayHelpers\Arr;

class TokenHash extends BaseModel
{

    public ?int $id = null;

    public string $hash;

    /**
     * @throws \Exception
     */
    public function initNew(array $values)
    {
        $tokenHash = $this->fromArray($values);

        return $tokenHash->save();
    }

    public function fromArray(array $values): TokenHash
    {
        $tokenHash = new $this;
        if ($id = Arr::get($values, 'id')) {
            $tokenHash->id = $id;
        }
        $tokenHash->hash = Arr::get($values, 'hash');

        return $tokenHash;
    }

    public function toArray(): array
    {
        $array = [];

        if ($this->id) {
            $array['id'] = $this->id;
        }
        $array['hash'] = $this->hash;

        return $array;
    }

    /**
     * @throws \Exception
     */
    public function save()
    {
        if ($this->getQueryObject()->doesTokenHashExist($this->hash)) {
            $message = 'Token hash with hash `' . $this->hash . '` already exists.';
            if (is_local()) {
                echo $message . PHP_EOL;
            } else {
                throw new \Exception($message);
            }
        } else {
            return $this->getQueryObject()->createNewTokenHash($this->hash);
        }
    }

    public function update()
    {
        //
    }

    public function delete()
    {
        //
    }

    public function getQueryObject(): TokenHashQuery
    {
        return new TokenHashQuery();
    }
}
