import React from 'react';
import {Link, useNavigate} from 'react-router-dom';
import '../../home/css/home.css';


const AdminNavbar = () => {
    const navigate = useNavigate();
    const handleLogout = () => {
        localStorage.removeItem('token');
        navigate('/');
    };
    return (
        <nav className="navbar navbar-expand-lg navbar-dark bg-dark">
            <div className="container">
                <Link className="navbar-brand" to="/">IASTUDY</Link>
                <button className="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span className="navbar-toggler-icon"></span>
                </button>
                <div className="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul className="navbar-nav ms-auto mb-2 mb-lg-0">
                        <li className="nav-item"><Link className="nav-link active" aria-current="page" to="/administrator">Dashboard</Link></li>
                        <li><Link to="/profile-admin">Profile</Link></li>
                        <li><Link to="/" onClick={handleLogout}>Logout</Link></li>
                    </ul>
                </div>
            </div>
        </nav>
    );
}

export default AdminNavbar;
