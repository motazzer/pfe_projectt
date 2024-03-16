import React from 'react';
import { Link, useNavigate } from 'react-router-dom';

const AdminHomepage = () => {
    const navigate = useNavigate();

    const handleLogout = () => {
        localStorage.removeItem('token');
        navigate('/');
    };

    return (
        <div>
            <h1>Welcome, Admin!</h1>
            <button onClick={handleLogout}>Logout</button>
            <Link to="/administrator/manage-users">
                <button>Manage Users</button>
            </Link>
            <Link to="/administrator/manage-documents">
                <button>Manage Documents</button>
            </Link>
        </div>
    );
};

export default AdminHomepage;
