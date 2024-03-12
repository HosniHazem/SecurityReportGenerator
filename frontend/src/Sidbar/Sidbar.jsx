import { useEffect, useRef, useState } from 'react';
import { Link, Navigate, useLocation, useNavigate } from 'react-router-dom';
import './sidebar.scss';
// import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
// const sidebarNavItems = [
//     {
//         display: 'Dashboard',
//         icon: <i className='bx bx-home'></i>,
//         to: '/dashboard',
//         section: 'dashboard'
//     },
//     {
//         display: 'Projects',
//         icon: <i className='bx bx-receipt'></i>,
//         to: '/project',
//         section: 'project'
//     },
//     {
//         display: 'Customers',
//         icon: <i className='bx bx-user'></i>,
//         to: '/customer',
//         section: 'customer'
//     },
//     // {
//     //     display: 'Database Tables',
//     //     // icon: <FontAwesomeIcon icon="fa-solid fa-table" />,
//     //     to: '/tables',
//     //     section: 'tables'
//     // }
 
// ]

const Sidebar = () => {
    const [activeIndex, setActiveIndex] = useState(0);
    const [stepHeight, setStepHeight] = useState(0);
    const sidebarRef = useRef();
    const indicatorRef = useRef();
    const location = useLocation();
    const navigate=useNavigate();
    const handleLogout=()=>{
        localStorage.removeItem('token');
        navigate('/login')
        location.reload() 
        
    }
    const sidebarNavItems = [
        {
            display: 'Dashboard',
            icon: <i className='bx bx-home'></i>,
            to: '/dashboard',
            section: 'dashboard'
        },
        {
            display: 'Projects',
            icon: <i className='bx bx-receipt'></i>,
            to: '/project',
            section: 'project'
        },
        {
            display: 'Customers',
            icon: <i className='bx bx-user'></i>,
            to: '/customer',
            section: 'customer'
        },
        {
            display:'History',
            icon: <i class='bx bx-history'></i>,
            to: '/logs',
            section: 'customer'


        },
        {
            display: 'Logout',
             icon: <i class='bx bx-log-out'></i>,

             onClick: handleLogout
        }
     
    ]
    

    useEffect(() => {
        setTimeout(() => {
            const sidebarItem = sidebarRef.current.querySelector('.sidebar__menu__item');
            indicatorRef.current.style.height = `${sidebarItem.clientHeight}px`;
            setStepHeight(sidebarItem.clientHeight);
        }, 50);
    }, []);

    // change active index
    useEffect(() => {
        const curPath = window.location.pathname.split('/')[1];
        const activeItem = sidebarNavItems.findIndex(item => item.section === curPath);
        setActiveIndex(curPath.length === 0 ? 0 : activeItem);
    }, [location]);

    return <div className='sidebar'>
        <div className="sidebar__logo">
             Web App 2 
        </div>
        <p className="version">V0.80</p>

        <div ref={sidebarRef} className="sidebar__menu">
            <div
                ref={indicatorRef}
                className="sidebar__menu__indicator"
                style={{
                    transform: `translateX(-50%) translateY(${activeIndex * stepHeight}px)`
                }}
            ></div>
            {
                sidebarNavItems.map((item, index) => (
                    <Link to={item.to} key={index} onClick={item?.onClick}>
                        <div className={`sidebar__menu__item ${activeIndex === index ? 'active' : ''}`}>
                            <div className="sidebar__menu__item__icon">
                                {item.icon}
                            </div>
                            <div className="sidebar__menu__item__text">
                                {item.display}
                            </div>
                        </div>
                    </Link>
                ))
            }
        </div>
    </div>;
};

export default Sidebar;