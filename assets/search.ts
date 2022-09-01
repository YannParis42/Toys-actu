export interface HTMLData{
    html: string;
}

window.addEventListener('load', () => {
    console.log('coucou');
    
    //champ recherche
    const searchInput: HTMLInputElement = document.querySelector('#search-input');

    if (searchInput) {

        searchInput.addEventListener('keyup', (e) => {
            //Déclaration de ma variable qui affichera mes résultats
            let results = document.querySelector('#Livesearch');

            //Commence à faire la recherche intéractive au bout de 2 caractères
            if (searchInput.value.length >= 2) {
                fetch('/news/ajax/search/' + searchInput.value, {
                        method: 'GET'
                    }).then(response => {
                        return response.json() as Promise < HTMLData > ;
                    })
                    .then(data => {
                        results.classList.remove('d-none');
                        results.innerHTML = data.html;
                    });
            } else if (searchInput.value.length < 2 && e.code == 'Backspace') {
                results.classList.add('d-none');
            }
        });
    }
});