import './bootstrap';
import 'flowbite';
import {initFlowbite} from "flowbite";

document.addEventListener('livewire:initialized', () => {
    Livewire.hook('morph.finished', ({ component, el }) => {
        if (typeof initFlowbite === 'function') {
            initFlowbite();
        }
    });
});
