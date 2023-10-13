import React, { useState } from 'react';
import './SOW.css'; // Import your CSS file for styling
import ipAddress from 'ip-address';

function SOW() {
  const [serveur, setServeur] = useState([]);
  const [r_s, setRS] = useState([]);
  const [pc, setPC] = useState([]);
  const [Apps, setApps] = useState([]);
  const [serveurInput, setServeurInput] = useState(null);
  const [r_sInput, setRSInput] = useState(null);
  const [pcInput, setPCInput] = useState(null);
  const [appsInput, setAppsInput] = useState(null);
  const [ipJson, setIpJson] = useState(null);
  const tableCellStyle = {
    textAlign: 'center',
    verticalAlign: 'middle',
  };
  const generateJSON = () => {
    const generateObjects = (content) => {
        const fields = Object.keys(content);
     
        const maxLines = Math.max(...fields.map((field) => content[field].split('\n').length));
      
        const resultArray = [];
      
        for (let i = 0; i < maxLines; i++) {
          const newObj = {};
      

          for (const field of fields) {
            const lines = content[field].split('\n');
      
 
            newObj[field] = lines[i] || '';
          }
      
          resultArray.push(newObj);
        }
      
        return resultArray;
      };

    //const appsArray = generateObjects(Apps); 
    //const srvArray = generateObjects(serveur); 
   // const rsArray = generateObjects(r_s); 
    const pcArray = generateObjects(pc); 
  
  
 

      
    function getIpRange(subnet) {
        // Get base IP and range 
        const [baseIp, ...range] = subnet.split('/');
      
        // Generate array from range 
        const ipRange = range.map(num => {
          const ipParts = baseIp.split('.');
          ipParts[3] = num;
          return ipParts.join('.');
        });
      
        ipRange.unshift(baseIp); // Add the base IP to the beginning of the array
        return ipRange;
      }
      
      const subnet = "192.168.20.11/10/44/246/247/248/251/249";
      const ipRange = getIpRange(subnet);
      console.log(ipRange);

 
  };
  const handleChange = (e,set,val) => {
    e.persist();
 
    set({...val, [e.target.name]: e.target.value });
}

const handleServeurInputChange = (id) => {

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
          value={Apps.Nom}
        onChange={(e) => handleChange(e,setApps,Apps)}  
        ></textarea></td>
                <td style={tableCellStyle}> <textarea
          id="field3"
          name="field3"
          value={Apps.field3}
        onChange={(e) => handleChange(e,setApps,Apps)}  
        ></textarea></td>
                <td style={tableCellStyle}> <textarea
          id="field4"
          name="field4"
          value={Apps.field4}
        onChange={(e) => handleChange(e,setApps,Apps)}  
        ></textarea></td>
                <td style={tableCellStyle}> <textarea
          id="field5"
          name="field5"
          value={Apps.field5}
        onChange={(e) => handleChange(e,setApps,Apps)}  
        ></textarea></td>
                <td style={tableCellStyle}> <textarea
          id="dev_by"
          name="dev_by"
          value={Apps.dev_by}
        onChange={(e) => handleChange(e,setApps,Apps)}  
        ></textarea></td>
                <td style={tableCellStyle}> <textarea
          id="URL"
          name="URL"
          value={Apps.URL}
        onChange={(e) => handleChange(e,setApps,Apps)}  
        ></textarea></td>
                <td style={tableCellStyle}> <textarea
          id="Number_users"
          name="Number_users"
          value={Apps.Number_users}
        onChange={(e) => handleChange(e,setApps,Apps)}  
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


      <button onClick={generateJSON}>Generate JSON</button>
      {
  serveurInput ? (
    <div>
      <label htmlFor="Serveur">Serveur:</label>
      {serveurInput.map((item, index) => (
        <div key={index}>
          <textarea
            id={`serveur-${index}`}
            value={item.IP_Host}
            onChange={(e) => handleServeurInputChange(e, index)}
          ></textarea>
        </div>
      ))}
    </div>
  ) : null
}

    </div>
  );
}

export default SOW;
