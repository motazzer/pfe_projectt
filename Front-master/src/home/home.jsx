import React from 'react';
import './css/home.css';
import Navbar from './components/Navbar';
import Header from './components/Header';
import ContentSection from './components/ContentSection';
import Footer from './components/Footer';

const Home = () => {
 return (
  <>
    <div>
        <Navbar />
        <Header />
        <ContentSection />
        <Footer />
    </div>
    </>
 );
};

export default Home;
