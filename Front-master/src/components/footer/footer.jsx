import React from 'react';
import './footer.css';
const Footer = () => {
 return (
    <footer className="footer">
      <div className="footer__addr">
        <h1 className="footer__logo">Something</h1>
        <h2>Contact</h2>
        <address>
          5534 Somewhere In. The World 22193-10212<br />
          <a className="footer__btn" href="mailto:example@gmail.com">Email Us</a>
        </address>
        
      </div>
      
      <ul className="footer__nav">
       
        <li className="nav__item">
          <h2 className="nav__title">Legal</h2>
          <ul className="nav__ul">
            <li><a href="#">Privacy Policy</a></li>
            <li><a href="#">Terms of Use</a></li>
            <li><a href="#">Sitemap</a></li>
          </ul>
        </li>
        <div className='aboutsection'>
        <p>About :</p>
        <p>aaaaa</p>
        </div>
      </ul>
      <div className="legal">
        <p>&copy; 2019 Something. All rights reserved.</p>
        <div className="legal__links">
          <span>Made with <span className="heart">♥</span> remotely from Anywhere</span>
        </div>
        
      </div>
    </footer>
 );
};

export default Footer;
