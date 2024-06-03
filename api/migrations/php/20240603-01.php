<?php

class Migration
{
    public function run()
    {
        $hashes = (array) json_decode(file_get_contents(ROOT . '/data/token_hashes/hashlist.json'), true);

        foreach ($hashes as $hash) {
            $tokenHash = new \App\Model\TokenHash();
            $tokenHash->hash = $hash;
            $tokenHash->save();
        }
    }
}
