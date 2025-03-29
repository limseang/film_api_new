import './bootstrap';

import html2canvas from 'html2canvas';
import axios from 'axios';

// Get the element you want to take a screenshot of
const element = document.getElementById('element-id');

// Use html2canvas to take a screenshot
html2canvas(element).then(canvas => {
    // Convert the canvas to a base64 image
    const base64image = canvas.toDataURL('image/png');

    // Send a POST request to your server with the base64 image
    axios.post('/api/share-article/{id}', { image: base64image })
        .then(response => {
            console.log('Image saved successfully');
        })
        .catch(error => {
            console.error('Error saving image:', error);
        });
});
