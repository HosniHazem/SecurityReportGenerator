import React, { useState, useEffect, useCallback } from 'react';
import Box from '@mui/material/Box';
import TextField from '@mui/material/TextField';
import MenuItem from '@mui/material/MenuItem';
import axios from 'axios';
import Button from '@mui/material/Button';
import './App.css';
import Checkbox from '@mui/material/Checkbox';
import FormControlLabel from '@mui/material/FormControlLabel';
import CircularProgress from '@mui/material/CircularProgress';
import swal from 'sweetalert';

export default function SelectTextFields() {

  const [Export_links, setExport_links] = useState(sessionStorage.getItem('Export_links'))
  const project_id = sessionStorage.getItem('project_id');
  const [Folders, setFolders] = useState([]);
  const [Scans, setScans] = useState([]);
  const [createdId, setcreatedId] = useState();
  const [VmSelected, setVmSelected] = useState();
  const [jsonData, setJsonData] = useState([]);
  const [checkedItems, setCheckedItems] = useState(() => {
    // Initialize checkedItems with all items checked by default
    const initialCheckedItems = {};
    jsonData.forEach((item) => {
      initialCheckedItems[item.id] = true;
    });
    return initialCheckedItems;
  });
  const [exporting, setExporting] = useState(false); 

  useEffect(() => {
    axios.get("http://webaudit.smartskills.tn:8000/api/getScan").then((res) => {
      if (res.status === 200) {
        setFolders(res.data.Folders.folders);
        setScans(res.data.Folders.scans);
      }
    }).catch((error) => {
      console.error('Error sending data:', error);
    });
  }, []);

  const [Vm, setVm] = useState(); 

  useEffect(() => {
    axios.get("http://webaudit.smartskills.tn:8000/api/get_vm").then((res) => {
      if (res.status === 200) {
        setVm(res.data.Vm);
      }
    }).catch((error) => {
      console.error('Error sending data:', error);
    });
  }, []);

  const handleCheckboxChange = (scanId, checked) => {
    setCheckedItems((prev) => ({
      ...prev,
      [scanId]: checked,
    }));
  };

  const handleChange = (event) => {
    const selectedFolderId = event.target.value;
    const scansForSelectedFolder = Scans.filter((scan) => scan.folder_id === selectedFolderId);
  
    // Initialize checkedItems with all items checked for the selected scans
    const initialCheckedItems = {};
    scansForSelectedFolder.forEach((item) => {
      initialCheckedItems[item.id] = true;
    });
  

    setJsonData(scansForSelectedFolder);
    setCheckedItems(initialCheckedItems); // Set initial checked state
  };

  const handleVmChange = (event) => {
    const selectedVm = event.target.value;

    setVmSelected(selectedVm);
  };

  const allItemsChecked = Object.values(checkedItems).every(Boolean);
  const someItemsChecked = Object.values(checkedItems).some(Boolean);

  const toggleAllCheckboxes = (event) => {
    const allChecked = event.target.checked;
    const updatedCheckedItems = {};
    jsonData.forEach((item) => {
      updatedCheckedItems[item.id] = allChecked;
    });
    setCheckedItems(updatedCheckedItems);
  };

  const handleExport = (event) => {
    event.preventDefault(); // Prevent form submission and page refresh
    const Data = {
      Source: 'Nessus' ,
      ID_Projet: project_id
    };

    axios.post('http://webaudit.smartskills.tn:8000/api/uploadanomalie',Data)
    .then((response) => {
      if(response.data.status===200){
        setcreatedId(response.data.createdId);
      }
      })
      .catch((error) => {
        // Handle errors
        console.error('Error sending data:', error);
      });
   





    const Export_links = sessionStorage.getItem('Export_links');
   

const parsedData = {}; // Initialize parsedData as an empty object
parsedData.links = JSON.parse(Export_links);
parsedData.project_id = createdId;

console.log(parsedData);
    setExporting(true);
    axios.post('http://webaudit.smartskills.tn:8000/api/ImportAll',parsedData)
    .then((response) => {
      if(response.data.status===200){
        sessionStorage.removeItem('Export_links');
        setExport_links(null);
        swal("Imported","Successfully");
      }else if(response.data.status===404) {
        swal("Import is still in process","Give it more time");
      }
    })
    .catch((error) => {
      // Handle error
      console.error('Error sending data:', error);
      swal("Import is still in process","Give it more time");
    }) 
    .finally(() => {
      // Set exporting to false when export completes (whether successful or not)
      setExporting(false);
      
    });   
 
  };
  const handleImport = (event) => {
    event.preventDefault(); // Prevent form submission and page refresh
    const selectedIds = Object.keys(checkedItems).filter((itemId) => checkedItems[itemId]);
    const selectedIdsJSON = selectedIds.map((itemId) => ({
      value: itemId,
    }));
    /* const combinedData = {
      selectedIdsJSON: selectedIdsJSON,
      VmSelected: VmSelected,
    }; */
     setExporting(true);
    axios.post('http://webaudit.smartskills.tn:8000/api/ExportAll',selectedIdsJSON)
    .then((response) => {
      if(response.data.status===200){

        swal("Files are Preparing for the download","Please give it some time");
        sessionStorage.setItem('Export_links',response.data.links);
        const parsedData = JSON.parse(response.data.links);
        setExport_links(parsedData);
      }
    })
    .catch((error) => {
      // Handle errors
      console.error('Error sending data:', error);
    }) 
    .finally(() => {
      // Set exporting to false when export completes (whether successful or not)
      setExporting(false);
    }); 

  };

  return (
<div>
    {exporting ? ( // Conditional rendering based on the exporting state
    <div className="loading">
    <Box sx={{ display: 'flex' }}>
     <CircularProgress />
   </Box>
   </div>
   ) : (
    <Box
      component="form"
      sx={{
        '& .MuiTextField-root': { m: 1, width: '25ch' },
      }}
      noValidate
      autoComplete="off"
    >
      <div className='App'>
     
     {/*    <TextField
          id="outlined-select"
          select
          label="Select"
          helperText="Please select your VM"
          sx={{ mt: 2 }}
          onChange={handleVmChange}
        >
          {Vm.map((option) => (
            <MenuItem key={option.id} value={option.IP_Port}  >
              {option.IP_Port}
            </MenuItem>
          ))}
        </TextField> */} 
        <TextField
          id="outlined-select"
          select
          label="Select"
          helperText="Please select your folder"
          sx={{ mt: 2 }}
          onChange={handleChange}
        >
          {Folders.map((option) => (
            <MenuItem key={option.id} value={option.id}  >
              {option.name}
            </MenuItem>
          ))}
        </TextField>
        <div>
          <FormControlLabel 
          style={{ marginBottom: '16px'  }}
            label="Select All"
            control={
              <Checkbox
                checked={allItemsChecked}
                defaultChecked
                indeterminate={!allItemsChecked && someItemsChecked}
                onChange={toggleAllCheckboxes}
              />
            }
            sx={{ mt: 2 }}
          />
          <Box sx={{ display: 'flex', flexDirection: 'column', ml: 3 }}>
            {jsonData.map((item) => (
              <FormControlLabel
                key={item.id}
                label={item.name}
                control={
                  <Checkbox
                    checked={checkedItems[item.id] || false}
                    onChange={(event) => handleCheckboxChange(item.id, event.target.checked)}
                  />
                }
              />
            ))}
          </Box>
        </div>
        <Button style={{ marginBottom: '16px'  }} variant="outlined" onClick={handleImport} >Request</Button>

{ Export_links ?
        <Button variant="outlined" onClick={handleExport}>Export</Button>
        :
        <Button disabled variant="outlined" onClick={handleExport}>Export</Button>
      }
      </div>
    </Box>
    )}
    </div>
  );
}
