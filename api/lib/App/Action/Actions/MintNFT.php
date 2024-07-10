<?php

namespace App\Action\Actions;

use App\Action\BaseAction;
use App\Slack;
use Attestto\SolanaPhpSdk\Borsh\Borsh;
use Attestto\SolanaPhpSdk\Borsh\BorshObject;
use Attestto\SolanaPhpSdk\Connection;
use Attestto\SolanaPhpSdk\Exceptions\GenericException;
use Attestto\SolanaPhpSdk\Exceptions\InputValidationException;
use Attestto\SolanaPhpSdk\Exceptions\TokenOwnerOffCurveError;
use Attestto\SolanaPhpSdk\Keypair;
use Attestto\SolanaPhpSdk\Programs\SplToken\State\Mint;
use Attestto\SolanaPhpSdk\Programs\SplTokenProgram;
use Attestto\SolanaPhpSdk\Programs\SystemProgram;
use Attestto\SolanaPhpSdk\PublicKey;
use Attestto\SolanaPhpSdk\SolanaRpcClient;
use Attestto\SolanaPhpSdk\Transaction;
use Attestto\SolanaPhpSdk\TransactionInstruction;
use Attestto\SolanaPhpSdk\Util\AccountMeta;
use Attestto\SolanaPhpSdk\Util\Commitment;
use phpseclib3\Math\BigInteger;
use Psr\Http\Client\ClientExceptionInterface;
use Random\RandomException;
use Tatum\Model\NftTokenByCollectionErc721;
use Tatum\Model\NftTokenByCollectionErc721TokenMetadata;
use Tatum\Model\SolanaNftMetadata;

class MintNFT extends BaseAction
{
    private SolanaRpcClient $solana;

    use BorshObject;

    public function __construct()
    {
        $rpcUrl = 'https://mainnet.helius-rpc.com/?api-key=' . env('HELIUS_API_KEY');
        $rpcUrl = 'https://devnet.helius-rpc.com/?api-key=' . env('HELIUS_API_KEY');
        $this->solana = new SolanaRpcClient($rpcUrl);
    }

    public function base58_decode($base58)
    {
        $alphabet = '123456789ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz';
        $base = new BigInteger(58);
        $decoded = new BigInteger(0);
        $length = strlen($base58);

        for ($i = 0; $i < $length; $i++) {
            $decoded = $decoded->multiply($base);
            $decoded = $decoded->add(new BigInteger(strpos($alphabet, $base58[$i])));
        }

        return $decoded->toBytes();
    }

    public function base58_encode($data) {
        $alphabet = '123456789ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz';
        $base = new BigInteger(58);
        $encoded = '';

        $data = new BigInteger($data, 256);
        while ($data->compare($base) >= 0) {
            list($data, $remainder) = $data->divide($base);
            $encoded = $alphabet[(int)$remainder->toString()] . $encoded;
        }
        $encoded = $alphabet[(int)$data->toString()] . $encoded;

        return $encoded;
    }

    public function getBalance(string $walletAddress): int
    {
        try {
            $response = $this->solana->call('getBalance', [$walletAddress]);
            return (int) $response['value'];
        } catch (ClientExceptionInterface $e) {
            throw new GenericException('Error fetching wallet balance: ' . $e->getMessage());
        }
    }

