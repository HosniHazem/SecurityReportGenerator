import React, { useState, useEffect, useCallback } from 'react';
import Box from '@mui/material/Box';
import TextField from '@mui/material/TextField';
import MenuItem from '@mui/material/MenuItem';
import axios from 'axios';
import Button from '@mui/material/Button';
import './App.scss';
import Checkbox from '@mui/material/Checkbox';
import FormControlLabel from '@mui/material/FormControlLabel';
import CircularProgress from '@mui/material/CircularProgress';
import swal from 'sweetalert';
import Typography from '@mui/material/Typography';
import Stack from '@mui/material/Stack';
import Snackbar from '@mui/material/Snackbar';
import MuiAlert from '@mui/material/Alert';
import Avatar from '@mui/material/Avatar';
import FolderIcon from '@mui/icons-material/Folder';




const Alert = React.forwardRef(function Alert(props, ref) {
  return <MuiAlert elevation={6} ref={ref} variant="filled" {...props} />;
});
export default function SelectTextFields() {

  const [Export_links, setExport_links] = useState(sessionStorage.getItem('Export_links'))
  const project_id = sessionStorage.getItem('project_id');
  const [Folders, setFolders] = useState([]);
  const [Scans, setScans] = useState([]);
  const [Host, setHost] = useState([]);
  const [VmSelected, setVmSelected] = useState();
  const [jsonData, setJsonData] = useState([]);
  const [Stats, setStats] = useState([]);
  const [StatExport, setStatExport] = useState([]);
  const [Vm, setVm] = useState([]);
  const [Ready, setReady] = useState("no");
  const [checkedItems, setCheckedItems] = useState(() => {
    // Initialize checkedItems with all items checked by default

    const initialCheckedItems = {};
    jsonData.forEach((item) => {
      initialCheckedItems[item.id] = true;
    });
    return initialCheckedItems;
  });
const project_name = sessionStorage.getItem('project_name');
  const [exporting, setExporting] = useState(false); 
  const [expanded, setExpanded] = React.useState(false);
  const selectedIp = sessionStorage.getItem('selectedIp');
 
  useEffect(() => {
    const dataToSend = {
      selectedIp: selectedIp,
    }
  
    console.log(dataToSend)
    axios.post("http://webapp.ssk.lc/AppGenerator/backend/api/getScan",dataToSend).then((res) => {
      if (res.status === 200) {
        
        const filteredFolders = res.data.Folders.folders.filter(folder => folder.name.toLowerCase().includes(project_name.toLowerCase()));
        setFolders(filteredFolders);
        setScans(res.data.Folders.scans);
        setHost(res.data.info);

      }
    }).catch((error) => {
      console.error('Error sending data:', error);
    });
  }, []);




  useEffect(() => {
    axios.get("http://webapp.ssk.lc/AppGenerator/backend/api/get_vm").then((res) => {
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
  

  // Find the folder object with the matching id
  const selectedFolder = Folders.find((folder) => folder.id === selectedFolderId);

  if (selectedFolder) {
    // Now, you have the selected folder object
    const folderName = selectedFolder.name;

    // Store the folder name in session storage
    sessionStorage.setItem('description', folderName);

  }
    // Initialize checkedItems with all items checked for the selected scans
    const initialCheckedItems = {};
    scansForSelectedFolder.forEach((item) => {
      initialCheckedItems[item.id] = true;
    });
  

    setJsonData(scansForSelectedFolder);
    setCheckedItems(initialCheckedItems); 
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
    // Prevent form submission and page refresh
    event.preventDefault(); 
    let parsedData = {};
    const Label = sessionStorage.getItem('project_name');
    const description = sessionStorage.getItem('description');
    const Export_links = sessionStorage.getItem('Export_links');

 // Initialize parsedData as an empty object
parsedData.links = JSON.parse(Export_links);
parsedData.project_id = project_id;
parsedData.Label = Label;
parsedData.description = description;
parsedData.selectedIp = selectedIp;



     setExporting(true);
    axios.post('http://webapp.ssk.lc/AppGenerator/backend/api/ImportAll',parsedData)
    .then((response) => {
      if(response.data.status===200){
        setReady("no");
        sessionStorage.removeItem('Export_links');
        setExport_links(null);
        const inputObject = response.data.stats;
        const outputArray = Object.keys(inputObject).map(key => ({
          id: key,
          ...inputObject[key],
        }));
        
        setStatExport(outputArray);
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
      name: jsonData.find((item) => item.id === Number(itemId))?.name || "Unknown",
      ip : selectedIp
    }));
 console.log(selectedIdsJSON);
     setExporting(true);
    axios.post('http://webapp.ssk.lc/AppGenerator/backend/api/ExportAll',selectedIdsJSON)
    .then((response) => {
      if(response.data.status===200){

        swal("Files are Prepared for the download","You can start your download");
        sessionStorage.setItem('Export_links',response.data.links);

        const parsedData = JSON.parse(response.data.links);
        setExport_links(parsedData);
 
        const statsArray = Object.entries(response.data.stats).map(([key, value]) => ({
          id: key,
          ...value,
        }));
        
        setStats(statsArray);
        const allDone = statsArray.every(item => item.ver === 'done');
        const notDoneExist = statsArray.some(item => item.ver !== 'done');
        if (allDone) {
          setReady('yes');
        } else if (notDoneExist) {
          setReady('no');
        }
        
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
console.log(StatExport);
  return (
<div>
<div className='project'>
       <Stack direction="row" spacing={2}>
      <Avatar>
        <FolderIcon />  
      </Avatar>
      
      <h3>Project Name:</h3>
      <span>{project_name}</span>
   
      </Stack>
      </div>
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
            <MenuItem key={option.name} value={option.id}  >
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
  {jsonData.map((item) => {
 let stat = Stats.find((statItem) => statItem.id == item.id);

    let status = stat ? stat.ver : 'not done';

    return (
      <div key={item.id} style={{ display: 'flex', alignItems: 'center' }}>
        <FormControlLabel
          label={item.name}
          control={
            <Checkbox
              checked={checkedItems[item.id] || false}
              onChange={(event) => handleCheckboxChange(item.id, event.target.checked)}
            />
          }
        />
        {Stats ?
        <Typography
          variant="h7"
          gutterBottom
          color={status === 'done' ? 'green' : 'red'}
          style={{ marginLeft: '16px' }}
        >
         [{status}]
        </Typography> : null 
          }
      </div> 
      
    );
  })}
</Box>
        </div>
        <Button style={{ marginBottom: '16px'  }} variant="outlined" onClick={handleImport} >Request</Button>
  
{ Ready != "no" ?
        <Button variant="outlined" onClick={handleExport}>Export</Button>
        :
        <Button disabled variant="outlined" onClick={handleExport}>Export</Button>
      }
      <div className='item'></div>
      { StatExport ?
      StatExport.map((item) => (
    <Stack key={item.id} spacing={5} sx={{ width: '20%' }}>
    <Alert severity="info">The scan  <strong>{jsonData.find(item2 => item2.id == item.scan)?.name}</strong> has <strong>{item.number}</strong> imported vulnerability</Alert>
    </Stack>
    )) : null


}
      </div>
    </Box>
    )}
   
    </div>
  );
}
