// --- App entry: shared Alpine components, global behaviour, then page modules loaded only where their markup exists ---

import Alpine from 'alpinejs';
import registerAjaxPagination from './lib/ajax-pagination';
import registerConfirmForm from './lib/confirm-form';
import registerConfirmSubmit from './lib/confirm-submit';
import registerEnterBlurs from './lib/enter-blurs';
import registerImageFallback from './lib/image-fallback';
import registerPriceArrowKeys from './lib/price-arrow-keys';
import showToast from './lib/show-toast';
import achievementList from './components/achievement-list';
import activityFiles from './components/activity-files';
import autoHide from './components/auto-hide';
import autoplayCarousel from './components/autoplay-carousel';
import fileDrop from './components/file-drop';
import guestToasts from './components/guest-toasts';
import importDropzone from './components/import-dropzone';
import sharedImportManager from './components/import-manager';
import photoPicker from './components/photo-picker';
import profileLightbox from './components/profile-lightbox';
import removablePhoto from './components/removable-photo';
import sectionBuilder from './components/section-builder';
import slideCarousel from './components/slide-carousel';
import toastStack from './components/toast-stack';

// --- Shared Alpine components ---

window.Alpine = Alpine;

Alpine.data('toastStack', toastStack);
Alpine.data('sharedImportManager', sharedImportManager);
Alpine.data('importDropzone', importDropzone);
Alpine.data('photoPicker', photoPicker);
Alpine.data('removablePhoto', removablePhoto);
Alpine.data('fileDrop', fileDrop);
Alpine.data('activityFiles', activityFiles);
Alpine.data('slideCarousel', slideCarousel);
Alpine.data('autoplayCarousel', autoplayCarousel);
Alpine.data('profileLightbox', profileLightbox);
Alpine.data('autoHide', autoHide);
Alpine.data('achievementList', achievementList);
Alpine.data('sectionBuilder', sectionBuilder);
Alpine.data('guestToasts', guestToasts);

// --- Start Alpine once page modules that register Alpine components are loaded ---

const alpinePageModules = [];

if (document.querySelector('[data-business-admin]')) {
    alpinePageModules.push(import('./pages/business-admin'));
}

if (document.querySelector('[data-user-admin]')) {
    alpinePageModules.push(import('./pages/user-admin'));
}

if (document.querySelector('[data-business-directory]')) {
    alpinePageModules.push(import('./pages/business-directory'));
}

if (document.querySelector('[data-testimony-admin]')) {
    alpinePageModules.push(import('./pages/testimony-admin'));
}

if (document.querySelector('[data-business-show]')) {
    alpinePageModules.push(import('./pages/business-show'));
}

const startAlpine = () => Promise.all(alpinePageModules).then(() => Alpine.start());

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', startAlpine);
} else {
    startAlpine();
}

// --- Global behaviour ---

window.showToast = showToast;

document.addEventListener('DOMContentLoaded', () => {
    registerPriceArrowKeys();
});

registerConfirmSubmit();
registerAjaxPagination();
registerConfirmForm();

document.addEventListener('DOMContentLoaded', registerImageFallback);

// --- Page modules without Alpine components ---

if (document.querySelector('[data-offering-form]')) {
    import('./pages/offering-form').then(({ default: initOfferingForm }) => initOfferingForm());
}

if (document.querySelector('select[data-tom-select], select[data-region-province]')) {
    import('./components/tom-select-fields').then(({ default: initTomSelectFields }) => initTomSelectFields());
}

if (document.querySelector('[data-enter-blurs]')) {
    registerEnterBlurs();
}

if (document.querySelector('[data-user-form]')) {
    import('./pages/user-form').then(({ default: initUserForm }) => initUserForm());
}
