import React, { useState, useEffect } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import './ManageDocuments.css';

const ManageDocuments = () => {
    const [documents, setDocuments] = useState([]);
    const [searchQuery, setSearchQuery] = useState('');
    const navigate = useNavigate();
    const [openMenus, setOpenMenus] = useState({});
    const [currentPage, setCurrentPage] = useState(1);
    const documentsPerPage = 7;


    useEffect(() => {
        const token = localStorage.getItem('token');
        fetch('https://127.0.0.1:8000/api/administrator/documents', {
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
                setDocuments(data);
            })
            .catch(error => {
                console.error('Error fetching documents:', error);
            });
    }, []);

    const handleSearchChange = (event) => {
        setSearchQuery(event.target.value);
    };

    const deleteDocument = (id) => {
        const token = localStorage.getItem('token');

        fetch(`https://127.0.0.1:8000/api/administrator/documents/${id}`, {
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
                setDocuments(documents.filter(document => document.id !== id));
            })
            .catch(error => {
                console.error('Error deleting document:', error);
            });
    };

    const verifyDocument = (id) => {
        const token = localStorage.getItem('token');

        fetch(`https://127.0.0.1:8000/api/administrator/documents/${id}/verify`, {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${token}`
                }
            }
        )
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                setDocuments(documents.map(document => {
                    if (document.id === id) {
                        return { ...document, status: 'verified' };
                    }
                    return document;
                }));
            })
            .catch(error => {
                console.error('Error verifying document:', error);
            });
    };

    const updateDocument = (id) => {
        navigate(`/update-document/${id}`);
    };

    const detailsDocument = (id) => {
        navigate(`/details-document/${id}`);
    };

    const handleLogout = () => {
        localStorage.removeItem('token');
        navigate('/');
    };

    const toggleMenu = (id) => {
        setOpenMenus(prevState => ({
            ...prevState,
            [id]: !prevState[id]
        }));
    };

    const handlePageChange = (pageNumber) => {
        setCurrentPage(pageNumber);
    };

    const indexOfLastDocument = currentPage * documentsPerPage;
    const indexOfFirstDocument = indexOfLastDocument - documentsPerPage;
    const filteredDocuments = documents.filter(document =>
        document.title.toLowerCase().includes(searchQuery.toLowerCase()) ||
        document.created_by.toLowerCase().includes(searchQuery.toLowerCase())
    ).slice(indexOfFirstDocument, indexOfLastDocument);

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
                        <h2>Document Management</h2>
                    </div>
                    <div className="content">
                        <input
                            type="text"
                            placeholder="Search by title or created by"
                            value={searchQuery}
                            onChange={handleSearchChange}
                        />
                        {filteredDocuments.length === 0 && (
                            <p>No results found.</p>
                        )}
                        <div className="table-container">
                            <table>
                                <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Title</th>
                                    <th>Created By</th>
                                    <th>Created At</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                {filteredDocuments.map(document => (
                                    <tr key={document.id}>
                                        <td>{document.id}</td>
                                        <td>{document.title}</td>
                                        <td>{document.created_by}</td>
                                        <td>{document.createdAt}</td>
                                        <td>{document.status}</td>
                                        <td>
                                            <div className="dropdown">
                                                <button onClick={() => toggleMenu(document.id)} className="dropbtn">...</button>
                                                {openMenus[document.id] && (
                                                    <div className="dropdown-content">
                                                        {document.status === 'unverified' && (
                                                            <>
                                                                <a>
                                                                    <button className="verify"
                                                                        onClick={() => verifyDocument(document.id)}>Verify
                                                                    </button>
                                                                </a>
                                                                <a>
                                                                    <button className="update"
                                                                        onClick={() => updateDocument(document.id)}>Update
                                                                    </button>
                                                                </a>
                                                            </>
                                                        )}
                                                        <a>
                                                            <button className="delete" onClick={() => deleteDocument(document.id)}>Delete
                                                            </button>
                                                        </a>
                                                        <a>
                                                            <button className="detail" onClick={() => detailsDocument(document.id)}>Details
                                                            </button>
                                                        </a>
                                                    </div>
                                                )}
                                            </div>
                                        </td>
                                    </tr>
                                ))}
                                </tbody>
                            </table>
                        </div>
                        <div className="pagination">
                            <span onClick={() => handlePageChange(currentPage > 1 ? currentPage - 1 : 1)}>&laquo;</span>
                            {Array.from({length: Math.ceil(documents.length / documentsPerPage)}, (_, i) => (
                                <span key={i + 1} className={currentPage === i + 1 ? 'active' : ''}
                                      onClick={() => handlePageChange(i + 1)}>
                                {i + 1}
                            </span>
                            ))}
                            <span
                                onClick={() => handlePageChange(currentPage < Math.ceil(documents.length / documentsPerPage) ? currentPage + 1 : Math.ceil(documents.length / documentsPerPage))}>&raquo;</span>
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

export default ManageDocuments;
