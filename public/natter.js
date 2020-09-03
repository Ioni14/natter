(function () {
    const baseUrl = '';//'http://localhost:8000';

    function createSpace(name, owner) {
        const payload = {
            name,
            owner,
        };

        fetch(`${baseUrl}/spaces`, {
            method: 'POST',
            credentials: 'include',
            body: JSON.stringify(payload),
            headers: {
                'Content-Type': 'application/json',
            },
        })
            .then(response => {
                if (response.ok) {
                    return response.json();
                } else {
                    throw Error(response.statusText);
                }
            })
            .then(json => console.log('Created space: ', json.name))
            .catch(error => console.error('Error: ', error));
    }

    function processFormSubmit(e) {
        e.preventDefault();
        let spaceName = document.getElementById('spaceName').value;
        let owner = document.getElementById('owner').value;
        createSpace(spaceName, owner);

        return false;
    }

    window.addEventListener('load', function (e) {
        document.getElementById('createSpace')
            .addEventListener('submit', processFormSubmit);
    });
})();
