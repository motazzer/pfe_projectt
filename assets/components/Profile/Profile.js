import React, { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import Navbar from '../Navbar';

const Profile = () => {
    const [userData, setUserData] = useState(null);

    useEffect(() => {
        const token = localStorage.getItem('token');

        fetch(`/api/user`, {
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
                setUserData(data);
            })
            .catch(error => {
                console.error('Error fetching user details:', error);
            });
    }, []);

    if (!userData) {
        return <div>Loading...</div>;
    }

    return (
        <div>
            <Navbar/>
            <h2>User Profile</h2>
            <p>First Name: {userData.firstname}</p>
            <p>Last Name: {userData.lastname}</p>
            <p>Email: {userData.email}</p>
            <p>Joined At: {userData.createdAt}</p>
            <p>Uploaded Files Count: {userData.uploadedFilesCount}</p>
            <Link to="/update-profile">
                <button>Update Profile</button>
            </Link>
        </div>
    );
}

export default Profile;

