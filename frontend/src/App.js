import './App.css';
import { BrowserRouter, Routes, Route } from 'react-router-dom';
import Nessus from './Nessus';
import Project from './projects/Projects';


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
              
            </Routes>
        </BrowserRouter>
    );
}

export default App;