import React, {useState} from 'react';
import { useNavigate } from 'react-router-dom';

function Dashboard() {
    const navigate = useNavigate();
    const [selectedFile, setSelectedFile] = useState(null);

    const handleFileChange = (e) => {
        setSelectedFile(e.target.files[0]);
    };

    const handleUpload = async () => {
        try {
            const formData = new FormData();
            formData.append('file', selectedFile);

            const response = await fetch('/api/course-documents/upload', {
                method: 'POST',
                body: formData,
            });

            if (!response.ok) {
                throw new Error('Failed to upload file');
            }

            console.log('File uploaded successfully');
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
            <h2>Dashboard</h2>
            <p>Welcome to the dashboard!</p>
            <div>
                <input type="file" onChange={handleFileChange}/>
                <button onClick={handleUpload} disabled={!selectedFile}>Upload</button>
            </div>
            <button onClick={handleLogout}>Logout</button>
        </div>
    );
}

export default Dashboard;
