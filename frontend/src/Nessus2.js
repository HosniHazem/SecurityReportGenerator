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
import Swal from 'sweetalert2';
import Typography from '@mui/material/Typography';
import Stack from '@mui/material/Stack';
import Snackbar from '@mui/material/Snackbar';
import MuiAlert from '@mui/material/Alert';
import Avatar from '@mui/material/Avatar';
import FolderIcon from '@mui/icons-material/Folder';
import PropTypes from 'prop-types';
import { Spinner } from "reactstrap";




const Alert = React.forwardRef(function Alert(props, ref) {
  return <MuiAlert elevation={6} ref={ref} variant="filled" {...props} />;
});
function CircularProgressWithLabel(props) {
  return (
    <Box sx={{ position: 'relative', display: 'inline-flex' }}>
      <CircularProgress variant="determinate" {...props} />
      <Box
        sx={{
          top: 0,
          left: 0,
          bottom: 0,
          right: 0,
          position: 'absolute',
          display: 'flex',
          alignItems: 'center',
          justifyContent: 'center',
        }}
      >
        <Typography variant="caption" component="div" color="text.secondary">
          {`${Math.round(props.value)}%`}
        </Typography>
      </Box>
    </Box>
  );
}

CircularProgressWithLabel.propTypes = {
  /**
   * The value of the progress indicator for the determinate variant.
   * Value between 0 and 100.
   * @default 0
   */
  value: PropTypes.number.isRequired,
};
export default function SelectTextFields() {

  const [Export_links, setExport_links] = useState()
  const project_id = sessionStorage.getItem('project_id');
  const [Folders, setFolders] = useState([]);
  const [Scans, setScans] = useState([]);
  const [Host, setHost] = useState([]);
  const [VmSelected, setVmSelected] = useState();
  const [jsonData, setJsonData] = useState([]);
  const [Stats, setStats] = useState([]);
  
  const [StatExport, setStatExport] = useState(0);
  const [Vm, setVm] = useState([]);
  const [Progress, setProgress] = useState(0);
  const [Loading, setLoading] = useState(false);
  const [checkedItems, setCheckedItems] = useState(() => {
    // Initialize checkedItems with all items checked by default

    const initialCheckedItems = {};
    jsonData.forEach((item) => {
      initialCheckedItems[item.id] = true;
    });
    return initialCheckedItems;
  });
const project_name = sessionStorage.getItem('project_name');
const Auth = sessionStorage.getItem('Auth');
  const [exporting, setExporting] = useState(false); 
  const [expanded, setExpanded] = React.useState(false);
  const selectedIp = sessionStorage.getItem('selectedIp');
  const name = sessionStorage.getItem('project_name');
  const description = sessionStorage.getItem('description');

 
  useEffect(() => {
    const dataToSend = {
      selectedIp: selectedIp,
      Auth : Auth
    }
  
    console.log(dataToSend)
    axios.post("http://webapp.smartskills.tn/AppGenerator/backend/api/getScan2",dataToSend).then((res) => {
      if (res.status === 200) {
        
        const filteredFolders = res.data.Folders.folders.filter(folder => folder.name.toLowerCase().includes(project_name.toLowerCase()));
       // const filteredFolders = res.data.Folders.folders;

        setFolders(filteredFolders);
        setScans(res.data.Folders.scans);

      }
    }).catch((error) => {
      console.error('Error sending data:', error);
    });
  }, []);

  useEffect(() => {
    axios.get("http://webapp.smartskills.tn/AppGenerator/backend/api/get_vm").then((res) => {
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


  
  const handleImport = (event) => {
    event.preventDefault(); // Prevent form submission and page refresh
    setLoading(true)


    const selectedIds = Object.keys(checkedItems).filter((itemId) => checkedItems[itemId]);
    const initialProgress = selectedIds.reduce((acc, itemId) => {
      acc[itemId] = {
        ready: 0,
        request1: false,
        request2: false
      };
      return acc;
    }, {});
    setProgress(initialProgress);
    console.log(Progress);
    const selectedIdsJSON = selectedIds.map((itemId) => ({
      value: itemId,
      name: jsonData.find((item) => item.id === Number(itemId))?.name || "Unknown",
      ip : selectedIp,
      Auth : Auth
    }));
    console.log(selectedIdsJSON)
    const promises = selectedIdsJSON.map((item) => {
      return axios.post('http://webapp.smartskills.tn/AppGenerator/backend/api/ExportOne', item)
        .then((response) => {
          if(response.data.error){
            Swal.fire({
              icon: 'error',
              title: 'Oops...',
              text: response.data.error
            })
          }
          if (response.data.status === 200) {
            let parsedData = {
              project_id: project_id,
              Label: name,
              description: description,
              selectedIp: selectedIp,
              Auth: Auth,
              scan: item.value,
              links: response.data.links
            };
            console.log(parsedData);
    
            setProgress((prevProgress) => ({
              ...prevProgress,
              [item.value]: {
                ...prevProgress[item.value],
                request1: true,
                ready: 50
              }
            }));
    
            return parsedData;
          }
        })
        .catch((error) => {
          console.error('Error sending data in export:', error.message);
          swal(error);
        });
    });
    let i=0;
// Wait for all export promises to resolve
Promise.all(promises)
  .then((parsedDataArray) => {
    // Access the parsedDataArray when all export requests are completed
    console.log('all parsed data here', parsedDataArray);

    // Process the import requests one by one
    let importPromiseChain = Promise.resolve();
    parsedDataArray.forEach((item) => {
      importPromiseChain = importPromiseChain.then(() =>
        axios.post('http://webapp.smartskills.tn/AppGenerator/backend/api/ImportOne', item)
      ).then((response) => {
        if(response.data.error){
          Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: response.data.error
          })
        }
        if (response.data.status === 200) {
          const inputObject = response.data.stats;
          setStatExport((prevStatExport) => ({
            ...prevStatExport,
            [inputObject.scan]: {
              scan: inputObject.scan,
              number: inputObject.number
            }
          }));

          setProgress((prevProgress) => ({
            ...prevProgress,
            [item.scan]: {
              ...prevProgress[item.scan],
              request1: true,
              ready: 100
            }
          }));

          setLoading(false);
        }
      }).catch((error) => {
        // Handle error
        console.error('Error sending data in import:', error.message);
        swal(error);
      });
    });

    // Wait for all import promises to resolve
    importPromiseChain.then(() => {
      console.log('All import requests completed');
    });
        // Perform any additional actions here
      })
      .catch((error) => {
        // Handle error if any of the promises fail
        console.error('Error in Promise.all:', error.message);
      });
    
    
  };

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
   
    <Box
      component="form"
      sx={{
        '& .MuiTextField-root': { m: 1, width: '25ch' },
      }}
      noValidate
      autoComplete="off"
    >
      <div className='App'>
     

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
    let status = Progress[item.id] ? Progress[item.id].ready : 0;
    let number = StatExport[item.id] ? StatExport[item.id].number : 0;

    return (
      <div key={item.id} style={{ display: 'flex', alignItems: 'center', marginBottom: '10px' }}>
        <FormControlLabel
          label={item.name}
          control={
            <Checkbox
              checked={checkedItems[item.id] || false}
              onChange={(event) => handleCheckboxChange(item.id, event.target.checked)}
            />
          }
        />
        <CircularProgressWithLabel value={status} />

        {StatExport ? (
        <Stack spacing={1} sx={{ marginLeft: '10px' }}>
        <Alert severity="info" sx={{ width: '100%', fontSize: '12px' }}>
          {`${number} vulnerability imported`}
        </Alert>
      </Stack>
        ) : null}
      </div>
    );
  })}
</Box>
        </div>
        <Button style={{ marginBottom: '16px'  }} variant="outlined" onClick={handleImport} > { Loading!=false ?   <Spinner type="grow" className="text-primary">
        <span className=" sr-only">Loading...</span>
      </Spinner> :'Import'}</Button>
  


     
      <div className='item'></div>
    {/*   { StatExport ?
      StatExport.map((item,index) => (
    <Stack key={index} spacing={5} sx={{ width: '20%' }}>
    <Alert severity="info">The scan  <strong>{jsonData.find(item2 => item2.id == item.scan)?.name}</strong> has <strong>{item.number}</strong> imported vulnerability</Alert>
    </Stack>
    )) : null


} */}
      </div>
    </Box>
   
   
    </div>
  );
}
