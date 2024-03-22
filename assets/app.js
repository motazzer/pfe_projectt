import React from 'react';
import ReactDOM from 'react-dom';
import {BrowserRouter, Route, Routes, Navigate} from 'react-router-dom';
import './styles/app.scss'
import Home from './components/home' ;
import Dashboard from "./components/Dashboard";
import Quiz from './components/Quiz';
import Chat from './components/Chat';
import Profile from './components/Profile/Profile';
import Summary from './components/summarysheet';
import AdminHomepage from './components/Administrator/AdminHomepage';
import ManageUsers from './components/Administrator/ManageUsers';
import ManageDocuments from './components/Administrator/ManageDocuments';
import UpdateDocument from './components/Administrator/UpdateDocument';
import DetailsDocument from './components/Administrator/detailsdocument';
import UpdateProfile from './components/profile/UpdateProfile';

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
            <Route path="/dashboard" element={<PrivateRoute element={<Dashboard />} />} />
            <Route path="/quiz" element={<Quiz />} />
            <Route path="/chat" element={<Chat />} />
            <Route path="/profile" element={<Profile />} />
            <Route path="/update-profile" element={<UpdateProfile/>} />
            <Route path="/summarysheet" element={<Summary />} />
            <Route path="/administrator" element={<AdminHomepage/>} />
            <Route path="/administrator/manage-users" element={<ManageUsers/>} />
            <Route path="/administrator/manage-documents" element={<ManageDocuments/>} />
            <Route path="/update-document/:id" element={<UpdateDocument/>} />
            <Route path="/details-document/:id" element={<DetailsDocument/>} />
            <Route path="*" element={<Navigate to="/" />} />
        </Routes>
    </BrowserRouter>,
    document.getElementById( 'root')
);