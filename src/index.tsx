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
                        <h1>CryptoBots Upgrade</h1>
                        <p>Upgrade your CryptoBot V1 to V2</p>
                    </div>
                </div>
            </div>
            <NFTList />
            <MintNFT /> {/* Integrate MintNFT component here */}
        </WalletConnect>

        <footer>
            <a target="_blank" href="https://x.com/cryptobotsnfts">CryptoBots</a> is an NFT collection on Solana created by <a target="_blank" href="https://x.com/0xEDDB">Ed</a>

            <br />
            <hr />

            <small>
                This dApp is powered and maintained by <a target="_blank" href="https://hasmints.com">HasMints</a>
            </small>
        </footer>

    </React.StrictMode>,
    document.getElementById('root')
);

reportWebVitals();
