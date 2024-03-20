import React, { useState, useEffect } from 'react';
import './manageuser.css';
import { Link, useNavigate } from "react-router-dom";

const ManageUsers = () => {
    const [users, setUsers] = useState([]);
    const [searchQuery, setSearchQuery] = useState('');
    const [currentPage, setCurrentPage] = useState(1);
    const usersPerPage = 7;

    const navigate = useNavigate();

    useEffect(() => {
        const token = localStorage.getItem('token');

        fetch('https://127.0.0.1:8000/api/administrator/users', {
            headers: {
                'Authorization': `Bearer ${token}`
            }
        })
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
        })
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
        setCurrentPage(1); // Reset to first page when performing a new search
    };

    const indexOfLastUser = currentPage * usersPerPage;
    const indexOfFirstUser = indexOfLastUser - usersPerPage;
    const filteredUsers = users.filter(user =>
        user.email?.toLowerCase().includes(searchQuery.toLowerCase()) ||
        user.firstName?.toLowerCase().includes(searchQuery.toLowerCase()) ||
        user.lastName?.toLowerCase().includes(searchQuery.toLowerCase())
    ).slice(indexOfFirstUser, indexOfLastUser);

    const paginate = (pageNumber) => setCurrentPage(pageNumber);

    const handleLogout = () => {
        localStorage.removeItem('token');
        navigate('/');
    };

    return (

        <>
            <div className="admin-dashboard">
                <div className="sidebar">
                    <ul>
                        <li><big><big><Link to="/administrator">Dashboard</Link></big></big></li>
                    </ul>
                    <ul>
                        <li><Link to="/administrator/manage-users">Manage Users</Link></li>
                        <li><Link to="/administrator/manage-documents">Manage Documents</Link></li>
                    </ul>
                </div>
                <div className="main-content">
                    <header>
                        <h1>Welcome Admin!</h1>
                        <ul>
                            <li><Link to="/profile-admin">Profile</Link></li>
                            <li><Link to="/" onClick={handleLogout}>Logout</Link></li>
                        </ul>
                    </header>
                    <div className="header-container">
                        <h2>User Management</h2>
                    </div>
                    <div className="content">
                        <input
                            type="text"
                            placeholder="Search by name or email"
                            value={searchQuery}
                            onChange={handleSearchChange}
                        />
                        {filteredUsers.length === 0 && (
                            <p>No results found.</p>
                        )}
                        <div className="table-container">
                            <table>
                                <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Email</th>
                                    <th>First Name</th>
                                    <th>Last Name</th>
                                    <th>Created At</th>
                                    <th>Uploaded Files Count</th>
                                    <th>Action</th>
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
                                            <button className="action-button"
                                                    onClick={() => deleteUser(user.id)}>Delete
                                            </button>
                                        </td>
                                    </tr>
                                ))}
                                </tbody>
                            </table>
                        </div>
                        <div className="pagination">
                            <span onClick={() => paginate(currentPage > 1 ? currentPage - 1 : 1)}>&laquo;</span>
                            {Array.from({length: Math.ceil(users.length / usersPerPage)}, (_, i) => (
                                <span key={i + 1} className={currentPage === i + 1 ? 'active' : ''}
                                      onClick={() => paginate(i + 1)}>
                                    {i + 1}
                                </span>
                            ))}
                            <span
                                onClick={() => paginate(currentPage < Math.ceil(users.length / usersPerPage) ? currentPage + 1 : Math.ceil(users.length / usersPerPage))}>&raquo;</span>
                        </div>
                    </div>
                </div>
            </div>
            <footer className="py-5 bg-dark">
                <div className="container">
                    <p className="m-0 text-center text-white">Copyright &copy; IASTUDY 2023</p>
                </div>
            </footer>
        </>

    );
}

export default ManageUsers;
