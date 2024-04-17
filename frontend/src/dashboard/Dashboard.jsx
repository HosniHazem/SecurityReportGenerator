import React, { useRef, useContext, useState, useEffect } from "react";
import { DataGrid } from "@mui/x-data-grid";
import swal from "sweetalert";
import { Navigate, useNavigate, useParams } from "react-router-dom";
import { Link } from "react-router-dom";
import CircularProgress from "@mui/material/CircularProgress";
import Box from "@mui/material/Box";
import { encode } from "html-entities";
import axios from "axios";
import Button from "@mui/material/Button";
import Dialog from "@mui/material/Dialog";
import DialogActions from "@mui/material/DialogActions";
import DialogContent from "@mui/material/DialogContent";
import DialogContentText from "@mui/material/DialogContentText";
import DialogTitle from "@mui/material/DialogTitle";
import useMediaQuery from "@mui/material/useMediaQuery";
import { useTheme } from "@mui/material/styles";
import IconButton from "@mui/material/IconButton";
import CloseIcon from "@mui/icons-material/Close";
import Nessus from "../Nessus";
import Nessus2 from "../Nessus2";
import "./datatable.scss";
import { green } from "@mui/material/colors";
import { axiosInstance } from "../axios/axiosInstance";
import { ButtonBase, ButtonGroup } from "@mui/material";
import toast, { Toaster } from "react-hot-toast";

