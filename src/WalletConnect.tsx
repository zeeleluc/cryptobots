import React, { FC, ReactNode, useMemo } from 'react';
import { WalletAdapterNetwork } from '@solana/wallet-adapter-base';
import { ConnectionProvider, WalletProvider, useConnection, useWallet } from '@solana/wallet-adapter-react';
import { WalletModalProvider, WalletMultiButton } from '@solana/wallet-adapter-react-ui'; // Remove useWallet import
import { clusterApiUrl } from '@solana/web3.js';
import BalanceDisplay from './BalanceDisplay'; // Assuming BalanceDisplay is in a file named BalanceDisplay.tsx

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

    return (
        <ConnectionProvider endpoint={endpoint}>
            <WalletProvider wallets={wallets} autoConnect>
                <WalletModalProvider>{children}</WalletModalProvider>
            </WalletProvider>
        </ConnectionProvider>
    );
};

const Content: FC = () => {
    const { publicKey } = useWallet(); // Corrected import statement

    return (
        <div className="App">
            <WalletMultiButton />
            {publicKey && <BalanceDisplay address={publicKey.toBase58()} />}
        </div>
    );
};
