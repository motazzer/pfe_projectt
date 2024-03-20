import {BrowserRouter, Routes, Route, Navigate, } from "react-router-dom";

import Home  from './home/home';
import Register from "./security/register";
import Login from "./security/login";
import Dashboard from "./user/dashboard/Dashboard";
import Profile from "./Profile/Profile";
import UpdateProfile from "./Profile/UpdateProfile";
import AdminHomepage from "./Administrator/homepage/adminHomepage";
import Profileadmin from "./Administrator/pofileadmin/profileadmin";
import UpdateProfileadmin from "./Administrator/pofileadmin/updateprofileadmin";
import ManageDocuments from "./Administrator/managedocument/ManageDocuments";
import DetailsDocument from "./Administrator/detailsdocument";
import ManageUsers from "./Administrator/manageuser/ManageUsers";
import UpdateDocument from "./Administrator/UpdateDocument";

const isAuthenticated = () => {
    return localStorage.getItem('token') !== null;
};
const PrivateRoute = ({ element }) => {
    return isAuthenticated() ? element : <Navigate to="/login" />;
};

function App() {
  return (
      <BrowserRouter>
        <Routes>
          <Route path="/" element={<Home/>}/>
          <Route path="/register" element={<Register/>}/>
          <Route path="/login" element={<Login/>}/>
          <Route path="/Dashboard" element={<PrivateRoute element={<Dashboard />} />} />
            <Route path="/profile" element={<Profile/>} />
            <Route path="/update-profile" element={<UpdateProfile/>} />
            <Route path="/administrator" element={<AdminHomepage/>} />
            <Route path="/profile-admin" element={<Profileadmin/>} />
            <Route path="/update-profile-admin" element={<UpdateProfileadmin/>} />
            <Route path="/administrator/manage-users" element={<ManageUsers/>} />
            <Route path="/administrator/manage-documents" element={<ManageDocuments/>} />
            <Route path="/update-document/:id" element={<UpdateDocument/>} />
            <Route path="/details-document/:id" element={<DetailsDocument/>} />

            <Route path="*" element={<Navigate to="/" />} />
        </Routes>
      </BrowserRouter>
  );
}

export default App;