function useDialogState() {
  const [open, setOpen] = React.useState(false);
  return { open, setOpen };
}
const Dashboard = () => {
  const navigate = useNavigate();
  const [Project, setProject] = useState([]);
  const [singleProject, setSinlgeProject] = useState();
  const [Vm, setVm] = useState([]);
  const [exporting, setExporting] = useState(false); // Add loading state
  const [downloading, setDownloading] = useState(false);
  const { open, setOpen } = useDialogState();
  const selected = sessionStorage.getItem("selectedIp");
  const [selectedIp, setSelectedIp] = useState(selected);
  const theme = useTheme();
  const [isScrollingLeft, setIsScrollingLeft] = useState(false);

  const fullScreen = useMediaQuery(theme.breakpoints.down("md"));

  // }, []);
  // useEffect(() => {
  //   axiosInstance
  //     .get(`/Project/${id}/show`)
  //     .then((response) => {
  //       if (response.status === 200) {
  //         setProject(response.data.Project);

  //       }
  //     })
  //     .catch((error) => {
  //       console.error("Error fetching data:", error);
  //     });
  // }, []);

  useEffect(() => {
    axios
      .get(`http://webapp.ssk.lc/AppGenerator/backend/api/Project`)
      .then((res) => {
        if (res.status === 200) {
          const sortedProjects = res.data.Project.sort((a, b) => b.id - a.id);
          setProject(sortedProjects);
        }
      })
      .catch((error) => {
        // Handle error accordingly
        console.error("Error fetching data: ", error);
      });
  }, []);
  console.log(Project);

  //... rest of your component
  useEffect(() => {
    axios
      .get(`http://webapp.ssk.lc/AppGenerator/backend/api/get_vm`)
      .then((res) => {
        if (res.status === 200) {
          const inputObject = res.data.Vm;
          const outputArray = Object.keys(inputObject).map((key) => ({
            id: key,
            ...inputObject[key],
          }));
          setVm(outputArray);
        }
      });
  }, []);

  const handleSelectProject = (id) => {
    // Remove the previously selected project ID
    localStorage.removeItem("selectedProjectId");

    // Set the new selected project ID
    localStorage.setItem("selectedProjectId", id);
  };

  const handleGenerateWordDocument = async () => {
    try {
      await axios.get(
        `http://webapp.ssk.lc/AppGenerator/backend/api/generate-ansi/1`,
        {}
      );

      console.log("Request sent successfully");
      // Optionally, show a success message here
    } catch (error) {
      // Handle errors or show an error message
      console.error("Error generating ANSI document:", error);
      // swal("Error", "An error occurred while generating ANSI document", "error");
    }
  };

  const handleNavigateToRmQuesion = (c) => {
    window.open(`https://smartskills.com.tn/wqkjsdhvj76vhbnc565ds/rmquestions.php?c=${c}&k=qdsg54SFDbfdQSd`, '_blank');
};


  const handleFillQuestions = async (c) => {
    console.log('c is ',c)
    try {
      const response = await axiosInstance(`/Insert-Into-Answers/${c}`);
      console.log(response.data);
      if(response.data.success){
        toast.success(response.data.message);

      }




    } catch (error) {

      toast.error(error);

    }
  };

  const handleFillIndicators = async (c) => {
    console.log('c is ',c)
    try {
      const response = await axiosInstance(`/Insert-Into-Indicators/${c}`);
      console.log(response.data);
      if(response.data.success){
        toast.success(response.data.message);

      }




    } catch (error) {

      toast.error(error);

    }
  };

    const handleScroll = (event) => {
    const scrollLeft = event.target.scrollLeft;
    setIsScrollingLeft(scrollLeft > 0);
  };

  const userColumns = [
    { field: "id", headerName: "ID", width: 100 , 
    //  headerClassName: "sticky-header",
    // cellClassName: "sticky-column",
  },
    {
      field: "Nom",
      headerName: "Nom",
      width: 120,
      // headerClassName: "sticky-header",
      // cellClassName: "sticky-column",
      renderCell: (params) => {
        return params.row.Nom;
      },
    },
    {
      field: "Import",
      headerName: "Import",
      width: 100,
      renderCell: (params) => {
        const id = params.row.id;
        const name = params.row.Nom;
        return (
          <div className="cellAction">
            <div className="Pick" onClick={(e) => Popup(name, id, e)}>
              Import
            </div>
            {/* <Link to={`/sow/${id}`} style={{ textDecoration: "none" }}>
              <div className="Pick2">SOW</div>
            </Link> */}
          </div>
        );
      },
    },
    {
      field: "ProjectDetails",
      headerName: "Project Details",
      width: 650,
      renderCell: (params) => {
        const id = params.row.id;
        const customerId=params.row.customer_id
        const nom = params.row.Nom;
        const c=params.row.iterationKey;
        return (
          <div className="cellAction">
            <Link to={`/add-glb-pip/${customerId}`} style={{ textDecoration: "none" }}>
              <div className="Pick2">PIP</div>
            </Link>
            <Link to={`/sow/${id}`} style={{ textDecoration: "none" }}>
              <div className="Pick2">SOW</div>
            </Link>
            <Link to={`/sites/${customerId}/customer-sites/${customerId}`} style={{ textDecoration: "none" }}>
              <div className="Pick2">Sites</div>
            </Link>
            <Link
              to={`/all-audit-previous-audit/${id}`}
              style={{ textDecoration: "none" }}
            >
              <div className="Pick2">PrevAudit</div>
            </Link>
            <Link to={`/anomalie/${id}`} style={{ textDecoration: "none" }}>
              <div className="Pick2">Anomalie</div>
            </Link>
            <Link onClick={()=>handleFillQuestions(c)} style={{ textDecoration: "none" }}>
              <div className="Pick2">Questions</div>
            </Link>
            <Link onClick={()=>handleFillIndicators(c)} style={{ textDecoration: "none" }}>
              <div className="Pick2">Indicators</div>
            </Link>
            <Link to={`/all-rm-processus/${c}`} style={{ textDecoration: "none" }}>
              <div className="Pick2">RmProccessus</div>
            </Link>
            <Link to={`/all-vuln/${id}`} style={{ textDecoration: "none" }}>
              <div className="Pick2">Vuln</div>
            </Link>
            

          </div>
        );
      },
    },
    {
      field: "Navigation",
      headerName: "Navigation",
      width: 100,
      renderCell:(params)=>{
        const c=params.row.iterationKey ?params.row.iterationKey : "";
        return (
          <>
          {c && c!="" &&
          <Button onClick={() =>handleNavigateToRmQuesion(c)}>
           OSMQ 
          </Button>
          
          
          }


          </>


        )
        



      }

    },
    {
      field: "Export",
      headerName: "Export",
      width: 250,
      renderCell: (params) => {
        const id = params.row.id;
        const name = params.row.Nom;
        return (
          <div className="cellAction">
            <Link to={`/quality/${id}`} style={{ textDecoration: "none" }}>
              <div
                className="Pick3"
                onClick={() => {
                  sessionStorage.setItem("project_name", name);
                  // hendleSelectProject(id);
                }}
              >
                QC
              </div>
            </Link>
            <div
              className={`deButton ${
                params.row.QualityChecked === 0 ? "disabled" : ""
              }`}
              onClick={(e) => {
                if (params.row.QualityChecked !== 0) {
                  Export2(name, id, e);
                }
              }}
            >
              Annexe
            </div>

            <div>
              <Link
                to={`/ansi-report/${id}`}
                style={{ textDecoration: "none" }}
              >
                <Button> Ansi </Button>
              </Link>
            </div>
          </div>
        );
      },
    },
  ];

  const handleDelete = async (e, id) => {
    e.preventDefault();
    await axios
      .delete(
        `http://webapp.ssk.lc/AppGenerator/backend/api/Project/${id}/delete`
      )
      .then((res) => {
        if (res.status === 200) {
          swal("Deleted!", res.data.message, "success");
          window.location.reload();
        } else if (res.data.status === 404) {
          swal("Error", res.data.message, "error");
        }
      });
  };
  const handleClickOpen = () => {
    setOpen(true);
  };

  const handleClose = () => {
    setOpen(false);
  };

  const Select = (name, id, e) => {
    e.persist();
    sessionStorage.setItem("project_id", id);
    sessionStorage.setItem("project_name", name);

    navigate("/import");
  };

  const Popup = (name, id, e) => {
    e.persist();
    sessionStorage.setItem("project_id", id);
    sessionStorage.setItem("project_name", name);
    setOpen(true);
  };

  const handleSelect = (ip, auth) => {
    // Store the selected IP in session storage
    sessionStorage.setItem("selectedIp", ip);
    sessionStorage.setItem("Auth", auth);
    setSelectedIp(ip);
  };

  const handleCheck = () => {
    // Handle the logic for the checked button
    console.log("Checked button clicked");
  };
  if (!selectedIp) {
    const firstActiveVm = Vm.find((element) => element.answer === "Online");
    if (firstActiveVm) {
      console.log("happened")
      handleSelect(firstActiveVm.ip, firstActiveVm.Auth)
    }
}

  const Export = (id, e) => {
    e.persist();
    setDownloading(true);
    const project_id = sessionStorage.getItem("project_id");
    const dataToSend = {
      project_id: id,
    };
    setExporting(true);

    axios
      .post(
        `http://webapp.ssk.lc/AppGenerator/backend/api/generate-word-document/`,
        dataToSend,
        {
          responseType: "blob", // Set responseType to 'blob' to indicate binary data
        }
      )
      .then((response) => {
        // Use response.data as the blob
        const blob = new Blob([response.data], {
          type: "application/octet-stream",
        });

        // Create a URL for the blob
        const url = window.URL.createObjectURL(blob);

        // Create a temporary <a> element to trigger the download
        const a = document.createElement("a");
        a.href = url;
        a.download = "downloaded_files.zip";
        document.body.appendChild(a);
        a.click();

        // Remove the temporary <a> element and revoke the URL to free up resources
        document.body.removeChild(a);
        window.URL.revokeObjectURL(url);

        setDownloading(false);
        swal("Exported", "Successfully");
      })
      .catch((error) => {
        // Handle errors
        console.error("Error sending data:", error);
        swal("Problem", "Detected");
        setDownloading(false);
      })
      .finally(() => {
        // Set exporting to false when export completes
        setExporting(false);
      });
  };

  const Export2 = (name, id, e) => {
    e.persist();
    setDownloading(true);
    const project_id = sessionStorage.getItem("project_id");
    const token = localStorage.getItem("token"); // Fetch token from local storage
    const dataToSend = {
      project_id: [id],
      annex_id: [1, 2, 3, 4, 5, 6, 7, 8],
      ZipIt: "oui",
    };
    console.log(dataToSend);

    setExporting(true);

    axios
      .post(
        `http://webapp.ssk.lc/AppGenerator/backend/api/getAnnexes`,
        dataToSend,
        {
          headers: {
            Authorization: `Bearer ${token}`, // Set Authorization header with the token
          },
          responseType: "blob", // Set responseType to 'blob' to indicate binary data
        }
      )
      .then((response) => {
        // Use response.data as the blob
        const blob = new Blob([response.data], {
          type: "application/octet-stream",
        });

        // Create a URL for the blob
        const url = window.URL.createObjectURL(blob);

        // Create a temporary <a> element to trigger the download
        const a = document.createElement("a");
        a.href = url;
        a.download = name + "downloaded_files.zip";
        document.body.appendChild(a);
        a.click();

        // Remove the temporary <a> element and revoke the URL to free up resources
        document.body.removeChild(a);
        window.URL.revokeObjectURL(url);

        setDownloading(false);
        swal("Exported", "Successfully");
      })
      .catch((error) => {
        // Handle errors

        console.error("Error sending data:", error);
        swal("Problem", "Detected");
        setDownloading(false);
      })
      .finally(() => {
        // Set exporting to false when export completes
        setExporting(false);
      });
  };

  const Export3 = (name, id, e) => {
    e.persist();
    setDownloading(true);
    const project_id = sessionStorage.getItem("project_id");
    const dataToSend = {
      project_id: id,
      filename: name,
    };
    console.log(dataToSend);

    setExporting(true);

    axios
      .post(
        `http://webapp.ssk.lc/AppGenerator/backend/api/generate-ansi`,
        dataToSend,
        {
          responseType: "blob", // Set responseType to 'blob' to indicate binary data
        }
      )
      .then((response) => {
        // Use response.data as the blob
        const blob = new Blob([response.data], {
          type: "application/octet-stream",
        });

        // Create a URL for the blob
        const url = window.URL.createObjectURL(blob);

        // Create a temporary <a> element to trigger the download
        const a = document.createElement("a");
        a.href = url;
        a.download = name + ".csv";
        document.body.appendChild(a);
        a.click();

        // Remove the temporary <a> element and revoke the URL to free up resources
        document.body.removeChild(a);
        window.URL.revokeObjectURL(url);

        setDownloading(false);
        swal("Exported", "Successfully");
      })
      .catch((error) => {
        // Handle errors
        console.error("Error sending data:", error);
        swal("Problem", "Detected");
        setDownloading(false);
      })
      .finally(() => {
        // Set exporting to false when export completes
        setExporting(false);
      });
  };
  const cellStyle = {
    padding: "10px",
    textAlign: "center",
    border: "1px solid black",
  };

  return (
    <div>
      <Dialog
        open={open}
        onClose={handleClose}
        maxWidth={"md"}
        fullWidth={"false"}
      >
        <DialogTitle>Import Nessus</DialogTitle>
        <IconButton
          aria-label="close"
          onClick={handleClose}
          sx={{
            position: "absolute",
            right: 8,
            top: 8,
            color: (theme) => theme.palette.grey[500],
          }}
        >
          <CloseIcon />
        </IconButton>

        <DialogContent>
          <Nessus2 />

          <DialogActions>
            <Button onClick={handleClose}>Cancel</Button>
          </DialogActions>
        </DialogContent>
      </Dialog>

      <div className="datatable">
        <table style={{ borderCollapse: "collapse", width: "15%" }}>
          <thead>
            <tr>
            <th className="sticky-header">Name</th> 
              <th>URL</th>
              <th>Status</th>
              <th>Select</th>
            </tr>
          </thead>

          <tbody>
            {Vm.map((url) => (
              <tr
                key={url.ip}
                style={{
                  backgroundColor: url.answer === "Online" ? "green" : "red",
                }}
              >
                <td  style={cellStyle}>{url.Name}</td> 
                <td  style={cellStyle}>{url.ip}</td>
                <td style={cellStyle}>{url.answer}</td>
                <td style={cellStyle}>
                  <input
                    type="radio"
                    name="selectedIp"
                    value={url.ip}
                    checked={url.ip === selectedIp}
                    onChange={() => handleSelect(url.ip, url.Auth)}
                  />
                </td>
              </tr>
            ))}
          </tbody>
        </table>

        {exporting ? ( // Conditional rendering based on the exporting state
          <div className="loading">
            <Box sx={{ display: "flex" }}>
              <CircularProgress />
            </Box>
          </div>
        ) : (
          <div>
            <DataGrid
              style={{ width: "100%" }}
              className="datagrid"
              rows={Project}
              columns={userColumns}
              pageSize={9}
              rowsPerPageOptions={[9]}
              columnBuffer={2}
              onScroll={handleScroll}
               // Add this line
            />
          </div>
        )}
      </div>
    </div>
  );
};

export default Dashboard;
