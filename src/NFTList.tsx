import React, { useEffect, useState } from 'react';
import Moralis from 'moralis';
import { Connection, PublicKey, Transaction } from '@solana/web3.js';
import { createBurnCheckedInstruction } from '@solana/spl-token';
import { useWallet } from '@solana/wallet-adapter-react';

const NFTList: React.FC = () => {
    const [nfts, setNFTs] = useState<any[] | null>(null);
    const connection = new Connection('https://mainnet.helius-rpc.com/?api-key=' + process.env.HELIUS_API_KEY); // Update with your preferred RPC endpoint
    const { publicKey, signTransaction } = useWallet();

    useEffect(() => {
        const fetchNFTs = async () => {
            try {
                const walletAddress = localStorage.getItem('walletAddress');
                if (!walletAddress) {
                    console.error('Wallet address not found in local storage');
                    return;
                }

                const options = { address: walletAddress };
                const response = await Moralis.SolApi.account.getNFTs(options);
                const nftsArray = Object.values(response)[0];
                console.log("NFTs:", nftsArray);
                setNFTs(nftsArray);
            } catch (error) {
                console.error('Error fetching NFTs:', error);
            }
        };

        fetchNFTs();

        const handleWalletChange = () => {
            fetchNFTs();
        };
        window.addEventListener('walletChanged', handleWalletChange);

        return () => {
            window.removeEventListener('walletChanged', handleWalletChange);
        };
    }, []);

    const burnNFT = async (nft: any) => {
        try {
            if (!publicKey || !signTransaction) {
                console.error('Wallet not connected');
                return;
            }

            const tokenAccountPubkey = new PublicKey(nft.associatedTokenAddress);
            const mintPubkey = new PublicKey(nft.mint);

            const transaction = new Transaction().add(
                createBurnCheckedInstruction(
                    tokenAccountPubkey, // token account
                    mintPubkey, // mint
                    publicKey, // owner of token account
                    1, // amount
                    0 // decimals, typically 0 for NFTs
                )
            );

            // Get the latest blockhash
            const { blockhash } = await connection.getRecentBlockhash();
            transaction.recentBlockhash = blockhash;
            transaction.feePayer = publicKey;

            // Sign the transaction
            const signedTransaction = await signTransaction(transaction);

            // Send the transaction
            const txid = await connection.sendRawTransaction(signedTransaction.serialize());
            console.log('Transaction sent with ID:', txid);

            // Confirm the transaction
            await connection.confirmTransaction(txid);
            console.log('Transaction confirmed');
        } catch (error) {
            console.error('Error burning NFT:', error);
        }
    };

    const mintNFT = async () => {
        try {
            if (!publicKey) {
                console.error('Wallet not connected');
                return;
            }

            // Call the minting method here
            console.log('Minting NFT...');
        } catch (error) {
            console.error('Error minting NFT:', error);
        }
    };

    return (
        <div className="row">
            <button onClick={mintNFT}>Mint NFT</button>
            {nfts ? (
                nfts.map((nft: any, index: number) => (
                    <div className="col-md-4 mb-4" key={index}>
                        <div className="card">
                            <div className="card-header">{nft.name}</div>
                            <div className="card-body">
                                associatedTokenAddress<br />
                                {nft.associatedTokenAddress}<br />
                                <br />
                                mint<br />
                                {nft.mint}<br />
                                <br />
                                symbol<br />
                                {nft.symbol}<br />
                                <br />
                                <img src={nft.image} alt={nft.name} className="card-img-top" />
                                <br />
                                <button onClick={() => burnNFT(nft)} className="btn btn-danger mt-3">
                                    Burn NFT
                                </button>
                            </div>
                        </div>
                    </div>
                ))
            ) : (
                <p>Loading NFTs...</p>
            )}
        </div>
    );
};

export default NFTList;
