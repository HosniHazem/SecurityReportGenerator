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
import toast, { Toaster } from 'react-hot-toast';
import ViewGlbPip from "./GlbPip/ViewGlbPip";
import Quality from "./QualityTable/Quality";
import ModifyGlbPip from "./GlbPip/ModfiyGlbPip";
import AddAuditPreviousAudit from "./AuditPreviousAudit/AddAuditPreviousAudit";
import ViewAuditPRevious from "./AuditPreviousAudit/ViewAuditPreviousAudit";
import ModifyAuditPreviousAudit from "./AuditPreviousAudit/ModifyAuditPreviousAudit";

function App() {
  return (
    <BrowserRouter>
    <Toaster
  position="top-center"
  reverseOrder={false}
/>
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
        <Route path="/add-glb-pip" element={<AddGlbPip />} />
        <Route path='/all-glb-pip' element={<ViewGlbPip />} /> 
        <Route path="/quality/:id" element={<Quality />} />
        <Route path="/modify-glb-pip/:id" element={<ModifyGlbPip />} />
        <Route path="/add-audit-previous-audit" element={<AddAuditPreviousAudit />} />
        <Route path="/all-audit-previous-audit" element={<ViewAuditPRevious />} />
        <Route path="/all-audit-previous-audit/modify-audit-previous-audit/:id" element={< ModifyAuditPreviousAudit/>} />


      </Routes>
    </BrowserRouter>
  );
}

export default App;
