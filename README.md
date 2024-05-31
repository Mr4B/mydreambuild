# Capolavoro

Questo git conterrà il sito web "mydreambuild", nonchè utilizzato come capolavoro per l'esame

Per farlo funzionare:

- scaricare la libreria php-jwt e inserirla nella cartella vendor/firebase
- modificare i link in base alla cartella dove viene hostato, nel file db_connect.php (es: http://localhost/mydreambuild/)


per fare un commit e push
(terminale git/cartella corrente)

git add README.md
git commit -m "prova secondo commit"
git push -u origin main


per collegarsi al server della scuola e prendere/mettere file:

sftp user@indirizzo
? // Per le info dei comandi
get -r remote_directory // Per scaricare la directory da remoto
put -r local_directory // Per caricare la directory da locale
exit


Lavori da fare:
- Sistemare tutti gli stili delle pagine
- Popolare il db
- Pagina del profilo della persona
- Pagina di modifica dei prodotti
- Pagina per assegnare i moderatori (accessibile solo dall'admin)
- Pagina dettagli del prodotto



per schema e/r: https://www.drawio.com/blog/insert-sql

