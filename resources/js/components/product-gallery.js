import { FilePond } from 'filepond';
import 'filepond/dist/filepond.min.css';

// Register FilePond plugins if needed
// import FilePondPluginImagePreview from 'filepond-plugin-image-preview';
// FilePond.registerPlugin(FilePondPluginImagePreview);

// Initialize FilePond for product image uploads
const productImageInput = document.querySelector('input[name="product_images[]"]');
if (productImageInput) {
    const pond = FilePond.create(productImageInput, {
        allowMultiple: true,
        maxFiles: 5,
        onaddfile: (error, file) => {
            if (error) {
                console.error('Error adding file:', error);
            } else {
                console.log('File added:', file);
            }
        },
        onremovefile: (file) => {
            console.log('File removed:', file);
        },
        // Add additional options as needed
    });
}

// Function to handle image gallery display
function displayProductGallery(images) {
    const galleryContainer = document.getElementById('product-gallery');
    if (galleryContainer) {
        galleryContainer.innerHTML = ''; // Clear existing images
        images.forEach(image => {
            const imgElement = document.createElement('img');
            imgElement.src = image.url; // Assuming image.url contains the image path
            imgElement.alt = image.alt || 'Product Image';
            imgElement.classList.add('rounded-lg', 'shadow-md', 'transition', 'duration-300', 'hover:scale-105');
            galleryContainer.appendChild(imgElement);
        });
    }
}

// Example usage: Call this function with an array of image objects
// displayProductGallery([{ url: 'path/to/image1.jpg', alt: 'Image 1' }, { url: 'path/to/image2.jpg', alt: 'Image 2' }]);