import React, { useState, useEffect } from 'react';
import './manageuser.css';
import {Link} from "react-router-dom";
import AdminNavbar from "../navbar/AdminNavbar";


const ManageUsers = () => {
    const [users, setUsers] = useState([]);
    const [searchQuery, setSearchQuery] = useState('');

    useEffect(() => {
        const token = localStorage.getItem('token');

        fetch('https://127.0.0.1:8000/api/administrator/users', {
                headers: {
                    'Authorization': `Bearer ${token}`
                }
            }
        )
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                setUsers(data);
            })
            .catch(error => {
                console.error('Error fetching users:', error);
            });
    }, []);
    const deleteUser = (id) => {
        const token = localStorage.getItem('token');

        fetch(`https://127.0.0.1:8000/api/administrator/users/${id}`, {
                method: 'DELETE',
                headers: {
                    'Authorization': `Bearer ${token}`
                }
            }
        )
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                setUsers(users.filter(user => user.id !== id));
            })
            .catch(error => {
                console.error('Error deleting user:', error);
            });
    };

    const handleSearchChange = (event) => {
        setSearchQuery(event.target.value);
    };

    const filteredUsers = users.filter(user =>
        user.email?.toLowerCase().includes(searchQuery.toLowerCase()) ||
        user.firstName?.toLowerCase().includes(searchQuery.toLowerCase()) ||
        user.lastName?.toLowerCase().includes(searchQuery.toLowerCase())
    );

    return (

        <>
        <div>
            <AdminNavbar/>
            <div className="header-container">
                <h2>User Management</h2>
            </div>
            <input
                type="text"
                placeholder="Search by name or email"
                value={searchQuery}
                onChange={handleSearchChange}
            />
            {filteredUsers.length === 0 && (
                <p>No results found.</p>
            )}
            <table>
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Email</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Created At</th>
                    <th>Uploaded Files Count</th>
                    <th>Delete</th>
                </tr>
                </thead>
                <tbody>
                {filteredUsers.map(user => (
                    <tr key={user.id}>
                        <td>{user.id}</td>
                        <td>{user.email}</td>
                        <td>{user.firstname}</td>
                        <td>{user.lastname}</td>
                        <td>{user.createdAt}</td>
                        <td>{user.uploadedFilesCount}</td>
                        <td>
                            <button className="action-button" onClick={() => deleteUser(user.id)}>Delete</button>
                        </td>
                    </tr>
                ))}
                </tbody>
                <tfoot>
                <tr>
                    <td colSpan="9">
                        <div className="pagination">
                            <span>&laquo;</span>
                            <span className="active">1</span>
                            <span>2</span>
                            <span>3</span>
                            <span>&raquo;</span>
                        </div>
                    </td>
                </tr>
                </tfoot>
            </table>
        </div>
    <footer className="admin-footer">
        <p>&copy; 2023 IASTUDY. All rights reserved.</p>
        <p><Link to="/privacy-policy">Privacy Policy</Link></p>
    </footer>
            </>

    );
}

export default ManageUsers;
