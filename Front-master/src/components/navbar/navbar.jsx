import React, { memo } from 'react';
import './navbar.css'; 

const Navbar = memo(function Navbar({ className = '' }) {
 return (
    <div className="root">
      <div className="frame4"></div>
      <div className="frame1">
        <a href="/" className="home">Home</a>
        <a href="/help" className="help">Help</a>
        <a href="/reservation" className="reservation">Reservation</a>
        <a href="/contactus" className="contactUs">Contact Us</a>
      </div>
      <div className="frame3">
        <div className="frame2">
          <a href="/login" className="signIn">Sign In</a>
        </div>
        <div className="frame32">
          <a href="/register" className="signUp">Sign Up</a>
        </div>
      </div>
    </div>
 );
});

export default Navbar;
