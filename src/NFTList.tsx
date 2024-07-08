import React, { useEffect, useState } from 'react';
import Moralis from 'moralis';
import { Connection, PublicKey, Transaction } from '@solana/web3.js';
import { createBurnCheckedInstruction } from '@solana/spl-token';
import { useWallet } from '@solana/wallet-adapter-react';

const NFTList: React.FC = () => {
    const [nfts, setNFTs] = useState<any[] | null>(null);
    const [loading, setLoading] = useState<boolean>(true);
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

                // Filter NFTs based on the has-hash check
                const filteredNFTs = await Promise.all(nftsArray.map(async (nft: any) => {
                    const mint = nft.mint;
                    const metadataUrl = `${process.env.APP_API_URL}/has-hash/${mint}`;
                    const result = await fetch(metadataUrl);
                    const data = await result.json();
                    if (data.has_hash) {
                        const id = nft.name.split('#')[1];
                        const metadataResponse = await fetch(`${process.env.APP_API_URL}/get-metadata/${id}`);
                        const metadata = await metadataResponse.json();
                        return { ...nft, metadata, id };
                    }
                    return null;
                }));

                const validNFTs = filteredNFTs.filter((nft: any) => nft !== null);
                setNFTs(validNFTs);
                setLoading(false);
            } catch (error) {
                console.error('Error fetching NFTs:', error);
                setLoading(false);
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

    return (
        <div className="container-fluid">
            <div className="row">
                {loading ? (
                    <div className="col">
                        <h2>Loading Cryptobots...</h2>
                    </div>
                ) : nfts && nfts.length > 0 ? (
                    nfts.map((nft: any, index: number) => (
                        <div className="col col-lg-2 col-12 col-md-6 mb-3" key={index}>
                            <div className="card">
                                <div className="card-header">
                                    <h2>
                                        {nft.metadata.name}
                                    </h2>
                                </div>
                                <div className="card-body">
                                    {/*<table className="table">*/}
                                    {/*    <tbody>*/}
                                        {/*<tr>*/}
                                        {/*    <td>associatedTokenAddress</td>*/}
                                        {/*    <td>{nft.associatedTokenAddress}</td>*/}
                                        {/*</tr>*/}
                                        {/*<tr>*/}
                                        {/*    <td>mint</td>*/}
                                        {/*    <td>{nft.mint}</td>*/}
                                        {/*</tr>*/}
                                        {/*<tr>*/}
                                        {/*    <td>symbol</td>*/}
                                        {/*    <td>{nft.metadata.symbol}</td>*/}
                                        {/*</tr>*/}
                                        {/*<tr>*/}
                                        {/*    <td>description</td>*/}
                                        {/*    <td>{nft.metadata.description}</td>*/}
                                        {/*</tr>*/}
                                        {/*{nft.metadata.attributes.map((attr: any, i: number) => (*/}
                                        {/*    <tr key={i}>*/}
                                        {/*        <td>{attr.trait_type}</td>*/}
                                        {/*        <td>{attr.value}</td>*/}
                                        {/*    </tr>*/}
                                        {/*))}*/}
                                    {/*    </tbody>*/}
                                    {/*</table>*/}
                                    <img src={`${process.env.V2_URL}${nft.id}.jpg`} alt={nft.metadata.name} className="card-img-top" />
                                    <button className="btn btn-default disabled mt-3">
                                        Morph to V2 (soon)
                                    </button>
                                    {/*<button onClick={() => burnNFT(nft)} className="btn btn-danger mt-3">*/}
                                    {/*    Burn NFT*/}
                                    {/*</button>*/}
                                </div>
                            </div>
                        </div>
                    ))
                ) : (
                    <div className="col">
                        <h2>No Cryptobots found.</h2>
                    </div>
                )}
            </div>
        </div>
    );
};

export default NFTList;
