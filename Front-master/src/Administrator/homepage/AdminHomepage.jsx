import React, {useState} from 'react';
import { Link, useNavigate } from 'react-router-dom';
import './AdminHomepage.css';

const AdminHomepage = () => {
    const [selectedFile, setSelectedFile] = useState(null);
    const [uploadSuccess, setUploadSuccess] = useState(false);

    const navigate = useNavigate();
    const handleLogout = () => {
        localStorage.removeItem('token');
        navigate('/');
    };
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
                <div className="content">
                    <div className="upload-container">
                    <input type="file" onChange={handleFileChange} className="file-input"/>
                    <button className="upload-button" onClick={handleUpload} disabled={!selectedFile}>
                        Upload
                    </button>
                    </div>
                    {uploadSuccess && <p className="success-message">File uploaded successfully!</p>}
                    <big><p className="file-name">{selectedFile ? selectedFile.name : 'No file selected'}</p></big>
                    <big><big><p className="file-type-info">(Accepted file types: PDF, DOCX, TXT, HTML)</p></big></big>
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

export default AdminHomepage;