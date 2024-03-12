import React from 'react';
import { Link } from 'react-router-dom';

class AdminHomepage extends React.Component {
    render() {
        return (
            <div>
                <h1>Welcome, Admin!</h1>
                <Link to="/administrator/manage-users">
                    <button>Manage Users</button>
                </Link>
                <Link to="/administrator/manage-documents">
                    <button>Manage Documents</button>
                </Link>
            </div>
        );
    }
}

export default AdminHomepage;