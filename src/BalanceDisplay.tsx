import React, { useEffect, useState } from 'react';
import Moralis from 'moralis';
import axios from 'axios';

interface BalanceDisplayProps {
    address: string;
}

const BalanceDisplay: React.FC<BalanceDisplayProps> = ({ address }) => {
    const [balance, setBalance] = useState<string | null>(null);

    useEffect(() => {
        // Reset balance to null when address changes
        setBalance(null);

        const fetchBalance = async () => {
            try {
                // Retrieve sessionId from sessionStorage
                const sessionId = sessionStorage.getItem('sessionId');
                if (!sessionId) {
                    console.error('Session ID not found');
                    return;
                }

                // Check if balance is available via API call to "get-balance"
                const response = await axios.post(process.env.APP_API_URL + '/get-balance', {
                    walletAddress: address,
                    sessionId: sessionId
                });

                // If balance is retrieved from the backend
                if (response.data.balance !== null) {
                    setBalance(response.data.balance);
                } else {
                    // Fetch balance from Moralis
                    const result = await Moralis.SolApi.account.getBalance({
                        "network": "mainnet",
                        "address": address
                    });
                    const balanceInLamports = Number(result.result.lamports);
                    const balanceInSOL = (balanceInLamports / 10 ** 9 / 10 ** 9).toFixed(4);
                    const newBalance = balanceInSOL.toString();
                    setBalance(newBalance);

                    // Send data to backend to set balance
                    await axios.post(process.env.APP_API_URL + '/set-balance', {
                        balance: newBalance,
                        walletAddress: address,
                        sessionId: sessionId
                    });
                }
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
