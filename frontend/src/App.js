import './App.css';
import { BrowserRouter, Routes, Route } from 'react-router-dom';
import Nessus from './Nessus';
import Project from './projects/Projects';
import Dashboard from './dashboard/Dashboard';
import CreateCustomer from './customer/CreateCustomer';
import AddCustomer from './customer/AddCustomer';
import AddProject from './projects/AddProject';
import UpdateCustomer from './customer/UpdateCustomer';
import UpdateProject from './projects/UpdateProject';


function App() {



    return (
        <BrowserRouter>
            <Routes>
                <Route exact path='/' element={<Project />} />
                    <Route exact path='/import' element={
      <Nessus />
  } />
                    <Route exact path='/project' element={
      <Project />
  } />
                    <Route exact path='/dashboard' element={
      <Dashboard />
  } />
                    <Route exact path='/customer_create' element={
      <CreateCustomer />
  } />
                    <Route exact path='/newcustomer' element={
      <AddCustomer />
  } />
                    <Route exact path='/newproject' element={
      <AddProject />
  } />
                    <Route exact path='/updatecustomer/:id' element={
      <UpdateCustomer />
  } />
                    <Route exact path='/updateproject/:id' element={
      <UpdateProject />
  } />
              
            </Routes>
        </BrowserRouter>
    );
}

export default App;