import FilePond from 'filepond';
import FilePondPluginImagePreview from 'filepond-plugin-image-preview';
import FilePondPluginFileValidateType from 'filepond-plugin-file-validate-type';
import FilePondPluginImageExifOrientation from 'filepond-plugin-image-exif-orientation';
import FilePondPluginImageTransform from 'filepond-plugin-image-transform';

// Register the plugins
FilePond.registerPlugin(
    FilePondPluginImagePreview,
    FilePondPluginFileValidateType,
    FilePondPluginImageExifOrientation,
    FilePondPluginImageTransform
);

// Select the file input and create a FilePond instance
const inputElement = document.querySelector('input[type="file"]');
const pond = FilePond.create(inputElement, {
    allowMultiple: true,
    imagePreviewHeight: 170,
    labelIdle: 'Drag & Drop your images or <span class="filepond--label-action">Browse</span>',
    acceptedFileTypes: ['image/png', 'image/jpeg', 'image/webp'],
    oninit: () => {
        console.log('FilePond instance has been initialized');
    },
    onerror: (error) => {
        console.error('FilePond error:', error);
    },
});

// Optional: Add additional configuration for image transformations
pond.setOptions({
    imageTransformOutput: {
        type: 'image/jpeg',
        quality: 90,
    },
});