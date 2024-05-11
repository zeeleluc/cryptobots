import React from 'react';
import ReactDOM from 'react-dom';
import WalletConnect from './WalletConnect';
import './index.css';
import reportWebVitals from './reportWebVitals';
import Moralis from "moralis";

Moralis.start({
    apiKey: process.env.MORALIS_API_KEY
});

ReactDOM.render(
    <React.StrictMode>
        <WalletConnect />
    </React.StrictMode>,
    document.getElementById('wallet-connect')
);

// If you want to start measuring performance in your app, pass a function
// to log results (for example: reportWebVitals(console.log))
// or send to an analytics endpoint. Learn more: https://bit.ly/CRA-vitals
reportWebVitals();
