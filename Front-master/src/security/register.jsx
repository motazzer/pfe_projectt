import React, { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import './globale.css';
import '../home/css/home.css';
import Navbar from "../home/components/Navbar";
import Footer from "../home/components/Footer";

function Signup() {
    const [formData, setFormData] = useState({
        firstname: '',
        lastname: '',
        email: '',
        password: '',
    });
    const [passwordError, setPasswordError] = useState('');
    const navigate = useNavigate();
    const handleChange = (e) => {
        const { name, value } = e.target;
        setFormData({ ...formData, [name]: value });
    };

    const validatePassword = (password) => {
        // Regular expression for password validation
        const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{8,}$/;
        return passwordRegex.test(password);
    };

    const handleSubmit = async (e) => {
        e.preventDefault();

        if (!validatePassword(formData.password)) {
            setPasswordError('Password must be at least 8 characters long and contain at least one uppercase letter, one lowercase letter, and one number');
            return;
        }

        try {
            const response = await fetch(' https://127.0.0.1:8000/register', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(formData),
            });

            if (!response.ok) {
                throw new Error('Failed to register');
            }
            navigate('/login');
        } catch (error) {
            console.error('Error:', error);
        }
    };

    return (
        <div>
            <Navbar />
        <div className="register-container">
            <h1>Inscrivez-vous</h1>
            <form onSubmit={handleSubmit}>
                <input type="text" placeholder="Nom" name="firstname" value={formData.firstname} onChange={handleChange} />
                <input type="text" placeholder="Prénom" name="lastname" value={formData.lastname} onChange={handleChange} />
                <input type="email" placeholder="Adresse e-mail" name="email" value={formData.email} onChange={handleChange} />
                <input type="password" placeholder="Mot de passe" name="password" value={formData.password} onChange={handleChange} />
                {passwordError && <p style={{ color: 'red' }}>{passwordError}</p>}
                <button type="submit">S'inscrire</button>
            </form>
        </div>
            <Footer />
        </div>
    );
}

export default Signup;
