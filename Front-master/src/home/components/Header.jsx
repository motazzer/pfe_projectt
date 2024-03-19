import React from 'react';
import homeback from '../homeback.jpg';
import gg from '../gg.png';

const Header = () => {
    return (
        <header className="py-5 bg-image-full" style={{backgroundImage: `url(${homeback})`}}>
            <div className="text-center my-5">
                <img className="img-fluid rounded-circle mb-4" src={gg} alt="..." /> {/* Use the imported image */}
                <h1 className="text-white fs-3 fw-bolder">Full Width Pics</h1>
                <p className="text-white-50 mb-0">Landing Page Template</p>
            </div>
        </header>
    );
}

export default Header;