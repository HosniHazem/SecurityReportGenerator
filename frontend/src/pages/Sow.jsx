import React, { useState } from 'react';
import './SOW.css'; // Import your CSS file for styling
import swal from 'sweetalert';
import axios from 'axios';
import { useParams , Link } from 'react-router-dom';



function SOW() {
  const [serveur, setServeur] = useState([]);
  const [r_s, setRS] = useState([]);
  const [pc, setPC] = useState([]);
  const [apps, setApps] = useState([]);
  const [serveurInput, setServeurInput] = useState(null);
  const [r_sInput, setRSInput] = useState(null);
  const [pcInput, setPCInput] = useState(null);
  const [appsInput, setAppsInput] = useState(null);
  const [Button, setButton] = useState(null);
  
  const tableCellStyle = {
    textAlign: 'center',
    verticalAlign: 'middle',
  };
  const { id } = useParams();

  const generateJSON = () => {
    const generateObjects = (content,type) => {
        const fields = Object.keys(content);
      
        let maxLines = Math.max(...fields.map((field) => {
          // Ensure that content[field] is a string before using split
          return typeof content[field] === 'string' ? content[field].split('\n').length : 0;
        }));
      
        let resultArray = [];
      
        for (let i = 0; i < maxLines; i++) {
          const newObj = {};
      
          for (const field of fields) {
            // Ensure that content[field] is a string before using split
            const lines = typeof content[field] === 'string' ? content[field].split('\n') : [];
      
            newObj[field] = lines[i] || '';
            newObj["Type"] = type;
          }
      
          resultArray.push(newObj);
        }
      
        return resultArray;
      };
    const appsArray = generateObjects(apps,"Apps"); 
    console.log(appsArray)
    const srvArray = generateObjects(serveur,"Serveur"); 
    const rsArray = generateObjects(r_s,"R_S"); 
    const pcArray = generateObjects(pc,"PC"); 
    let pcsubnetArray = [];
  pcArray.map((item) => {
const subnet = getSubnetIpRange(item.IP_Host);
subnet.map((ip)=>{
    const jsonObject = {
        Nom : item.Nom,
        IP_Host: ip,
        Type : "PC"
      };
pcsubnetArray.push(jsonObject);

})
  }); 

//test
function removeFromPcsubnetArray(pcsubnetArray, ipHostToRemove) {
    return pcsubnetArray.filter((pc) => pc.IP_Host !== ipHostToRemove);
  }
  
  function removeDuplicatesFromPcsubnetArray(pcsubnetArray, ipHostArray) {
    return pcsubnetArray.filter((pc) => !ipHostArray.includes(pc.IP_Host));
  }
  

  
  // Check and remove duplicates from pcsubnetArray
  appsArray.forEach((app) => {
    pcsubnetArray = removeFromPcsubnetArray(pcsubnetArray, app.IP_Host);
  });
  
  srvArray.forEach((srv) => {
    pcsubnetArray = removeFromPcsubnetArray(pcsubnetArray, srv.IP_Host);
  });
  
  rsArray.forEach((rs) => {
    pcsubnetArray = removeFromPcsubnetArray(pcsubnetArray, rs.IP_Host);
  });

  setServeurInput(srvArray);
  setRSInput(rsArray);
  setAppsInput(appsArray);
  setPCInput(pcsubnetArray);

    
      setButton("yes");


  };


  const handleChange = (e,set,val) => {
    e.persist();
 
    set({...val, [e.target.name]: e.target.value });
}

const imported = () => {

    let parsedData = {};
    parsedData.serveur = serveurInput;
    parsedData.apps = appsInput;
    parsedData.pc = pcInput;
    parsedData.rs = r_sInput;
    parsedData.project_id = id;
console.log(parsedData);
/*    axios.post('http://webapp.smartskills.tn/AppGenerator/backend/api/Sow/import',parsedData)
  .then((response) => {
    if(response.data.status===200){
      swal("Imported","Successfully");

    }else if(response.data.status===404) {
      swal("Error","Problem while importing");
    }
  })
  .catch((error) => {
    // Handle error
    console.error('Error sending data:', error);
    swal("Error","Problem while importing");
  })   */
}
const getSubnetIpRange = (cidr) => {

  // Split CIDR into IP and subnet mask

  const [ip, subnet] = cidr.split('/');
  if(cidr.includes("/")) {
  // Calculate host bits from mask 
  const mask = parseInt("24");
  const hostBits = 32 - mask;

  // Calculate number of IP addresses in range
  const numHosts = Math.pow(2, hostBits) - 2;  


  // Generate IP addresses
  const ipParts = ip.split('.').map(d => parseInt(d));
  const subnets = [];
 
  for (let i = 1; i <= numHosts; i++) {
    const octets = ipParts.slice(); 
    octets[3] = i;

    subnets.push(octets.join('.'));
  }
  return subnets;
}else {
  return [ip]
}
  

}

/*   const adjustTextareaHeight = (id) => {
    const textarea = document.getElementById(id);
    textarea.rows = textarea.value.split('\n').length;
  }; */

  return (
    <div className="sow-container">
      <h2>SOW Import</h2>

      <div className="textarea-container">

        <table border="1" style={{ borderCollapse: 'separate', borderSpacing: '0' }}>
            <tr>
                <td colSpan={7}>
                   <strong> Application </strong>
                </td>
            </tr>

            <tr>
                <td style={tableCellStyle}>Nom</td>
                <td style={tableCellStyle}>Modules</td>
                <td style={tableCellStyle}>Description</td>
                <td style={tableCellStyle}>Environnement de developpement</td>
                <td style={tableCellStyle}>Developee par /Annee</td>
               <td style={tableCellStyle}>Noms ou @IP des serveurs d'hebergement</td>
               <td style={tableCellStyle}>Nombre d'utilisateurs</td>
            </tr>
            <tr>
                <td style={tableCellStyle}> <textarea
          id="Nom"
          name="Nom"
          value={apps.Nom}
        onChange={(e) => handleChange(e,setApps,apps)}  
        ></textarea></td>
                <td style={tableCellStyle}> <textarea
          id="field3"
          name="field3"
          value={apps.field3}
        onChange={(e) => handleChange(e,setApps,apps)}  
        ></textarea></td>
                <td style={tableCellStyle}> <textarea
          id="field4"
          name="field4"
          value={apps.field4}
        onChange={(e) => handleChange(e,setApps,apps)}  
        ></textarea></td>
                <td style={tableCellStyle}> <textarea
          id="field5"
          name="field5"
          value={apps.field5}
        onChange={(e) => handleChange(e,setApps,apps)}  
        ></textarea></td>
                <td style={tableCellStyle}> <textarea
          id="dev_by"
          name="dev_by"
          value={apps.dev_by}
        onChange={(e) => handleChange(e,setApps,apps)}  
        ></textarea></td>
                <td style={tableCellStyle}> <textarea
          id="IP_Host"
          name="IP_Host"
          value={apps.IP_Host}
        onChange={(e) => handleChange(e,setApps,apps)}  
        ></textarea></td>
                <td style={tableCellStyle}> <textarea
          id="Number_users"
          name="Number_users"
          value={apps.Number_users}
        onChange={(e) => handleChange(e,setApps,apps)}  
        ></textarea></td>
            </tr>
        </table>








      </div>

      <div className="textarea-container">
      <table border="1" style={{ borderCollapse: 'separate', borderSpacing: '0' }}>
            <tr>
                <td colSpan={6}>
                <strong>   Serveurs (par platforme)</strong>
                </td>
            </tr>
            <tr>
                <td style={tableCellStyle}>Nom</td>
                <td style={tableCellStyle}>@IP</td>
                <td style={tableCellStyle}>Type</td>
                <td style={tableCellStyle}>Systeme d'exploitation</td>
                <td style={tableCellStyle}>Role/metier</td>
            </tr>
            <tr>
                <td style={tableCellStyle}> <textarea
          id="Nom"
          name="Nom"
          value={serveur.Nom}
        onChange={(e) => handleChange(e,setServeur,serveur)}  
        ></textarea></td>
                <td style={tableCellStyle}> <textarea
          id="IP_Host"
          name="IP_Host"
          value={serveur.IP_Host}
        onChange={(e) => handleChange(e,setServeur,serveur)}  
        ></textarea></td>
                <td style={tableCellStyle}> <textarea
          id="field3"
          name="field3"
          value={serveur.field3}
        onChange={(e) => handleChange(e,setServeur,serveur)}  
        ></textarea></td>
                <td style={tableCellStyle}> <textarea
          id="field4"
          name="field4"
          value={serveur.field4}
        onChange={(e) => handleChange(e,setServeur,serveur)}  
        ></textarea></td>
                <td style={tableCellStyle}> <textarea
          id="field5"
          name="field5"
          value={serveur.field5}
        onChange={(e) => handleChange(e,setServeur,serveur)}  
        ></textarea></td>
              
            </tr>
        </table>
      </div>

      <div className="textarea-container">
      <table border="1" style={{ borderCollapse: 'separate', borderSpacing: '0' }}>
            <tr>
                <td colSpan={8}>
                <strong>   Infrastructure Réseau et sécurité </strong>
                </td>
            </tr>
            <tr>
                <td style={tableCellStyle}>Nom</td>
                <td style={tableCellStyle}>@IP</td>
                <td style={tableCellStyle}>Nature</td>
                <td style={tableCellStyle}>Marque</td>
                <td style={tableCellStyle}>Nombre</td>
                <td style={tableCellStyle}>Aministré par:</td>
                <td style={tableCellStyle}>Observations</td>
            </tr>
            <tr>
                <td style={tableCellStyle}> <textarea
          id="Nom"
          name="Nom"
          value={r_s.Nom}
        onChange={(e) => handleChange(e,setRS,r_s)}  
        ></textarea></td>
                <td style={tableCellStyle}> <textarea
          id="IP_Host"
          name="IP_Host"
          value={r_s.IP_Host}
        onChange={(e) => handleChange(e,setRS,r_s)}  
        ></textarea></td>
                <td style={tableCellStyle}> <textarea
          id="dev_by"
          name="dev_by"
          value={r_s.dev_by}
        onChange={(e) => handleChange(e,setRS,r_s)}  
        ></textarea></td>
                <td style={tableCellStyle}> <textarea
          id="URL"
          name="URL"
          value={r_s.URL}
        onChange={(e) => handleChange(e,setRS,r_s)}  
        ></textarea></td>
                <td style={tableCellStyle}> <textarea
          id="field3"
          name="field3"
          value={r_s.field3}
        onChange={(e) => handleChange(e,setRS,r_s)}  
        ></textarea></td>
                <td style={tableCellStyle}> <textarea
          id="field4"
          name="field4"
          value={r_s.field4}
        onChange={(e) => handleChange(e,setRS,r_s)}  
        ></textarea></td>
                <td style={tableCellStyle}> <textarea
          id="field5"
          name="field5"
          value={r_s.field5}
        onChange={(e) => handleChange(e,setRS,r_s)}  
        ></textarea></td>
              
            </tr>
        </table>
      </div>
      <div className="textarea-container">
      <table border="1" style={{ borderCollapse: 'separate', borderSpacing: '0' }}>
            <tr>
                <td colSpan={2}>
                <strong>   Postes de travail </strong>
                </td>
            </tr>
            <tr>
                <td style={tableCellStyle}>Nom </td>
                <td style={tableCellStyle}>@IP or Subnet</td>
            
            </tr>
            <tr>
                <td style={tableCellStyle}> <textarea
          id="Nom"
          name="Nom"
          value={pc.Nom}
        onChange={(e) => handleChange(e,setPC,pc)}  
        ></textarea></td>
   

                <td style={tableCellStyle}> <textarea
          id="IP_Host"
          name="IP_Host"
          value={pc.IP_Host}
        onChange={(e) => handleChange(e,setPC,pc)}  
        ></textarea></td>
              
              
            </tr>
        </table>
      </div>
      <Link to={`/`} style={{ textDecoration: "none" }}>
      <button className='button3'>Back</button>
          </Link>

      <button  onClick={generateJSON}>Generate</button>
 
      {
  Button ?   <button className='button2' onClick={imported}>Import</button>
 : null
} 

    </div>
  );
}

export default SOW;
