import React, { useEffect, useState } from 'react';
import Moralis from 'moralis';

interface BalanceDisplayProps {
    address: string;
}

const BalanceDisplay: React.FC<BalanceDisplayProps> = ({ address }) => {
    const [balance, setBalance] = useState<string | null>(null);

    useEffect(() => {
        const fetchBalance = async () => {
            try {
                const result = await Moralis.SolApi.account.getBalance({
                    "network": "mainnet",
                    "address": address
                });
                const balanceInLamports = Number(result.result.lamports);
                const balanceInSOL = (balanceInLamports / 10 ** 9 / 10 ** 9).toFixed(4);;
                setBalance(balanceInSOL.toString());
            } catch (error) {
                console.error('Error fetching balance:', error);
            }
        };

        if (address) {
            fetchBalance();
        }
    }, [address]);

    return (
        <div>
            {balance !== null ? (
                <p>Balance: {balance} SOL</p>
            ) : (
                <p>Loading balance...</p>
            )}
        </div>
    );
};

export default BalanceDisplay;
