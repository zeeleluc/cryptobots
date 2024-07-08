import React, { FC, ReactNode, useMemo, useEffect } from 'react';
import { WalletAdapterNetwork } from '@solana/wallet-adapter-base';
import { ConnectionProvider, WalletProvider, useWallet } from '@solana/wallet-adapter-react';
import { WalletModalProvider, WalletMultiButton } from '@solana/wallet-adapter-react-ui';
import { clusterApiUrl } from '@solana/web3.js';
import axios from 'axios';
import { v4 as uuidv4 } from 'uuid';

import BalanceDisplay from './BalanceDisplay';
import NFTList from './NFTList'; // Import your NFTList component

require('./WalletConnect.css');
require('@solana/wallet-adapter-react-ui/styles.css');

const WalletConnect: FC<{ children: ReactNode }> = ({ children }) => {
    const network = WalletAdapterNetwork.Mainnet;
    const endpoint = useMemo(() => clusterApiUrl(network), [network]);

    const wallets = useMemo(() => [], [network]); // List of wallets here if you want to include any

    // Generate or retrieve a unique session ID and store it in session storage
    let sessionId = sessionStorage.getItem('sessionId');
    if (!sessionId) {
        sessionId = uuidv4();
        sessionStorage.setItem('sessionId', sessionId);
    }

    return (
        <ConnectionProvider endpoint={endpoint}>
            <WalletProvider wallets={wallets} autoConnect>
                <WalletModalProvider>
                    <Context>{children}</Context>
                </WalletModalProvider>
            </WalletProvider>
        </ConnectionProvider>
    );
};

const Context: FC<{ children: ReactNode }> = ({ children }) => {
    const { publicKey, wallet, disconnect } = useWallet();

    useEffect(() => {
        if (publicKey) {
            pushWalletAddress(publicKey.toBase58());
        } else {
            removeWalletAddress();
        }
    }, [publicKey]);

    const pushWalletAddress = async (walletAddress: string) => {
        try {
            // Save walletAddress in local storage
            localStorage.setItem('walletAddress', walletAddress);

            const sessionId = sessionStorage.getItem('sessionId');
            await axios.post(process.env.APP_API_URL + '/push-wallet-address', { walletAddress, sessionId }); // Include sessionId in the payload

            // Trigger a custom event when the wallet address changes
            const event = new CustomEvent('walletChanged');
            window.dispatchEvent(event);
        } catch (error) {
            console.error('Error pushing wallet address to the server:', error);
        }
    };

    const removeWalletAddress = () => {
        // Remove walletAddress from local storage
        localStorage.removeItem('walletAddress');

        // Trigger a custom event when the wallet address changes
        const event = new CustomEvent('walletChanged');
        window.dispatchEvent(event);
    };

    return (
        <div className="App">
            <WalletMultiButton />
            {publicKey && <BalanceDisplay address={publicKey.toBase58()} />}
            {children} {/* Render the children */}
        </div>
    );
};

export default WalletConnect;
