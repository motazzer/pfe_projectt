import React, { useState, useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import './manageuser/manageuser.css';

const ManageDocuments = () => {
    const [documents, setDocuments] = useState([]);
    const [searchQuery, setSearchQuery] = useState('');
    const navigate = useNavigate();

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
                        return {...document, status: 'verified'};
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
    const DetailsDocument = (id) => {
        navigate(`/details-document/${id}`);
    };

    const formatContent = (content) => {
        if (content) {
            let truncatedContent = '' ;
            const words = content.split(/\s+/);
            truncatedContent = words.slice(0,7).join(' ');
            return truncatedContent;
        }
    };

    const filteredDocuments = documents.filter(document =>
        document.title.toLowerCase().includes(searchQuery.toLowerCase()) ||
        document.created_by.toLowerCase().includes(searchQuery.toLowerCase())
    );

    return (
        <div>
            <div className="header-container">
            <h2>Document Management</h2>
            </div>
            <input
                type="text"
                placeholder="Search by title or created by"
                value={searchQuery}
                onChange={handleSearchChange}
            />
            {filteredDocuments.length === 0 && (
                <p>No results found.</p>
            )}
            <table>
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Created By</th>
                    <th>Created At</th>
                    <th>Status</th>
                    <th>Content</th>
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
                        <td>{formatContent(document.content)}...</td>
                        <td>
                            {document.status === 'unverified' && (
                                <>
                                    <button onClick={() => verifyDocument(document.id)}>Verify</button>
                                    <button onClick={() => updateDocument(document.id)}>Update</button>
                                </>
                            )}
                            <button onClick={() => deleteDocument(document.id)}>Delete</button>
                            <button onClick={() => DetailsDocument(document.id)}>Details</button>
                        </td>
                    </tr>
                ))}
                </tbody>
            </table>
        </div>
    );
}

export default ManageDocuments;
