import React from 'react';
import {Link} from "react-router-dom";
import '../styles/Home.css';

class Home extends React.Component {
    render() {
        return (
            <div className="home-container">
                <h1>Welcome to Your Education Assistant!</h1>
                <div>
                    <Link to="/login">Login</Link>
                    <Link to="/signup">Sign Up</Link>
                </div>
                <p>
                    Our intelligent assistant is here to help you with your studies, provide personalized support,
                    and offer interactive educational resources.
                </p>
            </div>
        );
    }
}

export default Home;