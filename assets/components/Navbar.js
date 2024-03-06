import React from 'react';
import { Link } from 'react-router-dom';

function Navbar() {
    return (
        <nav>
            <ul>
                <li><Link to="/dashboard">Dashboard</Link></li>
                <li><Link to="/chat">Chat</Link></li>
                <li><Link to="/quiz">Quiz</Link></li>
                <li><Link to="/profile">Profile</Link></li>
                <li><Link to="/summarysheet">Summary</Link></li>
            </ul>
        </nav>
    );
}

export default Navbar;