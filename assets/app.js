import React from 'react';
import ReactDOM from 'react-dom';
import {BrowserRouter, Route, Routes, Navigate} from 'react-router-dom';
import Home from './components/home' ;
import Signup from './components/Signup';
import Login from "./components/Login";
import Dashboard from "./components/Dashboard";
import Quiz from './components/Quiz';
import Chat from './components/Chat';
import Profile from './components/Profile';
import Summary from './components/summarysheet';

const isAuthenticated = () => {
    return localStorage.getItem('token') !== null;
};
const PrivateRoute = ({ element }) => {
    return isAuthenticated() ? element : <Navigate to="/login" />;
};

ReactDOM.render(
    <BrowserRouter>
        <Routes>
            <Route path="/" element={<Home />} />
            <Route path="/signup" element={<Signup />} />
            <Route path="/login" element={<Login />} />
            <Route path="/dashboard" element={<PrivateRoute element={<Dashboard />} />} />
            <Route path="/quiz" element={<Quiz />} />
            <Route path="/chat" element={<Chat />} />
            <Route path="/profile" element={<Profile />} />
            <Route path="/summarysheet" element={<Summary />} />
            <Route path="*" element={<Navigate to="/" />} />
        </Routes>
    </BrowserRouter>,
    document.getElementById( 'root')
);