    /**
     * @throws RandomException
     * @throws InputValidationException
     * @throws GenericException
     * @throws \SodiumException
     * @throws TokenOwnerOffCurveError
     */
    public function run(): array
    {

        $client = new \GuzzleHttp\Client();

        $body = [
            'chain' => 'SOL',
            'to' => 'Gkg3d9fSsrJN8VZ5kdY3kUfGHW4vzG4JhgkW87gAauVk',
            'metadata' => [
                'name' => 'SOLpepens',
                'symbol' => 'SOLPEPEN',
                'sellerFeeBasisPoints' => 500,
                'uri' => 'https://bafybeie3jsblpiksu2wqhwudptghfdvn6k6rirhagtr4gixeok3gjfxrue.ipfs.nftstorage.link/1.json',
                'collection' => '5oe6rE9fw7Xi6g9eF4SUqkonAU3JLAchkUTcoLy2e5pT',
                'mutable' => true,
            ]
        ];

        $response = $client->request('POST', 'https://api.tatum.io/v3/nft/mint', [
            'body' => json_encode($body),
            'headers' => [
                'accept' => 'application/json',
                'content-type' => 'application/json',
                'x-api-key' => 't-6650d7a5ae6526001c314401-941859c6ea924060b24f6d75',
            ],
        ]);

        var_dump($response->getBody());
        exit;



        $sdk = new \Tatum\Sdk('t-6650d7a5ae6526001c314401-a44e4aff0484443b80d314b5');
        $sdk->mainnet()->config()->setDebug(true);

        $imageCID = 'bafybeiafqglxctbxb326scwje5jclrq45kz5clrnosqcf7ch6cye6r2yiq';
        $metadata = (array) json_decode(file_get_contents(ROOT . '/data/metadata/1.json'));
        $metadata['image'] = str_replace('REPLACE_WITH_IMAGE_IPFS_URL', $imageCID, $metadata['image']);


        $metadata = new SolanaNftMetadata($metadata);
        $mintNFTSolana = (new \Tatum\Model\MintNftSolana())
            ->setChain('SOL')
            ->setTo('Gkg3d9fSsrJN8VZ5kdY3kUfGHW4vzG4JhgkW87gAauVk')
            ->setFrom('Gkg3d9fSsrJN8VZ5kdY3kUfGHW4vzG4JhgkW87gAauVk')
            ->setFromPrivateKey()
            ->setCollectionVerifierPrivateKey()
            ->setMetadata($metadata);

        try {
            $response = $sdk->mainnet()
                ->api()
                ->nFTERC721OrCompatible()
                ->mintNftSolana($mintNFTSolana);

            (new Slack())->sendInfoMessage(json_encode($response));

        } catch (\Tatum\Sdk\ApiException $apiExc) {
            echo sprintf(
                "API Exception when calling api()->nFTERC721OrCompatible()->mintNftSolana(): %s\n",
                var_export($apiExc->getResponseObject(), true)
            );
        } catch (\Exception $exc) {
            echo sprintf(
                "Exception when calling api()->nFTERC721OrCompatible()->mintNftSolana(): %s\n",
                $exc->getMessage()
            );
        }
//        exit;

        try {
            $privateKey = env('PRIVATE_KEY');
            $decodedPrivateKey = $this->base58_decode($privateKey);

            if (strlen($decodedPrivateKey) !== SODIUM_CRYPTO_SIGN_SECRETKEYBYTES) {
                throw new GenericException('Private key must be ' . SODIUM_CRYPTO_SIGN_SECRETKEYBYTES . ' bytes long.');
            }

            $feePayer = Keypair::fromSecretKey($decodedPrivateKey);
            (new Slack())->sendInfoMessage('Public Key: ' . $feePayer->getPublicKey());

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $data = json_decode(file_get_contents('php://input'), true);
                $userWalletAddress = $data['walletAddress'];
                $metadataUrl = $data['metadataUrl'];

                $mintKeypair = Keypair::generate();
                $mintPubkey = new PublicKey($mintKeypair->getPublicKey());
                (new Slack())->sendInfoMessage('Mint public key: ' . $mintPubkey);

                $lamportsForRentExemption = 2039280;
                $MINT_LAYOUT_SIZE = 82;

                // testing



                // 1. Creating the Mint Account:
                // Purpose: This transaction creates a new account on the Solana blockchain that will act as
                // the mint account for the NFT. This account holds the token's mint information, such as
                // the supply and mint authority.
                $createAccount = SystemProgram::createAccount(
                    $feePayer->getPublicKey(),
                    $mintPubkey,
                    $lamportsForRentExemption,
                    $MINT_LAYOUT_SIZE,
                    new PublicKey(SplTokenProgram::TOKEN_PROGRAM_ID)
                );

                // 2. Initializing the Mint
                // Purpose: This initializes the newly created mint account. It sets the initial supply
                // (usually zero for an NFT), the number of decimal places (zero for an NFT), and the authority
                // that can mint new tokens.
                $initializeMintInstruction = $this->initializeMintInstruction(
                    $mintPubkey,
                    $feePayer->getPublicKey(),
                    null,
                    0
                );

                // 3. Creating the Associated Token Account
                // Purpose: This creates an associated token account for the user’s wallet. An associated token
                // account is necessary for holding the NFT once it is minted. Each user needs a separate token
                // account for each type of token they own.
                $associatedTokenAccountInstruction = $this->createAssociatedTokenAccountInstruction(
                    $feePayer->getPublicKey(),
                    new PublicKey($userWalletAddress),
                    $mintPubkey
                );

                // 4. Minting the Token to the Associated Account
                // Purpose: This mints the actual NFT (i.e., a single token) and transfers it to the user's
                // associated token account. This step effectively completes the creation and transfer of
                // the NFT to the user.
                $mintToInstruction = $this->mintToInstruction(
                    $mintPubkey,
                    $feePayer->getPublicKey(),
                    1,
                    $feePayer->getPublicKey()
                );

                $transaction = new Transaction($this->getRecentBlockhash());
                $transaction->add($createAccount);
                $transaction->add($initializeMintInstruction);
//                $transaction->add($associatedTokenAccountInstruction);
//                $transaction->add($mintToInstruction);
                $transaction->sign($feePayer, $mintKeypair);

                $connection = new Connection($this->solana);
                $response = $connection->simulateTransaction($transaction, [$feePayer, $mintKeypair]);

                (new Slack())->sendInfoMessage(json_encode($response));
                var_dump($response);
                exit;


                // end testing



                $transaction = $transaction->add(
                    SystemProgram::createAccount(
                        $feePayer->getPublicKey(),
                        $mintPubkey,
                        $lamportsForRentExemption,
                        $MINT_LAYOUT_SIZE,
                        new PublicKey(SplTokenProgram::TOKEN_PROGRAM_ID)
                    ),
                    $this->initializeMintInstruction(
                        $mintPubkey,
                        $feePayer->getPublicKey(),
                        null,
                        0
                    ),
                    $this->createAssociatedTokenAccountInstruction(
                        $feePayer->getPublicKey(),
                        new PublicKey($userWalletAddress),
                        $mintPubkey
                    ),
                    $this->mintToInstruction(
                        $mintPubkey,
                        $feePayer->getPublicKey(),
                        1,
                        $feePayer->getPublicKey()
                    )
                );

//                $recentBlockhash = $this->getRecentBlockhash();
//                $transaction->recentBlockhash = $recentBlockhash;
//                $transaction->sign($feePayer, $mintKeypair);

                $connection = new Connection($this->solana);
                $signature = $connection->sendTransaction($transaction, [$feePayer, $mintKeypair]);

//                $signature = $this->solana->call('sendTransaction', [$transaction]);

                return ['signature' => $signature];
            }

            throw new GenericException('Invalid request method');
        } catch (\Exception $e) {
            throw new GenericException('An error occurred while minting the NFT (Exception): ' . $e->getMessage());
        } catch (ClientExceptionInterface $e) {
            throw new GenericException('An error occurred while minting the NFT. (ClientExceptionInterface)');
        }
    }

    /**
     * @throws Exception
     */
    public function getRecentBlockhash()
    {
        $connection = new Connection($this->solana);

        $result = $connection->getLatestBlockhash(Commitment::finalized());

        return $result['blockhash'];
    }



    private function initializeMintInstruction(
        PublicKey $mint,
        PublicKey $mintAuthority,
        ?PublicKey $freezeAuthority,
        int $decimals
    ): TransactionInstruction {
        $data = array_merge(
            unpack("C*", pack("V", 0)), // Mint initialization instruction index
            unpack("C*", pack("C", $decimals)),
            $mintAuthority->toBytes(),
            [isset($freezeAuthority) ? 1 : 0],
            isset($freezeAuthority) ? $freezeAuthority->toBytes() : array_fill(0, 32, 0)
        );

        $keys = [
            new AccountMeta($mint, false, true),
            new AccountMeta(new PublicKey(SystemProgram::PROGRAM_ID), false, false)
        ];

        return new TransactionInstruction(
            new PublicKey(SplTokenProgram::TOKEN_PROGRAM_ID),
            $keys,
            $data
        );
    }

    /**
     * @throws TokenOwnerOffCurveError
     * @throws InputValidationException
     */
    private function createAssociatedTokenAccountInstruction(
        PublicKey $payer,
        PublicKey $walletAddress,
        PublicKey $mint
    ): TransactionInstruction {
        $splTokenProgram = new SplTokenProgram($this->solana);
        $associatedTokenAddress = $splTokenProgram->getAssociatedTokenAddressSync($mint, $walletAddress);

        $data = [];
        $keys = [
            new AccountMeta($payer, true, true),
            new AccountMeta($associatedTokenAddress, false, true),
            new AccountMeta($walletAddress, false, false),
            new AccountMeta($mint, false, false),
            new AccountMeta(new PublicKey(SystemProgram::PROGRAM_ID), false, false),
            new AccountMeta(new PublicKey(SplTokenProgram::ASSOCIATED_TOKEN_PROGRAM_ID), false, false),
            new AccountMeta(new PublicKey(SplTokenProgram::TOKEN_PROGRAM_ID), false, false)
        ];

        return new TransactionInstruction(
            new PublicKey(SplTokenProgram::ASSOCIATED_TOKEN_PROGRAM_ID),
            $keys,
            $data
        );
    }

    private function mintToInstruction(
        PublicKey $mint,
        PublicKey $destination,
        int $amount,
        PublicKey $authority
    ): TransactionInstruction {
        $data = array_merge(
            unpack("C*", pack("V", 7)), // Mint to instruction index
            unpack("C*", pack("P", $amount))
        );

        $keys = [
            new AccountMeta($mint, false, true),
            new AccountMeta($destination, false, true),
            new AccountMeta($authority, true, false)
        ];

        return new TransactionInstruction(
            new PublicKey(SplTokenProgram::TOKEN_PROGRAM_ID),
            $keys,
            $data
        );
    }
}
