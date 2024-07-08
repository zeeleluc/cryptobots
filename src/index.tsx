import React from 'react';
import ReactDOM from 'react-dom';
import WalletConnect from './WalletConnect';
import NFTList from './NFTList';
import MintNFT from './MintNFT';
import './bootstrap.min.css';
import './index.css';
import reportWebVitals from './reportWebVitals';
import Moralis from "moralis";
import { v4 as uuidv4 } from 'uuid';

Moralis.start({
    apiKey: process.env.MORALIS_API_KEY
});

let sessionId = sessionStorage.getItem('sessionId');
if (!sessionId) {
    sessionId = uuidv4();
    sessionStorage.setItem('sessionId', sessionId);
}

ReactDOM.render(
    <React.StrictMode>
        <WalletConnect>
            <div className="container-fluid">
                <div className="row">
                    <div className="col">
                        <h1>Cryptobots</h1>
                    </div>
                </div>
            </div>
            <NFTList />
            <MintNFT /> {/* Integrate MintNFT component here */}
        </WalletConnect>
    </React.StrictMode>,
    document.getElementById('root')
);

reportWebVitals();
