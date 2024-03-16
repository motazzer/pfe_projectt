import React, { useEffect, useState } from 'react';
import { useNavigate } from 'react-router-dom';

function Login() {
    const [formData, setFormData] = useState({
        email: '',
        password: '',
    });
    const [errorMessage, setErrorMessage] = useState('');
    const navigate = useNavigate();

    useEffect(() => {
        if (isAuthenticated()) {
            const userRole = getUserRole();
            if (userRole === 'ROLE_ADMIN') {
                navigate('/administrator');
            } else if (userRole === 'ROLE_USER') {
                navigate('/dashboard');
            }
        }
    }, []);

    const handleChange = (e) => {
        const { name, value } = e.target;
        setFormData({ ...formData, [name]: value });
    };

    const handleSubmit = async (e) => {
        e.preventDefault();

        try {
            const response = await fetch('/api/login_check', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(formData),
            });

            if (!response.ok) {
                throw new Error('Failed to login');
            }

            const responseData = await response.json();
            const token = responseData.token;

            localStorage.setItem('token', token);

            const userRole = getUserRole();
            if (userRole === 'ROLE_ADMIN') {
                navigate('/administrator');
            } else if (userRole === 'ROLE_USER') {
                navigate('/dashboard');
            }
        } catch (error) {
            console.error('Error:', error);
            setErrorMessage('Invalid email or password. Please try again.');
        }
    };

    return (
        <div>
            <h2>Login</h2>
            <form onSubmit={handleSubmit}>
                <div>
                    <label htmlFor="email">Email:</label>
                    <input type="email" id="email" name="email" value={formData.email} onChange={handleChange} />
                </div>
                <div>
                    <label htmlFor="password">Password:</label>
                    <input type="password" id="password" name="password" value={formData.password} onChange={handleChange} />
                </div>
                {errorMessage && <p style={{ color: 'red' }}>{errorMessage}</p>}
                <button type="submit">Login</button>
            </form>
        </div>
    );
}

const isAuthenticated = () => {
    return localStorage.getItem('token') !== null;
};

const getUserRole = () => {
    const token = localStorage.getItem('token');
    if (!token) return null;
    const decodedToken = JSON.parse(atob(token.split('.')[1]));
    return decodedToken.roles[0];
};

export default Login;
