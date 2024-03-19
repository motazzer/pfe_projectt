import React, { useState, useEffect } from 'react';
import Navbar from '../components/navbar/navbar';

const UpdateProfile = () => {
    const [formData, setFormData] = useState({
        firstname: '',
        lastname: '',
        currentPassword: '',
        newPassword: ''
    });
    const [errorMessage, setErrorMessage] = useState('');
    const [passwordError, setPasswordError] = useState('');
    const [Successmessage, setSuccessmessage] = useState('');

    useEffect(() => {
        const token = localStorage.getItem('token');

        fetch(`https://127.0.0.1:8000/api/user`, {
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
                setFormData({
                    firstname: data.firstname,
                    lastname: data.lastname,
                    currentPassword: '', // Clear current password field
                    newPassword: '' // Clear new password field
                });
            })
            .catch(error => {
                console.error('Error fetching user details:', error);
            });
    }, []);

    const handleInputChange = (event) => {
        const { name, value } = event.target;
        setFormData({ ...formData, [name]: value });
    };

    const validatePassword = (password) => {
        // Regular expression for password validation
        const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{8,}$/;
        return passwordRegex.test(password);
    };

    const handleSubmit = (event) => {
        event.preventDefault();
        const token = localStorage.getItem('token');

        setPasswordError('');
        setErrorMessage('');
        setSuccessmessage('');

        if (formData.newPassword && !validatePassword(formData.newPassword)) {
            setPasswordError('New password must be at least 8 characters long and contain at least one uppercase letter, one lowercase letter, and one number');
            return;
        }

        const requestBody = {
            firstname: formData.firstname,
            lastname: formData.lastname,
        };

        if (formData.currentPassword && formData.newPassword) {
            requestBody.currentPassword = formData.currentPassword;
            requestBody.newPassword = formData.newPassword;
        }

        fetch(`/api/update-profile`, {
            method: 'PUT',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(requestBody)
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                setSuccessmessage('Profile Updated successfully');
            })
            .catch(error => {
                console.error('Error updating profile:', error);
                setErrorMessage('Invalid password. Please try again.');
            });
    };

    return (
        <div>
            <Navbar/>
            <h2>Update Profile</h2>
            <form onSubmit={handleSubmit}>
                <div>
                    <label htmlFor="firstname">First Name:</label>
                    <input type="text" id="firstname" name="firstname" value={formData.firstname} onChange={handleInputChange} />
                </div>
                <div>
                    <label htmlFor="lastname">Last Name:</label>
                    <input type="text" id="lastname" name="lastname" value={formData.lastname} onChange={handleInputChange} />
                </div>
                <div>
                    <label htmlFor="currentPassword">Current Password:</label>
                    <input type="password" id="currentPassword" name="currentPassword" value={formData.currentPassword} onChange={handleInputChange} />
                </div>
                <div>
                    <label htmlFor="newPassword">New Password:</label>
                    <input type="password" id="newPassword" name="newPassword" value={formData.newPassword} onChange={handleInputChange} />
                    {passwordError && <p style={{ color: 'red' }}>{passwordError}</p>}
                    {errorMessage && <p style={{ color: 'red' }}>{errorMessage}</p>}
                    {Successmessage && <p style={{ color: 'green' }}>{Successmessage}</p>}
                </div>
                <button type="submit">Update</button>
            </form>
        </div>
    );
}

export default UpdateProfile;
