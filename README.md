# [SCP-Web](https://github.com/DecreasingDenis/SCP-Web)

Progetto SCP scolastico di fine quinta.

## Richiesta

Si progetti un progetto di una pagina web con database e interazioni ad esso. Si simulerà un database sicuro per la "Fondazione SCP", esibendo le entità (SCP), personale e informazioni relative ad essi.<br>
Caratteristiche chiave:<br>
-Autentificazione dell'utente<br>
-SCP database con ricerca, filtraggio e visione delle tabelle<br>
-Report di incidenti (breaches) e test<br>
-Pannello Admin per maneggiare le entrate di SCP, pesonale e registri<br>
-Gestione del personale (es profili dello staff, livelli di autorizzazione) [?]<br>
-...

## Cosa si usa

Il software usato per svolgere il progetto è _HTML_, _CSS_, _JS_, _PHP_ e _MySQL_.<br>
**-HTML:** Lo scheletro della applicazione web. Definisce la strutture di tutte le pagine, inserisce il metadata (titolo della pagina, codifica dei caratteri, impostazioni della finestra di visualizzazione), crea container per content dinamico, si collega a CSS/JS file e PHP endpoints.<br>
**-CSS:** Controlla lo stile della visualizzazione e disposizione. stilizza elementi HTML per abbinarsi all'aesthetic SCP (dark theme, accenti rossi, "classified document" look), implementa design reattivo, aggiunge animationi (fae-in, hover effects for redacted text), gestisce stati UI dinamici[?].<br>
**-JS:** 

## Struttura

**-index.html:** pagina di benvenuto scroll, topbar redirect a login/sign-up e spiegaione della sigla spezzata per acronimo del sito, messa su "fogli" e con fittizie cancellazioni dei documenti. Aspetti scuri che funzionano con il tema dark. Questa sarà la pagina di Overview/benvenuto per gli un utente non ancora registrato [Sessione 0?].<br>
**-signup.html & signup.php:** pagina di registrazione al sito che garantira un accesso base alle informazioni, nessun potere sul database e personale se non di visualizzazione. Un utente appena registrato avrà poteri minimi che crescono solo se un potere più alto alza il livello di autorità. Password hashing tramite funzione php<br>
**-login.html & login.php:** pagina di accesso di un utente già esistente, se la password hashata è uguale a quella registrata nel database (già hashata) allora è giusta.<br>
**-personnel.php:** pagina di elenco del personale, opzioni di filtraggio ma parte in ordine alfabetico, in base al livello di autorità dell'utente esso può vedere solo chi ha lo stesso livello o più basso o gli utenti che hanno profilo con flag "pubblico" [da aggiungere attributo nel sql]. Da qui si va "personnelProfile".<br>
**-personnelProfile.php:** pagina dell'utente specifico, il numero della sessione è sempre quello dell'utente al momento loggato, si confronta con l'utente selezionato, controllo di sicurezza se l'utente che è collegato ha il permeso per vedere il profilo.<br>
**-incidents.php:** pagina lista dell'elenco degli incidenti per ordine di data (dal più recente)<br>
**-incidentReport.php:** pagina dell'incidente specifico, sempre stesse istruzioni generali di "personnelProfile.php".<br>
**-tests.php:** pagina lista dell'elenco dei test per ordine di data (dal più recente)<br>
**-testReport.php:** pagina del test specifico, sempre stesse istruzioni generali di "personnelProfile.php".<br>
**-adminPanel.php:** pannello (controllo in entrata) dove un utente di livello autorizzazione abbastanza alto (4 o +) può aggiungere, editare e cancellare (cancellare solo 5+, forse invece di cancellare definitivamente lo si renderà "invisibile a ogni ricerca") il personale, i test, gli SCP e gli incidenti. Si potrà anche vedere il registro delle azioni (logs).

## Database

Descrizione del problema, dei componenti chiave, delle relazioni e degli attributi del possibile database. In allegato creerò in SQL come creare le diverse tabelle e le relazioni inter-relazionali e intra-relazionali.
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

## Aspetto

L'aspetto generale del sito sarà dark, con spesso messaggi oscurati per inscenare segretezza e sicurezza.

### aiuti

[*repos help*](https://docs.github.com/en/repositories/creating-and-managing-repositories/about-repositories)

[*README di benvenuto personale*](https://github.com/DecreasingDenis/SCP-Web/edit/main/README.md)

[*VS/GitHub connection*](https://code.visualstudio.com/docs/sourcecontrol/github)

[*README.md syntax*](https://docs.github.com/en/get-started/writing-on-github/getting-started-with-writing-and-formatting-on-github/basic-writing-and-formatting-syntax)

__DeepSeek/Gemini/Havier69Rusca aiuto AI (copilot)__
