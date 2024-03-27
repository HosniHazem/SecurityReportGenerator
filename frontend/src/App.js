import "./App.scss";
import "boxicons/css/boxicons.min.css";
import { BrowserRouter, Routes, Route, useNavigate,Navigate } from "react-router-dom";
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
import Nessus2 from "./Nessus2";
import AddGlbPip from "./GlbPip/AddGlbPip";
import toast, { Toaster } from 'react-hot-toast';
import ViewGlbPip from "./GlbPip/ViewGlbPip";
import Quality from "./QualityTable/Quality";
import ModifyGlbPip from "./GlbPip/ModfiyGlbPip";
import AddAuditPreviousAudit from "./AuditPreviousAudit/AddAuditPreviousAudit";
import ViewAuditPRevious from "./AuditPreviousAudit/ViewAuditPreviousAudit";
import ModifyAuditPreviousAudit from "./AuditPreviousAudit/ModifyAuditPreviousAudit";
import AnsiReport from "./AnsiReport";
import Anomalie from "./Anomalie";
import AfterANomalie from "./AfterAnomalie";
import TablesClone from "./Tables";
import CreateCustomerSite from "./CustomerSites";
import Register from "./Register";
import Login from "./Login";
import AllCustomerSites from "./CustomerSites/allCustomerSites";
import ModifyCustomerSite from "./CustomerSites/ModifyCustomerSite";
import PrivateRoutes from "./PrivateRoutes";
import Logs from "./Logs";
import AllRmProcess from "./RmProccessusDomains/ViewRmProcessDomains";
import AddRmProccess from "./RmProccessusDomains/AddRmProccessDomains";
import CreateUser from "./Users";
import AllVulns from "./Vuln/AllVuln";
import AddVuln from "./Vuln/AddVuln";
// import AllRmProcess from "./RmProccessusDomains/AddRmProcessDomains";

function App() {
  // const isAuthenticated = localStorage.getItem('token');


  return (
    <BrowserRouter>
    <Toaster
  position="top-center"
  reverseOrder={false}
/>
      <Routes>
      <Route element={<PrivateRoutes />}>

        <Route path="/" element={<AppLayout />}>
          <Route index element={<Dashboard />} />
          <Route path="/dashboard" element={<Dashboard />} />
          <Route path="/project" element={<Project />} />
          <Route path="/customer" element={<Customer />} />
        </Route>
        <Route path="/updatecustomer/:id" element={<UpdateCustomer/>} />
        <Route path="/updateproject/:id" element={<UpdateProject />} />
        <Route path="/newcustomer" element={<AddCustomer />} />
        <Route path="/newproject" element={<AddProject />} />
        <Route path="/import" element={<Nessus />} />
        <Route path="/import2" element={<Nessus2 />} />
        <Route path="/sow/:id" element={<Sow />} />
        <Route path="/add-glb-pip/:customerID" element={<ViewGlbPip/>} />
        <Route path='/ajout-glb-pip/:id' element={<AddGlbPip />} /> 
        <Route path="/quality/:id" element={<Quality />} />
        <Route path="/modify-glb-pip/:id" element={<ModifyGlbPip />} />
        <Route path="/add-audit-previous-audit/:id" element={<AddAuditPreviousAudit />} />
        <Route path="/all-audit-previous-audit/:projectId" element={<ViewAuditPRevious />} />
        <Route path="/all-audit-previous-audit/:id/modify-audit-previous-audit/:id" element={< ModifyAuditPreviousAudit/>} />
        <Route path="/ansi-report/:id" element={<AnsiReport />} />
        <Route path="/anomalie/:id" element={<Anomalie />} />
        <Route path="/tables" element={<TablesClone />} />
        <Route path="/sites/:id" element={<CreateCustomerSite />} />
        <Route path="/sites/:id/customer-sites/:customerID" element={<AllCustomerSites />} />
         <Route path="sites/:id/customer-sites/:customerID/customer-sites-modify/:customerSiteId"  element={<ModifyCustomerSite />} />
         <Route path="logs" element={<Logs />} />
         <Route path="/all-rm-processus/:idIteration" element={<AllRmProcess />} />
         <Route path="/add-rm-proccessus/:idIteration" element={<AddRmProccess />} />
         <Route path="/users" element={<CreateUser />} />
         <Route path="/all-vuln/:id" element={<AllVulns />} />
         <Route path="/add-vuln/:id" element={<AddVuln />} />


         </Route>
         <Route path="/login" element ={<Login />}  />


      </Routes>
    </BrowserRouter>
  );
}

export default App;
