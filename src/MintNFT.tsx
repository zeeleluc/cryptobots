import React, { useState, useEffect } from 'react';
import { useWallet } from '@solana/wallet-adapter-react';
import axios from 'axios';
import { Connection } from '@solana/web3.js';

const MintNFT = () => {
    const { publicKey } = useWallet();
    const [minting, setMinting] = useState(false);
    const [mintingError, setMintingError] = useState<string | null>(null);

    useEffect(() => {
        if (publicKey) {
            console.log(`Connected wallet address: ${publicKey.toBase58()}`);
        }
    }, [publicKey]);

    const handleMint = async () => {
        if (!publicKey) {
            alert('Please connect your wallet first.');
            return;
        }

        setMinting(true);
        setMintingError(null);

        try {
            const sessionId = sessionStorage.getItem('sessionId');
            const metadataResponse = await axios.post(process.env.APP_API_URL + '/upload-metadata', {
                walletAddress: publicKey.toBase58(),
                sessionId
            });

            const metadataUrl = metadataResponse.data.metadata_ipfs_url;

            const mintResponse = await axios.post(process.env.APP_API_URL + '/mint-nft', {
                walletAddress: publicKey.toBase58(),
                metadataUrl,
                sessionId
            });

            console.log('Minting response:', mintResponse.data);
            alert('NFT minted successfully! Transaction signature: ' + mintResponse.data.signature);
        } catch (error) {
            console.error('Error minting NFT:', error);

            // Handle error with type guard
            if (error instanceof Error) {
                setMintingError(error.message);
            } else {
                setMintingError('An unexpected error occurred.');
            }
        } finally {
            setMinting(false);
        }
    };

    return (
        <div>
            {/*<button onClick={handleMint} disabled={minting}>*/}
            {/*    {minting ? 'Minting...' : 'Mint NFT'}*/}
            {/*</button>*/}
            {/*{mintingError && <p style={{ color: 'red' }}>Error: {mintingError}</p>}*/}
        </div>
    );
};

export default MintNFT;
