let filmSelectat = "";

function selecteazaFilm(numeFilm) {
    filmSelectat = numeFilm;
    document.getElementById("film-ales").innerText = filmSelectat;
    
    // Derulează automat pagina spre zona de rezervare
    document.getElementById("rezervare").scrollIntoView({ behavior: 'smooth' });
}

function confirmaRezervarea() {
    const numarPersoane = document.getElementById("numar-persoane").value;
    const mesaj = document.getElementById("mesaj-succes");

    if (filmSelectat === "") {
        alert("Te rugăm să alegi mai întâi un film din catalog!");
        return;
    }

    if (numarPersoane <= 0) {
        alert("Introdu un număr valid de persoane.");
        return;
    }

    mesaj.innerText = `Succes! Ai rezervat ${numarPersoane} loc(uri) pentru filmul "${filmSelectat}".`;
}