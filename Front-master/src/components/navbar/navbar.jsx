import React from 'react';
import './navbar.css'
import { useNavigate , Link } from 'react-router-dom';

function Navbar() {
    const navigate = useNavigate();
    const handleLogout = () => {
        localStorage.removeItem('token');
        navigate('/');
    };
    return (
        <nav className="navbar">
            <div className="navbar-brand">
                <h2>My App</h2>
            </div>
            <ul className="navbar-menu">
                <Link to="/Dashboard"><li><a href="#">Dashboard</a></li></Link>
                <Link to="/Chat"><li><a href="#">Chat</a></li></Link>
                <Link to="/Quiz"><li><a href="#">Quiz</a></li></Link>
                <Link to="/Summarysheet"><li><a href="#">Summarysheet</a></li></Link>
                <Link to="/profile"><li><a href="#">Profile</a></li></Link>
                <button onClick={handleLogout}>Logout</button>
            </ul>
        </nav>
    );
}

export default Navbar;
