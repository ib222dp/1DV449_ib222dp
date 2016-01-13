Ingela Bladh (ib222dp)

# Inledning

Jag har skapat en webbplats där botaniker kan söka i [BHL(Biodiversity Heritage Library)]
(http://www.biodiversitylibrary.org/) och [Gallica](http://gallica.bnf.fr/) samtidigt. 
Jag blev inspirerad av [denna bloggpost](http://blog.biodiversitylibrary.org/2012/04/bhl-and-our-users-anders-hagborg.html), 
där Anders Hagborg berättar att han skulle vilja kunna söka i bland annat BHL, Gallica och Google Books samtidigt.
Såvitt jag vet finns det inga applikationer där man kan söka i BHL och Gallica samtidigt. Jag har skapat applikationen i
 PHP, och anropar API:en via CURL. Datat hämtas i JSON-format, och cachas i en MySQL-databas.
Gallica har inget eget API, men de bidrar med sitt material till [Europeana](http://www.europeana.eu/portal/), så deras 
material hämtas via Europeanas API. BHL:s material hämtas från deras API.

# Applikationens beståndsdelar - Google Docs
  
[Schematisk bild](https://docs.google.com/document/d/11tlq-4DHs-rW8pJuzJd-9RdyJ-s7xIky-cCwYn3KQbs/edit?usp=sharing)
[DB - Relationsschema](https://docs.google.com/document/d/1AchY1QlCHjMJzbH1u3dovml0MAMJ-ahXVn59H_Tw07Y/edit?usp=sharing)

# Säkerhet

För att förhindra SQL-injektion (det vill säga att opålitlig data lurar programtolken till att utföra kommandon) 
har jag använt antingen parametriserade sökfrågor eller escape-sekvenser (i filen DBDAO.php) [1, s. 6-7].  
Indata från användaren (till exempel en titel användaren vill söka efter) valideras och saneras med hjälp av PHP-metoden 
filter_var (i filen MainView.php), innan den skickas vidare till API:erna [2]. Eftersom data från API:erna skulle kunna 
vara opålitlig så har jag använt PHP-metoden htmlspecialchars för att sanera datan innan den skrivs ut (i filerna 
Book.php, BHLBook.php och GABook.php) [3]. Detta förhindrar eventuella XSS-brister, vilka angripare skulle kunna 
utnyttja för att exekvera skript i offrets webbläsare och på så sätt förvanska webbplatsen eller dirigera om användaren 
till skadliga webbplatser [1, s. 6]. Om API:erna inte skulle returnera de förväntade resultaten visas ett felmeddelande.

# Prestanda

Jag använder CDN-tjänster för Bootstrap och Jquery, vilket medför att Javascript-kod och CSS-kod ligger i externa filer, 
och filerna som läses in är minifierade.  
En webbsidas svarstid påverkas av avståndet mellan användaren och webbservern. Genom att använda en CDN-tjänst, det vill 
säga en samling webbservrar som är utspridda på flera platser, kan svarstider för http-anrop till komponenter förbättras 
[4, s. 18-19].  
Javascript-filer och CSS-filer cachas i allmänhet, medan HTML-dokument som har dynamiskt innehåll i allmänhet inte 
cachas. Om CSS och Javascript lyfts ut från HTML-dokumentet blir det mindre och går därför snabbare att läsa in 
[4, s. 56].  
Minifiering innebär att ta bort onödiga tecken från kod för att minska dess storlek, och därmed förkorta laddningstider. 
Alla kommentarer och blanktecken tas bort [4, s. 69].  
Jag har aktiverat [komprimering med Gzip på servern](http://salscode.com/tutorials/2009/10/15/gzip-htaccess/). Med hjälp 
av komprimeringsprogrammet Gzip kan storleken på http-svar minskas, vilket leder till kortare svarstider [4, s. 29-30].  
I många webbläsare förhindras progressiv rendering om man placerar CSS längst ner i ett HTML-dokument. Webbläsare 
blockerar rendering för att inte behöva rita om sidelement om deras stilar ändras, och visar därför inga synliga 
komponenter förrän CSS-filen längst ner har laddats. Detta kan leda till att en användare endast ser en tom vit skärm, 
vilket ger intrycket av att sidan laddas långsamt. Därför är det bäst att placera CSS-filer längst upp på en HTML-sida 
[4, s. 38-41], vilket jag har gjort.  
Om man placerar Javascript längst upp på en HTML-sida kan innehåll nedanför skriptet inte renderas och komponenter 
nedanför skriptet inte laddas ner förrän skriptet är helt laddat. Därför är det bäst att placera Javascript 
längst ner på sidan [4, s. 49], vilket jag har gjort.

## Cachning

[DB - Relationsschema](https://docs.google.com/document/d/1AchY1QlCHjMJzbH1u3dovml0MAMJ-ahXVn59H_Tw07Y/edit?usp=sharing)
När sökresultat hämtas från API:erna cachas de i en MySQL-databas. Resultaten har en 1 till många-relation till en titel 
som användaren sökt på, det vill säga en titel kan ha många resultat men ett resultat kan bara höra till en titel. Om 
samma sökning görs igen kontrolleras det om titeln finns i databasen, och om det har gått mer än 5 minuter sedan titeln 
och dess resultat sparades. Om resultaten i databasen är gamla, hämtas nya resultat från API:erna. De gamla resultaten 
som hör till titeln raderas, de nya resultaten sparas och tidpunkten när titeln sparades senast uppdateras.  
För tillfället blir resultaten gamla redan efter 5 minuter eftersom det underlättar testning, men eftersom sökresultaten 
från API:erna troligen inte ändras väldigt ofta skulle man nog kunna cacha resultaten i till exempel 24 timmar. Jag har 
inte kunnat hitta något i API:ernas villkor om hur länge resultat får cachas.  
Sökresultat från API:erna sparas endast om sökningen gjordes på en titel utan att en författare, ett år eller ett språk 
angavs. På detta sätt sparas alla resultat för titeln i databasen och kan sedan filtreras på författare, år och språk.  
Om en användare söker på endast författare utan att ange någon titel hämtas resultaten direkt från API:et, och sparas ej 
i databasen.  

# Offline-first



# Risker

Jag tror inte det finns några etiska risker med min applikation eftersom den inte har ett kommersiellt syfte, och jag 
använder API:erna så som det är tänkt att de ska användas och följer deras villkor. Applikationen underlättar inte 
åtkomst till några personliga data, endast till böcker, varav de flesta är allmän egendom (public domain).  
Applikationen är förstås helt beroende av API:ernas data, så om de går ner och cachen är tom blir applikationen 
meningslös.  
Eftersom jag har hållit mina API-nycklar för mig själv finns det ingen risk att någon har stulit dem och använder dem på 
ett olovligt sätt.  
Se stycke "Säkerhet" ovan för säkerhetsrisker.

# Egna reflektioner

Det hade troligen varit lättare att skriva mer skalbar kod om resultaten från API:erna hade varit mer standardiserade, 
så att jag hade kunnit spara alla resultat som ett Book-objekt i stället för antingen ett BHLBook-objekt eller ett 
GABook-objekt. En skillnad är till exempel att BHL skickar med information om upplaga och förlag, medan Europeana inte 
gör det. Jag hade kunnat standardisera på egen hand och utelämna en del information, men jag tyckte inte det var så 
användarvänligt eftersom en användare troligen vill se den information hen skulle ha sett om sökningen hade gjorts 
direkt på BHL:s eller Gallica:s sida.  
Ett annat problem jag stötte på är att BHL inte returnerar språket på boken i det svar jag får, utan jag skulle 
i så fall få göra ett nytt anrop för varje bok för att få tillbaka språket. Om jag skulle få tillbaka ett svar med 10 
böcker skulle jag alltså få göra 10 extra anrop för att få reda på språken. Eftersom jag tyckte att det skulle påfresta 
API:et lite väl mycket så gör jag inga extra anrop och får därför inte reda på språket, vilket gör att jag inte kan 
spara bokens språk i databasen. Filtrering på språk fungerar därför inte på böcker från BHL om resultaten kommer från 
cachen.  
Det blir antagligen också en del duplicerade sökresultat i databasen om till exempel en sökning görs på "Origin of 
species" och en annan sökning görs på endast "species", men jag har inte lyckats komma på en lösning som undviker detta.  
Övriga funktioner som jag skulle vilja lägga till är att inkludera sökresultat från Google Books, och att ha fler 
alternativ för avancerad sökning. Jag skulle också kunna lägga till en möjlighet att logga in på sitt Google-konto eller 
sitt Europeana-konto via min applikation.

## Referenser

Nr  | Referens
--- | :------
[1] | OWASP, "OWASP Top 10 - 2013 - The ten most critical web application security risks", *OWASP*, september 2015 [Online] Tillgänglig: <https://www.owasp.org/index.php/Top10#OWASP_Top_10_for_2013>. [Hämtad: 13 januari, 2016].
[2] | J. Topjian, "Sanitize and validate data with PHP filters", *Envato Tuts+*, januari 2009 [Online] Tillgänglig: <http://code.tutsplus.com/tutorials/sanitize-and-validate-data-with-php-filters--net-2595> [Hämtad: 13 januari, 2016].
[3] | Virtue Security, "Preventing cross-site scripting in PHP", *Virtue Security*, 2012 [Online] Tillgänglig: <https://www.virtuesecurity.com/blog/preventing-cross-site-scripting-php/> [Hämtad: 13 januari, 2016].
[4] | S. Souders, *High Performance Web Sites: Essential knowledge for frontend engineers*. Sebastopol: O´Reilly Media, Inc., 2007.