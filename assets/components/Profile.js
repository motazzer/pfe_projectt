import React from 'react';
import Navbar from './Navbar';
class Profile extends React.Component {
    render() {
        return (
            <div>
                <Navbar/>
                <p>Welcome to the Profile!</p>
            </div>
        );
    }
}

export default Profile;