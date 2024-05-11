import React from 'react';
import ReactDOM from 'react-dom';
import WalletConnect from './WalletConnect';
import './index.css';
import reportWebVitals from './reportWebVitals';
import Moralis from "moralis";
import { v4 as uuidv4 } from 'uuid';

Moralis.start({
    apiKey: process.env.MORALIS_API_KEY
});

// Generate or retrieve a unique identifier from session storage
let sessionId = sessionStorage.getItem('sessionId');
if (!sessionId) {
    sessionId = uuidv4(); // Generate a new UUID if not found
    sessionStorage.setItem('sessionId', sessionId); // Store the UUID in session storage
}

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
