<?php
namespace App\Query;

use App\Model\TokenHash;

class TokenHashQuery extends Query
{

    private string $table = 'token_hashes';

    /**
     * @throws \Exception
     */
    public function createNewTokenHash(string $hash): TokenHash
    {
        $result = $this->db->insert($this->table, [
            'hash' => $hash,
        ]);

        if (!$result) {
            throw new \Exception('Token hash not created.');
        }

        return $this->getTokenHashByHash($hash);
    }

    public function doesTokenHashExist(string $hash): bool
    {
        return (bool) $this->db
            ->where('hash', $hash)
            ->get($this->table);
    }

    public function getTokenHashByHash(string $hash): ?TokenHash
    {
        $results = $this->db
            ->where('hash', $hash)
            ->getOne($this->table);

        if ($results) {
            return (new TokenHash())->fromArray($results);
        }

        return null;
    }
}
