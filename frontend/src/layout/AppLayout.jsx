import { Outlet } from "react-router-dom";
import Sidebar from "../Sidbar/Sidbar";
import { useSelector } from 'react-redux';

const AppLayout = () => {
    // Select the user profile from the Redux store
    const userProfile = useSelector(state => state.user.profile);

    // Extract the user name from the user profile, assuming user profile contains a 'name' field
    const userName = userProfile ? userProfile.name : 'Guest';

    // Display the user name in the console
    console.log('User Name:', userName);

    return (
        <div style={{
            padding: '50px 0px 0px 370px'
        }}>
            <Sidebar />
            <Outlet />
        </div>
    );
};


export default AppLayout;