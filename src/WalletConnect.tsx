import React, { FC, ReactNode, useMemo, useEffect } from 'react';
import { WalletAdapterNetwork } from '@solana/wallet-adapter-base';
import { ConnectionProvider, WalletProvider, useWallet } from '@solana/wallet-adapter-react';
import { WalletModalProvider, WalletMultiButton } from '@solana/wallet-adapter-react-ui';
import { clusterApiUrl } from '@solana/web3.js';
import axios from 'axios';
import { v4 as uuidv4 } from 'uuid';

import BalanceDisplay from './BalanceDisplay';

require('./WalletConnect.css');
require('@solana/wallet-adapter-react-ui/styles.css');

const WalletConnect: FC = () => {
    return (
        <Context>
            <Content />
        </Context>
    );
};
export default WalletConnect;

const Context: FC<{ children: ReactNode }> = ({ children }) => {
    const network = WalletAdapterNetwork.Mainnet;
    const endpoint = useMemo(() => clusterApiUrl(network), [network]);

    const wallets = useMemo(
        () => [],
        [network]
    );

    // Generate or retrieve a unique session ID and store it in session storage
    let sessionId = sessionStorage.getItem('sessionId');
    if (!sessionId) {
        sessionId = uuidv4();
        sessionStorage.setItem('sessionId', sessionId);
    }

    return (
        <ConnectionProvider endpoint={endpoint}>
            <WalletProvider wallets={wallets} autoConnect>
                <WalletModalProvider>{children}</WalletModalProvider>
            </WalletProvider>
        </ConnectionProvider>
    );
};
const Content: FC = () => {
    const { publicKey } = useWallet();

    useEffect(() => {
        if (publicKey) {
            pushWalletAddress(publicKey.toBase58());
        }
    }, [publicKey]);

    const pushWalletAddress = async (walletAddress: string) => {
        try {
            const sessionId = sessionStorage.getItem('sessionId');
            await axios.post(process.env.APP_API_URL + '/push-wallet-address', { walletAddress, sessionId }); // Include sessionId in the payload
        } catch (error) {
            console.error('Error pushing wallet address to the server:', error);
        }
    };

    return (
        <div className="App">
            <WalletMultiButton />
            {publicKey && <BalanceDisplay address={publicKey.toBase58()} />}
        </div>
    );
};
