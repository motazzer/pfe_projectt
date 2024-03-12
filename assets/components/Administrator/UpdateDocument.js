import React, { useState, useEffect } from 'react';
import { useNavigate, useParams } from 'react-router-dom';

const UpdateDocument = () => {
    const { id } = useParams();
    const navigate = useNavigate();
    const [documentData, setDocumentData] = useState({});
    const [formData, setFormData] = useState({
        title: '',
        content: ''
    });

    const [successMessage, setSuccessMessage] = useState('');

    useEffect(() => {
        const token = localStorage.getItem('token');

        fetch(`/api/administrator/documents/${id}`, {
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
                setDocumentData(data);
                setFormData({
                    title: data.title,
                    content: data.content
                });
            })
            .catch(error => {
                console.error('Error fetching document details:', error);
            });
    }, [id]);

    const handleInputChange = (event) => {
        const { name, value } = event.target;
        setFormData({ ...formData, [name]: value });
    };

    const handleSubmit = (event) => {
        event.preventDefault();
        const token = localStorage.getItem('token');

        fetch(`/api/administrator/documents/${id}`, {
            method: 'PUT',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(formData)
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                setSuccessMessage('Document updated successfully');
                setTimeout(() => {
                    navigate('/administrator/manage-documents');
                }, 2000);
            })
            .catch(error => {
                console.error('Error updating document:', error);
            });
    };

    return (
        <div>
            <h2>Update Document</h2>
            <form onSubmit={handleSubmit}>
                <div>
                    <label htmlFor="title">Title:</label>
                    <input type="text" id="title" name="title" value={formData.title} onChange={handleInputChange} />
                </div>
                <div>
                    <label htmlFor="content">Content:</label>
                    <textarea id="content" name="content" value={formData.content} onChange={handleInputChange} />
                </div>
                <button type="submit">Update</button>
                {successMessage && <p>{successMessage}</p>}
            </form>
        </div>
    );
};

export default UpdateDocument;
