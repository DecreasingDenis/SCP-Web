# [SCP-Web](https://github.com/DecreasingDenis/SCP-Web)

Progetto SCP scolastico di fine quinta.

## Struttura

-index.html: pagina di benvenuto dall'aspetto normale, due opzioni di login-registrazione oltre a una presentazione generale del lavoro e del database


## Database

Descrizione del problema, die componenti chiave, delle relazioni e degli attributi del possibile database. In allegato creerò in SQL come creare le diverse tabelle e le relazioni inter-relazionali e intra-relazionali.
Le entità che mi servono sono "Personale", la più grande e numerosa che conterrà anche le informazioni di login per il sito, l'entità sarà particolarmente ramificata in diversi reparti e ruoli. La seconda entità sarà SCP, le creature che saranno soggette a test da parte del personale. La terza e quarta sono particolarmente simili, considerando di unirle (possibilmente in una entità Documento), per ora le entità sono "Test" e "Incident Report". 
Considero una relazione "supervisiona" che collega Personale a se stessa forse 1 a 1.

| Personale (1) → (M) Test   |<br>
| Personale (1) → (M) Report |<br>
| Report (1) → (1) Test      |<br>
| Report (M) → (1) SCP       |<br>
| SCP (1) → (M) Test         |


Attributi Entità<br>
Personale: <ins>user_ID</ins>, username, password, email, name, surname, clearance_Level, birth_Date, enroll_Date, status, gender, nationality, department~~(?)~~, ...<br>
Test: <ins>test_ID</ins>, scp*, class_D*, scientist*, description, result, test_Date, start_Time, duration, end_Time, ...<br>
SCP: <ins>scp_Number</ins>, danger_Class, description, safety_Measures, status, containment_Date, ...<br>
Report: <ins>report_ID</ins>, scp_Breached*, reporter*, incident_description, incident_Date, start_Time, duration, end_Time, ...<br>
[*datbase.sql*](https://github.com/DecreasingDenis/SCP-Web/blob/main/scpDatabase.sql)

### aiuti

[*repos help*](https://docs.github.com/en/repositories/creating-and-managing-repositories/about-repositories)

[*README di benvenuto personale*](https://github.com/DecreasingDenis/SCP-Web/edit/main/README.md)

[*VS/GitHub connection*](https://code.visualstudio.com/docs/sourcecontrol/github)

[*README.md syntax*](https://docs.github.com/en/get-started/writing-on-github/getting-started-with-writing-and-formatting-on-github/basic-writing-and-formatting-syntax)

__DeepSeek aiuto AI (copilot)__
