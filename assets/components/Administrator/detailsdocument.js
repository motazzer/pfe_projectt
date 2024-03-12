import React, { useState, useEffect } from 'react';
import { useParams } from 'react-router-dom';

const DetailsDocument = () => {
    const { id } = useParams();
    const [documentData, setDocumentData] = useState(null);

    useEffect(() => {
        const token = localStorage.getItem('token');

        fetch(`/api/administrator/documents/details/${id}`, {
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
            })
            .catch(error => {
                console.error('Error fetching document details:', error);
            });
    }, [id]);

    if (!documentData) {
        return <div>Loading...</div>;
    }

    return (
        <div>
            <h2>Document Details</h2>
            <p>ID: {documentData.id}</p>
            <p>Title: {documentData.title}</p>
            <p>Created By: {documentData.created_by}</p>
            <p>Created At: {documentData.createdAt}</p>
            <p>Status: {documentData.status}</p>
            <p>Content: {documentData.content}</p>
        </div>
    );
};

export default DetailsDocument;