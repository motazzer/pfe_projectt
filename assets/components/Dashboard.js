import React, { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import Navbar from './Navbar';

function Dashboard() {
    const navigate = useNavigate();
    const [selectedFile, setSelectedFile] = useState(null);
    const [uploadSuccess, setUploadSuccess] = useState(false);

    const handleFileChange = (e) => {
        setSelectedFile(e.target.files[0]);
        setUploadSuccess(false);
    };

    const handleUpload = async () => {
        try {
            const allowedTypes = ['pdf', 'docx', 'txt', 'html'];
            const fileExtension = selectedFile.name.split('.').pop().toLowerCase();

            if (!allowedTypes.includes(fileExtension)) {
                console.error('Unsupported file type');
                alert('Unsupported file type. Please upload a PDF, DOCX, TXT, or HTML file.');
                return;
            }

            const formData = new FormData();
            formData.append('file', selectedFile);

            const token = localStorage.getItem('token');
            if (!token) {
                throw new Error('JWT token not found');
            }

            const response = await fetch('/api/course-documents/upload', {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${token}`,
                },
                body: formData,
            });

            if (response.ok) {
                console.log('File uploaded:', response.data);
                setUploadSuccess(true);
            } else {
                console.error('Error uploading file:', response.statusText);
            }
        } catch (error) {
            console.error('Error:', error);
        }
    };

    const handleLogout = () => {
        localStorage.removeItem('token');
        navigate('/');
    };

    return (
        <div>
            <Navbar />
            <button onClick={handleLogout}>Logout</button>
            <h2>Dashboard</h2>
            <p>Welcome to the dashboard!</p>
            {uploadSuccess && <p>File uploaded successfully!</p>}
            <div>
                <input type="file" onChange={handleFileChange}/>
                <button onClick={handleUpload} disabled={!selectedFile}>
                    Upload
                </button>
                <p style={{color: 'gray', fontSize: '0.8em', marginTop: '0.5em'}}>
                    (Accepted file types: PDF, DOCX, TXT, HTML)
                </p>
            </div>
        </div>
    );
}

export default Dashboard;