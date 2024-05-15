import { Outlet, Navigate } from 'react-router-dom'
import { useDispatch, useSelector } from "react-redux";
import { useEffect, useState } from 'react';
import { getMe } from '../ReduxToolkit/userSlice';

const PrivateRoutes = () => {
    const dispatch = useDispatch();
    const { profile, loading } = useSelector((state) => state.user);
    console.log("user", profile);

    useEffect(() => {
        dispatch(getMe());
    }, [dispatch]);
    
    const isLoggedIn=localStorage.getItem('token'); 
    
    // console.log("is logged",isLoggedIn);

    // Check if user state is not yet available
  

    return isLoggedIn ? <Outlet/> : <Navigate to="/login"/>;
}

export default PrivateRoutes;
