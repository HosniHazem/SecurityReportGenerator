import "./App.scss";
import "boxicons/css/boxicons.min.css";
import { BrowserRouter, Routes, Route } from "react-router-dom";
import AppLayout from "./layout/AppLayout";
import Dashboard from "./dashboard/Dashboard";
import Project from "./projects/Projects";
import Customer from "./customer/CreateCustomer";
import AddCustomer from "./customer/AddCustomer";
import AddProject from "./projects/AddProject";
import UpdateCustomer from "./customer/UpdateCustomer";
import UpdateProject from "./projects/UpdateProject";
import Sow from "./pages/Sow";
import Nessus from "./Nessus";
import AddGlbPip from "./GlbPip/AddGlbPip";

function App() {
  return (
    <BrowserRouter>
      <Routes>
        <Route path="/" element={<AppLayout />}>
          <Route index element={<Dashboard />} />
          <Route path="/dashboard" element={<Dashboard />} />
          <Route path="/project" element={<Project />} />
          <Route path="/customer" element={<Customer />} />
        </Route>
        <Route path="/updatecustomer/:id" element={<UpdateCustomer />} />
        <Route path="/updateproject/:id" element={<UpdateProject />} />
        <Route path="/newcustomer" element={<AddCustomer />} />
        <Route path="/newproject" element={<AddProject />} />
        <Route path="/import" element={<Nessus />} />
        <Route path="/sow/:id" element={<Sow />} />
        <Route path="/glb-pip" element={<AddGlbPip />} />
      </Routes>
    </BrowserRouter>
  );
}

export default App;
