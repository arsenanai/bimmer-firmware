(function () {
    'use strict';

    var form = document.getElementById('softwareForm');
    if (!form) return;

    var versionInput = document.getElementById('version');
    var hwVersionInput = document.getElementById('hwVersion');
    var versionError = document.getElementById('versionError');
    var hwVersionError = document.getElementById('hwVersionError');

    var resultArea = document.getElementById('resultArea');
    var successResult = document.getElementById('successResult');
    var errorResult = document.getElementById('errorResult');
    var successMsg = document.getElementById('successMsg');
    var errorMsg = document.getElementById('errorMsg');
    var stLink = document.getElementById('stLink');
    var gdLink = document.getElementById('gdLink');

    var openModalLink = document.getElementById('openModalLink');
    var softwareModal = document.getElementById('softwareModal');
    var closeModalBtn = document.getElementById('closeModalBtn');

    form.addEventListener('submit', function (e) {
        e.preventDefault();

        versionError.textContent = '';
        hwVersionError.textContent = '';
        versionInput.classList.remove('input-error');
        hwVersionInput.classList.remove('input-error');

        var version = versionInput.value.trim();
        var hwVersion = hwVersionInput.value.trim();
        var valid = true;

        if (!version) {
            versionError.textContent = 'This field is required';
            versionInput.classList.add('input-error');
            valid = false;
        }
        if (!hwVersion) {
            hwVersionError.textContent = 'This field is required';
            hwVersionInput.classList.add('input-error');
            valid = false;
        }
        if (!valid) return;

        form.classList.add('loading');

        resultArea.style.display = 'none';
        successResult.style.display = 'none';
        errorResult.style.display = 'none';
        stLink.style.display = 'none';
        gdLink.style.display = 'none';

        fetch('/api/carplay/software/version', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                version: version,
                mcuVersion: '',
                hwVersion: hwVersion
            })
        })
        .then(function (response) {
            return response.json();
        })
        .then(function (data) {
            resultArea.style.display = 'block';

            if (data.versionExist) {
                successResult.style.display = 'block';
                errorResult.style.display = 'none';
                successMsg.textContent = data.msg;

                if (data.st) {
                    stLink.href = data.st;
                    stLink.style.display = 'inline';
                }
                if (data.gd) {
                    gdLink.href = data.gd;
                    gdLink.style.display = 'inline';
                }
            } else {
                successResult.style.display = 'none';
                errorResult.style.display = 'block';
                errorMsg.textContent = data.msg;
            }
        })
        .catch(function () {
            resultArea.style.display = 'block';
            successResult.style.display = 'none';
            errorResult.style.display = 'block';
            errorMsg.textContent = 'Failed to fetch software details. Please try again.';
        })
        .finally(function () {
            form.classList.remove('loading');
        });
    });

    openModalLink.addEventListener('click', function (e) {
        e.preventDefault();
        softwareModal.style.display = 'flex';
    });

    closeModalBtn.addEventListener('click', function () {
        softwareModal.style.display = 'none';
    });

    softwareModal.addEventListener('click', function (e) {
        if (e.target === softwareModal) {
            softwareModal.style.display = 'none';
        }
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && softwareModal.style.display === 'flex') {
            softwareModal.style.display = 'none';
        }
    });
})();
