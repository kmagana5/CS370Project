import React, { useEffect, useState } from 'react';
import axios from 'axios';

function App() {
  const [data, setData] = useState([]);

  useEffect(() => {
    axios.get('http://localhost:8000/importpizzadata.php')
        .then(response => {
          console.log('Data received:', response.data);
          setData(response.data);
        })
        .catch(error => {
          console.error('Error fetching data:', error);
        });
  }, []);

  return (
      <div>
        <h1>Sample Data</h1>
        <ul>
          {data.map(item => (
              <li key={item.ID}>
                ID: {item.ID}, Sample Column: {item.samplecol}
              </li>
          ))}
        </ul>
      </div>
  );
}

export default App;
